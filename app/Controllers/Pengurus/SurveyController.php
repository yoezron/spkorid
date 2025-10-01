<?php

namespace App\Controllers\Pengurus;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Models\SurveyQuestionModel;
use App\Models\SurveyResponseModel;
use App\Models\SurveyAnswerModel;
use App\Models\MemberModel;
use App\Models\NotificationModel;

class SurveyController extends BaseController
{
    protected $surveyModel;
    protected $questionModel;
    protected $responseModel;
    protected $answerModel;
    protected $memberModel;
    protected $notificationModel;
    protected $db;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->questionModel = new SurveyQuestionModel();
        $this->responseModel = new SurveyResponseModel();
        $this->answerModel = new SurveyAnswerModel();
        $this->memberModel = new MemberModel();
        // $this->notificationModel = new NotificationModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Dashboard survei untuk pengurus
     */
    public function index()
    {
        $userId = session()->get('user_id');

        // Get surveys created by this user
        $surveys = $this->surveyModel->getSurveysByCreator($userId);

        // Add response count and status for each survey
        foreach ($surveys as &$survey) {
            $survey['response_count'] = $this->responseModel
                ->where('survey_id', $survey['id'])
                ->where('is_complete', 1)
                ->countAllResults();

            $survey['partial_count'] = $this->responseModel
                ->where('survey_id', $survey['id'])
                ->where('is_complete', 0)
                ->countAllResults();

            // Determine status
            $now = date('Y-m-d H:i:s');
            if (!$survey['is_active']) {
                $survey['status'] = 'inactive';
                $survey['status_text'] = 'Nonaktif';
                $survey['status_color'] = 'secondary';
            } elseif ($survey['end_date'] < $now) {
                $survey['status'] = 'expired';
                $survey['status_text'] = 'Selesai';
                $survey['status_color'] = 'danger';
            } elseif ($survey['start_date'] > $now) {
                $survey['status'] = 'upcoming';
                $survey['status_text'] = 'Akan Datang';
                $survey['status_color'] = 'warning';
            } else {
                $survey['status'] = 'active';
                $survey['status_text'] = 'Aktif';
                $survey['status_color'] = 'success';
            }
        }

        // Get summary statistics
        $summary = $this->responseModel->getResponseSummary($userId);

        $data = [
            'title' => 'Manajemen Survei - SPK',
            'surveys' => $surveys,
            'summary' => $summary
        ];

        return view('pengurus/survey/index', $data);
    }

    /**
     * Form untuk membuat survei baru
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
        // Validation rules
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

        // Validate date range
        $startDate = strtotime($this->request->getPost('start_date'));
        $endDate = strtotime($this->request->getPost('end_date'));

        if ($startDate >= $endDate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal berakhir harus setelah tanggal mulai');
        }

        $this->db->transStart();

        try {
            // Prepare survey data
            $surveyData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_anonymous' => $this->request->getPost('is_anonymous') ? 1 : 0,
                'allow_multiple_submissions' => $this->request->getPost('allow_multiple') ? 1 : 0,
                'require_login' => 1, // Always require login for member surveys
                'show_results_to_participants' => $this->request->getPost('show_results') ? 1 : 0,
                'randomize_questions' => $this->request->getPost('randomize') ? 1 : 0,
                'is_active' => 1,
                'created_by' => session()->get('user_id')
            ];

            $surveyId = $this->surveyModel->insert($surveyData);

            if (!$surveyId) {
                throw new \Exception('Gagal membuat survei');
            }

            // Process and insert questions
            $questions = $this->request->getPost('questions');

            if (!is_array($questions) || empty($questions)) {
                throw new \Exception('Survei harus memiliki minimal 1 pertanyaan');
            }

            foreach ($questions as $index => $question) {
                if (empty($question['text'])) continue;

                $questionData = [
                    'survey_id' => $surveyId,
                    'question_text' => $question['text'],
                    'question_type' => $question['type'] ?? 'text',
                    'is_required' => isset($question['required']) ? 1 : 0,
                    'order_number' => $index + 1,
                    'placeholder' => $question['placeholder'] ?? null,
                    'help_text' => $question['help_text'] ?? null
                ];

                // Handle options for choice questions
                if (in_array($question['type'], ['radio', 'checkbox', 'dropdown'])) {
                    if (!empty($question['options'])) {
                        $options = array_filter($question['options'], function ($opt) {
                            return !empty(trim($opt));
                        });
                        $questionData['options'] = json_encode(array_values($options));
                    }
                }

                // Handle number/scale constraints
                if (in_array($question['type'], ['number', 'rating', 'scale'])) {
                    $questionData['min_value'] = $question['min_value'] ?? null;
                    $questionData['max_value'] = $question['max_value'] ?? null;
                }

                // Handle text length constraints
                if (in_array($question['type'], ['text', 'textarea'])) {
                    $questionData['min_length'] = $question['min_length'] ?? null;
                    $questionData['max_length'] = $question['max_length'] ?? null;
                }

                $this->questionModel->insert($questionData);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            // Log activity
            $this->logActivity('create_survey', 'Membuat survei: ' . $surveyData['title']);

            // Send notification to members if requested
            if ($this->request->getPost('notify_members')) {
                $this->notifyMembers($surveyId);
            }

            return redirect()->to('/pengurus/surveys')
                ->with('success', 'Survei berhasil dibuat dan akan aktif pada tanggal yang ditentukan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating survey: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Form edit survei
     */
    public function edit($id)
    {
        $survey = $this->surveyModel->getSurveyWithQuestions($id);

        if (!$survey) {
            return redirect()->to('/pengurus/surveys')
                ->with('error', 'Survei tidak ditemukan');
        }

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->to('/pengurus/surveys')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit survei ini');
        }

