<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Models\SurveyQuestionModel;
use App\Models\SurveyResponseModel;
use App\Models\SurveyAnswerModel;
use App\Models\MemberModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SurveyManagementController extends BaseController
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
     * Dashboard survei - menampilkan semua survei
     */
    public function index()
    {
        $surveys = $this->surveyModel->orderBy('created_at', 'DESC')->findAll();

        // Add response count for each survey
        foreach ($surveys as &$survey) {
            $survey['response_count'] = $this->responseModel
                ->where('survey_id', $survey['id'])
                ->where('is_complete', 1)
                ->countAllResults();
        }

        $data = [
            'title' => 'Manajemen Survei - SPK',
            'surveys' => $surveys,
            'summary' => $this->surveyModel->getSurveySummary()
        ];

        return view('admin/survey/index', $data);
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

        return view('admin/survey/create', $data);
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
            // Prepare survey data
            $surveyData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_anonymous' => $this->request->getPost('is_anonymous') ? 1 : 0,
                'allow_multiple_submissions' => $this->request->getPost('allow_multiple') ? 1 : 0,
                'require_login' => $this->request->getPost('require_login') ? 1 : 0,
                'show_results_to_participants' => $this->request->getPost('show_results') ? 1 : 0,
                'randomize_questions' => $this->request->getPost('randomize') ? 1 : 0,
                'is_active' => 1,
                'created_by' => session()->get('user_id')
            ];

            // Insert survey
            $surveyId = $this->surveyModel->insert($surveyData);

            if (!$surveyId) {
                throw new \Exception('Gagal membuat survei');
            }

            // Process and insert questions
            $questions = $this->request->getPost('questions');

            if (!is_array($questions) || empty($questions)) {
                throw new \Exception('Pertanyaan survei tidak valid');
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
                        // Filter empty options
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

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            // Log activity
            $this->logActivity('create_survey', 'Membuat survei: ' . $surveyData['title']);

            // Send notification to members if requested
            if ($this->request->getPost('notify_members')) {
                $this->notifyMembers($surveyId);
            }

            return redirect()->to('/admin/surveys')
                ->with('success', 'Survei berhasil dibuat');
        } catch (\Exception $e) {
            $db->transRollback();
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
            return redirect()->to('/admin/surveys')
                ->with('error', 'Survei tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Survei - ' . $survey['title'],
            'survey' => $survey,
            'question_types' => SurveyQuestionModel::getQuestionTypes()
        ];

        return view('admin/survey/edit', $data);
    }

    /**
     * Update survei
     */
    public function update($id)
    {
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

        $survey = $this->surveyModel->find($id);
        if (!$survey) {
            return redirect()->to('/admin/surveys')
                ->with('error', 'Survei tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update survey data
            $surveyData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_anonymous' => $this->request->getPost('is_anonymous') ? 1 : 0,
                'allow_multiple_submissions' => $this->request->getPost('allow_multiple') ? 1 : 0,
                'require_login' => $this->request->getPost('require_login') ? 1 : 0,
                'show_results_to_participants' => $this->request->getPost('show_results') ? 1 : 0,
                'randomize_questions' => $this->request->getPost('randomize') ? 1 : 0
            ];

            $this->surveyModel->update($id, $surveyData);

            // Handle questions
            $existingQuestions = $this->request->getPost('existing_questions');
            $newQuestions = $this->request->getPost('new_questions');
            $deletedQuestions = $this->request->getPost('deleted_questions');

            // Delete removed questions
            if (!empty($deletedQuestions)) {
                foreach ($deletedQuestions as $questionId) {
                    $this->questionModel->delete($questionId);
                }
            }

            // Update existing questions
            if (!empty($existingQuestions)) {
                foreach ($existingQuestions as $questionId => $question) {
                    if (empty($question['text'])) continue;

                    $questionData = [
                        'question_text' => $question['text'],
                        'question_type' => $question['type'],
                        'is_required' => isset($question['required']) ? 1 : 0,
                        'order_number' => $question['order'] ?? 0,
                        'placeholder' => $question['placeholder'] ?? null,
                        'help_text' => $question['help_text'] ?? null
                    ];

                    // Handle options
                    if (in_array($question['type'], ['radio', 'checkbox', 'dropdown'])) {
                        $options = array_filter($question['options'] ?? [], function ($opt) {
                            return !empty(trim($opt));
                        });
                        $questionData['options'] = json_encode(array_values($options));
                    }

                    // Handle constraints
                    if (in_array($question['type'], ['number', 'rating', 'scale'])) {
                        $questionData['min_value'] = $question['min_value'] ?? null;
                        $questionData['max_value'] = $question['max_value'] ?? null;
                    }

                    if (in_array($question['type'], ['text', 'textarea'])) {
                        $questionData['min_length'] = $question['min_length'] ?? null;
                        $questionData['max_length'] = $question['max_length'] ?? null;
                    }

                    $this->questionModel->update($questionId, $questionData);
                }
            }

            // Add new questions
            if (!empty($newQuestions)) {
                foreach ($newQuestions as $index => $question) {
                    if (empty($question['text'])) continue;

                    $questionData = [
                        'survey_id' => $id,
                        'question_text' => $question['text'],
                        'question_type' => $question['type'] ?? 'text',
                        'is_required' => isset($question['required']) ? 1 : 0,
                        'order_number' => $question['order'] ?? ($index + 100),
                        'placeholder' => $question['placeholder'] ?? null,
                        'help_text' => $question['help_text'] ?? null
                    ];

                    // Handle options and constraints
                    if (in_array($question['type'], ['radio', 'checkbox', 'dropdown'])) {
                        $options = array_filter($question['options'] ?? [], function ($opt) {
                            return !empty(trim($opt));
                        });
                        $questionData['options'] = json_encode(array_values($options));
                    }

                    $this->questionModel->insert($questionData);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal mengupdate survei');
            }

            $this->logActivity('update_survey', 'Mengupdate survei: ' . $surveyData['title']);

            return redirect()->to('/admin/surveys')
                ->with('success', 'Survei berhasil diupdate');
        } catch (\Exception $e) {
            $db->transRollback();
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
            return redirect()->to('/admin/surveys')
                ->with('error', 'Survei tidak ditemukan');
        }

        // Get statistics
        $statistics = $this->surveyModel->getSurveyStatistics($id);

        // Get responses
        $responses = $this->responseModel->getFullResponses($id);

        // Get response timeline
        $responseStats = $this->responseModel->getResponseStatistics($id);

        $data = [
            'title' => 'Hasil Survei: ' . $survey['title'],
            'survey' => $survey,
            'statistics' => $statistics,
            'responses' => $responses,
            'response_stats' => $responseStats
        ];

        return view('admin/survey/results', $data);
    }

    /**
     * Lihat detail response individual
     */
    public function viewResponse($surveyId, $responseId)
    {
        $survey = $this->surveyModel->find($surveyId);
        $response = $this->responseModel->getResponseWithAnswers($responseId);

        if (!$survey || !$response) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $member = $this->memberModel->find($response['member_id']);

        $data = [
            'title' => 'Detail Response - ' . $survey['title'],
            'survey' => $survey,
            'response' => $response,
            'member' => $member
        ];

        return view('admin/survey/response_detail', $data);
    }

    /**
     * Export hasil survei ke Excel
     */
    public function export($id)
    {
        $survey = $this->surveyModel->getSurveyWithQuestions($id);

        if (!$survey) {
            return redirect()->back()->with('error', 'Survei tidak ditemukan');
        }

        try {
            $exportData = $this->responseModel->exportResponses($id);

            // Create spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set title
            $sheet->setTitle('Hasil Survei');

            // Add survey info
            $sheet->setCellValue('A1', 'Judul Survei:');
            $sheet->setCellValue('B1', $survey['title']);
            $sheet->setCellValue('A2', 'Deskripsi:');
            $sheet->setCellValue('B2', $survey['description']);
            $sheet->setCellValue('A3', 'Periode:');
            $sheet->setCellValue('B3', $survey['start_date'] . ' - ' . $survey['end_date']);
            $sheet->setCellValue('A4', 'Total Responden:');
            $sheet->setCellValue('B4', count($exportData) - 1); // Minus header row

            // Add data starting from row 6
            $row = 6;
            foreach ($exportData as $dataRow) {
                $col = 'A';
                foreach ($dataRow as $value) {
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }

            // Style header row
            $headerRow = 6;
            $lastCol = chr(65 + count($exportData[0]) - 1); // Calculate last column letter
            $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)
                ->getFont()->setBold(true);
            $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)
                ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');

            // Auto size columns
            foreach (range('A', $lastCol) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create writer and output
            $writer = new Xlsx($spreadsheet);
            $filename = 'Hasil_Survei_' . $id . '_' . date('Y-m-d_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
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
        $newSurveyId = $this->surveyModel->cloneSurvey($id);

        if ($newSurveyId) {
            $this->logActivity('clone_survey', 'Menduplikasi survei ID: ' . $id);
            return redirect()->to('/admin/surveys/edit/' . $newSurveyId)
                ->with('success', 'Survei berhasil diduplikasi');
        } else {
            return redirect()->back()
                ->with('error', 'Gagal menduplikasi survei');
        }
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
                'message' => 'Ada survei baru yang perlu Anda isi. Silakan login untuk mengisi survei.',
                'data' => json_encode(['survey_id' => $surveyId]),
                'is_read' => 0
            ];

            $db->table('notifications')->insert($notificationData);

            // Send email if enabled
            if (!empty($member['email'])) {
                $this->sendSurveyEmail($member, $survey);
            }
        }
    }

    /**
     * Kirim email survei
     */
    private function sendSurveyEmail($member, $survey)
    {
        $email = \Config\Services::email();

        $email->setTo($member['email']);
        $email->setSubject('Survei Baru: ' . $survey['title']);

        $message = view('emails/new_survey', [
            'member' => $member,
            'survey' => $survey
        ]);

        $email->setMessage($message);

        if (!$email->send()) {
            log_message('error', 'Failed to send survey email to: ' . $member['email']);
        }
    }

    /**
     * Log activity
     */
    private function logActivity($action, $description)
    {
        $activityData = [
            'user_id' => session()->get('user_id'),
            'action' => $action,
            'description' => $description,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->table('activity_logs')->insert($activityData);
    }
}
