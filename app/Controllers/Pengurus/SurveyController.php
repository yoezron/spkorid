<?php

namespace App\Controllers\Pengurus;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Models\SurveyQuestionModel;
use App\Models\SurveyResponseModel;
use App\Models\SurveyAnswerModel;
use App\Models\MemberModel;

class SurveyController extends BaseController
{
    protected $surveyModel;
    protected $questionModel;
    protected $responseModel;
    protected $answerModel;
    protected $memberModel;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->questionModel = new SurveyQuestionModel();
        $this->responseModel = new SurveyResponseModel();
        $this->answerModel = new SurveyAnswerModel();
        $this->memberModel = new MemberModel();
    }

    /**
     * Daftar survei yang dibuat pengurus
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $surveys = $this->surveyModel->getSurveysByCreator($userId);

        // Add response count
        foreach ($surveys as &$survey) {
            $survey['response_count'] = $this->responseModel
                ->where('survey_id', $survey['id'])
                ->where('is_complete', 1)
                ->countAllResults();
        }

        $data = [
            'title' => 'Survei Saya - SPK',
            'surveys' => $surveys
        ];

        return view('pengurus/survey/index', $data);
    }

    /**
     * Form buat survei
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Survei Baru - SPK',
            'question_types' => SurveyQuestionModel::getQuestionTypes()
        ];

        return view('pengurus/survey/create', $data);
    }

    /**
     * Simpan survei baru
     */
    public function store()
    {
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'description' => 'required|min_length[10]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'questions' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create survey
            $surveyData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_anonymous' => $this->request->getPost('is_anonymous') ? 1 : 0,
                'is_active' => 1,
                'created_by' => session()->get('user_id')
            ];

            $surveyId = $this->surveyModel->insert($surveyData);

            // Add questions
            $questions = $this->request->getPost('questions');
            foreach ($questions as $index => $question) {
                if (empty($question['text'])) continue;

                $questionData = [
                    'survey_id' => $surveyId,
                    'question_text' => $question['text'],
                    'question_type' => $question['type'],
                    'is_required' => isset($question['required']) ? 1 : 0,
                    'order_number' => $index + 1
                ];

                // Handle options
                if (in_array($question['type'], ['radio', 'checkbox', 'dropdown'])) {
                    if (!empty($question['options'])) {
                        $options = array_filter($question['options'], function ($opt) {
                            return !empty(trim($opt));
                        });
                        $questionData['options'] = json_encode(array_values($options));
                    }
                }

                $this->questionModel->insert($questionData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal membuat survei');
            }

            // Notify members
            $this->notifyMembers($surveyId);

            return redirect()->to('/pengurus/survey')
                ->with('success', 'Survei berhasil dibuat dan dikirim ke anggota');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Edit survei
     */
    public function edit($id)
    {
        $survey = $this->surveyModel->getSurveyWithQuestions($id);

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->to('/pengurus/survey')
                ->with('error', 'Anda tidak memiliki akses ke survei ini');
        }

        $data = [
            'title' => 'Edit Survei - ' . $survey['title'],
            'survey' => $survey,
            'question_types' => SurveyQuestionModel::getQuestionTypes()
        ];

        return view('pengurus/survey/edit', $data);
    }

    /**
     * Update survei
     */
    public function update($id)
    {
        $survey = $this->surveyModel->find($id);

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->to('/pengurus/survey')
                ->with('error', 'Anda tidak memiliki akses ke survei ini');
        }

        // Check if has responses
        $hasResponses = $this->responseModel
            ->where('survey_id', $id)
            ->countAllResults() > 0;

        if ($hasResponses) {
            // Only allow updating title, description, and dates
            $surveyData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'end_date' => $this->request->getPost('end_date')
            ];

            $this->surveyModel->update($id, $surveyData);

            return redirect()->to('/pengurus/survey')
                ->with('info', 'Survei yang sudah memiliki responden hanya bisa diubah judul, deskripsi, dan tanggal berakhirnya');
        }

        // Full update if no responses
        return $this->fullUpdate($id);
    }

    /**
     * Full update untuk survei tanpa responden
     */
    private function fullUpdate($id)
    {
        // Similar to admin update but with ownership check
        // [Implementation similar to Admin controller update method]
        // ...
    }

    /**
     * Lihat hasil survei
     */
    public function results($id)
    {
        $survey = $this->surveyModel->getSurveyWithQuestions($id);

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->to('/pengurus/survey')
                ->with('error', 'Anda tidak memiliki akses ke survei ini');
        }

        $statistics = $this->surveyModel->getSurveyStatistics($id);
        $responses = $this->responseModel->getFullResponses($id);
        $responseStats = $this->responseModel->getResponseStatistics($id);

        // Get answer distribution for each question
        foreach ($survey['questions'] as &$question) {
            $question['distribution'] = $this->answerModel->getAnswerDistribution($question['id']);
        }

        $data = [
            'title' => 'Hasil Survei: ' . $survey['title'],
            'survey' => $survey,
            'statistics' => $statistics,
            'responses' => $responses,
            'response_stats' => $responseStats
        ];

        return view('pengurus/survey/results', $data);
    }

    /**
     * Export hasil survei ke CSV
     */
    public function export($id)
    {
        $survey = $this->surveyModel->find($id);

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $exportData = $this->responseModel->exportResponses($id);

        // Generate CSV
        $filename = 'survey_' . $id . '_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Add BOM for Excel UTF-8 compatibility
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        foreach ($exportData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Toggle status survei
     */
    public function toggleStatus($id)
    {
        $survey = $this->surveyModel->find($id);

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ]);
        }

        $newStatus = $survey['is_active'] ? 0 : 1;
        $this->surveyModel->update($id, ['is_active' => $newStatus]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status survei berhasil diubah',
            'is_active' => $newStatus
        ]);
    }

    /**
     * Hapus survei
     */
    public function delete($id)
    {
        $survey = $this->surveyModel->find($id);

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ]);
        }

        // Check if has responses
        $responseCount = $this->responseModel
            ->where('survey_id', $id)
            ->countAllResults();

        if ($responseCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak dapat menghapus survei yang sudah memiliki responden'
            ]);
        }

        try {
            $this->surveyModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Survei berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus survei'
            ]);
        }
    }

    /**
     * Data survei untuk dashboard
     */
    public function surveyData()
    {
        $userId = session()->get('user_id');
        $surveys = $this->surveyModel->getSurveysByCreator($userId);

        $data = [
            'title' => 'Data Survei - SPK',
            'surveys' => $surveys,
            'total_surveys' => count($surveys),
            'active_surveys' => count(array_filter($surveys, function ($s) {
                return $s['is_active'] == 1;
            })),
            'total_responses' => array_sum(array_map(function ($s) {
                return $this->responseModel
                    ->where('survey_id', $s['id'])
                    ->countAllResults();
            }, $surveys))
        ];

        return view('pengurus/survey/data', $data);
    }

    /**
     * Kirim notifikasi ke anggota
     */
    private function notifyMembers($surveyId)
    {
        $survey = $this->surveyModel->find($surveyId);
        $members = $this->memberModel->where('status', 'active')->findAll();

        $db = \Config\Database::connect();
        foreach ($members as $member) {
            // Create notification
            $notificationData = [
                'user_id' => $member['user_id'] ?? null,
                'type' => 'new_survey',
                'title' => 'Survei Baru: ' . $survey['title'],
                'message' => 'Silakan isi survei terbaru dari pengurus SPK.',
                'data' => json_encode(['survey_id' => $surveyId]),
                'is_read' => 0
            ];

            $db->table('notifications')->insert($notificationData);
        }
    }
}