        // Check if has responses
        $hasResponses = $this->responseModel
            ->where('survey_id', $id)
            ->where('is_complete', 1)
            ->countAllResults() > 0;

        $data = [
            'title' => 'Edit Survei - ' . $survey['title'],
            'survey' => $survey,
            'question_types' => SurveyQuestionModel::getQuestionTypes(),
            'has_responses' => $hasResponses
        ];

        return view('pengurus/survey/edit', $data);
    }

    /**
     * Update survei
     */
    public function update($id)
    {
        $survey = $this->surveyModel->find($id);

        if (!$survey) {
            return redirect()->to('/pengurus/surveys')
                ->with('error', 'Survei tidak ditemukan');
        }

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->to('/pengurus/surveys')
                ->with('error', 'Anda tidak memiliki akses untuk mengupdate survei ini');
        }

        // Check if has responses
        $hasResponses = $this->responseModel
            ->where('survey_id', $id)
            ->where('is_complete', 1)
            ->countAllResults() > 0;

        if ($hasResponses) {
            // Only allow updating basic info if has responses
            $updateData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'end_date' => $this->request->getPost('end_date')
            ];

            $this->surveyModel->update($id, $updateData);

            $this->logActivity('update_survey', 'Mengupdate info survei: ' . $updateData['title']);

            return redirect()->to('/pengurus/surveys')
                ->with('info', 'Survei yang sudah memiliki responden hanya bisa diubah judul, deskripsi, dan tanggal berakhirnya');
        }

        // Full update if no responses
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'description' => 'required|min_length[10]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Update survey data
            $surveyData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_anonymous' => $this->request->getPost('is_anonymous') ? 1 : 0,
                'allow_multiple_submissions' => $this->request->getPost('allow_multiple') ? 1 : 0,
                'show_results_to_participants' => $this->request->getPost('show_results') ? 1 : 0,
                'randomize_questions' => $this->request->getPost('randomize') ? 1 : 0
            ];

            $this->surveyModel->update($id, $surveyData);

            // Handle questions update
            // ... (similar logic to admin controller for updating questions)

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal mengupdate survei');
            }

            $this->logActivity('update_survey', 'Mengupdate survei: ' . $surveyData['title']);

            return redirect()->to('/pengurus/surveys')
                ->with('success', 'Survei berhasil diupdate');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error updating survey: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Lihat hasil survei
     */
    public function results($id)
    {
        $survey = $this->surveyModel->getSurveyWithQuestions($id);

        if (!$survey) {
            return redirect()->to('/pengurus/surveys')
                ->with('error', 'Survei tidak ditemukan');
        }

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->to('/pengurus/surveys')
                ->with('error', 'Anda tidak memiliki akses untuk melihat hasil survei ini');
        }

        // Get statistics
        $statistics = $this->surveyModel->getSurveyStatistics($id);

        // Get responses
        $responses = $this->responseModel->getFullResponses($id);

        // Get response timeline
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
     * Lihat detail response individual
     */
    public function viewResponse($surveyId, $responseId)
    {
        $survey = $this->surveyModel->find($surveyId);

        if (!$survey) {
            return redirect()->back()->with('error', 'Survei tidak ditemukan');
        }

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $response = $this->responseModel->getResponseWithAnswers($responseId);

        if (!$response || $response['survey_id'] != $surveyId) {
            return redirect()->back()->with('error', 'Response tidak ditemukan');
        }

        $member = $this->memberModel->find($response['member_id']);

        $data = [
            'title' => 'Detail Response - ' . $survey['title'],
            'survey' => $survey,
            'response' => $response,
            'member' => $member
        ];

        return view('pengurus/survey/response_detail', $data);
    }

    /**
     * Export hasil survei ke CSV
     */
    public function export($id)
    {
        $survey = $this->surveyModel->find($id);

        if (!$survey) {
            return redirect()->back()->with('error', 'Survei tidak ditemukan');
        }

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        try {
            $exportData = $this->responseModel->exportResponses($id);

            // Generate CSV
            $filename = 'survei_' . $id . '_' . date('Y-m-d_His') . '.csv';

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
        } catch (\Exception $e) {
            log_message('error', 'Error exporting survey: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengexport data');
        }
    }

    /**
     * Toggle status survei (aktif/nonaktif)
     */
    public function toggleStatus($id)
    {
        $survey = $this->surveyModel->find($id);

        if (!$survey) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Survei tidak ditemukan'
            ]);
        }

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ]);
        }

        $newStatus = $survey['is_active'] ? 0 : 1;
        $this->surveyModel->update($id, ['is_active' => $newStatus]);

        $this->logActivity(
            'toggle_survey_status',
            'Mengubah status survei "' . $survey['title'] . '" menjadi ' .
                ($newStatus ? 'Aktif' : 'Nonaktif')
        );

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

        if (!$survey) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Survei tidak ditemukan'
            ]);
        }

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
            $this->logActivity('delete_survey', 'Menghapus survei: ' . $survey['title']);

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
     * Clone survei
     */
    public function clone($id)
    {
        $survey = $this->surveyModel->find($id);

        if (!$survey) {
            return redirect()->back()->with('error', 'Survei tidak ditemukan');
        }

        // Check ownership
        if ($survey['created_by'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $newSurveyId = $this->surveyModel->cloneSurvey($id);

        if ($newSurveyId) {
            $this->logActivity('clone_survey', 'Menduplikasi survei: ' . $survey['title']);

            return redirect()->to('/pengurus/surveys/edit/' . $newSurveyId)
                ->with('success', 'Survei berhasil diduplikasi. Silakan sesuaikan pengaturan survei baru.');
        } else {
            return redirect()->back()
                ->with('error', 'Gagal menduplikasi survei');
        }
    }

    /**
     * Data survei untuk menu khusus pengurus
     */
    public function surveyData()
    {
        $userId = session()->get('user_id');

        // Get all surveys created by this user
        $surveys = $this->surveyModel->getSurveysByCreator($userId);

        // Calculate statistics for each survey
        $totalResponses = 0;
        $activeCount = 0;
        $completedCount = 0;

        foreach ($surveys as &$survey) {
            $survey['responses'] = $this->responseModel
                ->where('survey_id', $survey['id'])
                ->where('is_complete', 1)
                ->countAllResults();

            $totalResponses += $survey['responses'];

            $now = date('Y-m-d H:i:s');
            if ($survey['is_active'] && $survey['start_date'] <= $now && $survey['end_date'] >= $now) {
                $activeCount++;
            }
            if ($survey['end_date'] < $now) {
                $completedCount++;
            }
        }

        $data = [
            'title' => 'Data Survei - SPK',
            'surveys' => $surveys,
            'total_surveys' => count($surveys),
            'active_surveys' => $activeCount,
            'completed_surveys' => $completedCount,
            'total_responses' => $totalResponses
        ];

        return view('pengurus/survey/data', $data);
    }

    /**
     * Kirim notifikasi ke anggota
     */
    private function notifyMembers($surveyId)
    {
        $survey = $this->surveyModel->find($surveyId);
        $members = $this->memberModel
            ->where('status', 'active')
            ->findAll();

        $notificationCount = 0;

        foreach ($members as $member) {
            // Create notification
            $notificationData = [
                'user_id' => $member['user_id'] ?? null,
                'type' => 'new_survey',
                'title' => 'Survei Baru: ' . $survey['title'],
                'message' => 'Ada survei baru yang perlu Anda isi. ' . $survey['description'],
                'data' => json_encode([
                    'survey_id' => $surveyId,
                    'start_date' => $survey['start_date'],
                    'end_date' => $survey['end_date']
                ]),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->db->table('notifications')->insert($notificationData)) {
                $notificationCount++;
            }

            // Send email if enabled (optional)
            if (!empty($member['email']) && $this->request->getPost('send_email')) {
                $this->sendSurveyEmail($member, $survey);
            }
        }

        log_message('info', "Sent {$notificationCount} notifications for survey ID: {$surveyId}");
    }

    /**
     * Send email notification
     */
    private function sendSurveyEmail($member, $survey)
    {
        $email = \Config\Services::email();

        $email->setTo($member['email']);
        $email->setSubject('Survei Baru SPK: ' . $survey['title']);

        $message = view('emails/new_survey_notification', [
            'member' => $member,
            'survey' => $survey,
            'survey_url' => base_url('member/surveys/take/' . $survey['id'])
        ]);

        $email->setMessage($message);

        if (!$email->send()) {
            log_message('error', 'Failed to send survey email to: ' . $member['email']);
        }
    }

    /**
     * Log activity
     */
    protected function logActivity($action, $description)
    {
        $activityData = [
            'user_id' => session()->get('user_id'),
            'action' => $action,
            'description' => $description,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('activity_logs')->insert($activityData);
    }
}
