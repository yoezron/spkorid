<?php

// app/Controllers/Admin/SurveyManagementController.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Models\SurveyQuestionModel;
use App\Models\SurveyResponseModel;

class SurveyManagementController extends BaseController
{
    protected $surveyModel;
    protected $questionModel;
    protected $responseModel;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->questionModel = new SurveyQuestionModel();
        $this->responseModel = new SurveyResponseModel();
    }

    /**
     * List all surveys
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Survei - SPK',
            'surveys' => $this->surveyModel->findAll()
        ];

        return view('admin/survey/index', $data);
    }

    /**
     * Create new survey
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Survei Baru - SPK'
        ];

        return view('admin/survey/create', $data);
    }

    /**
     * Store new survey
     */
    public function store()
    {
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'description' => 'required',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $surveyData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'is_anonymous' => $this->request->getPost('is_anonymous') ? 1 : 0,
            'created_by' => session()->get('user_id')
        ];

        $surveyId = $this->surveyModel->insert($surveyData);

        // Add questions
        $questions = $this->request->getPost('questions');
        if ($questions && is_array($questions)) {
            foreach ($questions as $index => $question) {
                $questionData = [
                    'survey_id' => $surveyId,
                    'question_text' => $question['text'],
                    'question_type' => $question['type'],
                    'options' => isset($question['options']) ? json_encode($question['options']) : null,
                    'is_required' => isset($question['required']) ? 1 : 0,
                    'order_number' => $index + 1
                ];
                $this->questionModel->insert($questionData);
            }
        }

        return redirect()->to('/pengurus/surveys')->with('success', 'Survei berhasil dibuat');
    }

    /**
     * View survey results
     */
    public function results($id)
    {
        $survey = $this->surveyModel->find($id);

        if (!$survey) {
            return redirect()->back()->with('error', 'Survei tidak ditemukan');
        }

        $data = [
            'title' => 'Hasil Survei: ' . $survey['title'],
            'survey' => $survey,
            'statistics' => $this->surveyModel->getSurveyStatistics($id),
            'responses' => $this->responseModel->getResponsesWithMembers($id)
        ];

        return view('admin/survey/results', $data);
    }

    /**
     * Export survey results
     */
    public function export($id)
    {
        $survey = $this->surveyModel->getSurveyWithQuestions($id);
        $responses = $this->responseModel->getResponsesWithMembers($id);

        // Create CSV
        $filename = 'survey_' . $id . '_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header row
        $headers = ['Responden', 'Tanggal Submit'];
        foreach ($survey['questions'] as $question) {
            $headers[] = $question['question_text'];
        }
        fputcsv($output, $headers);

        // Data rows
        foreach ($responses as $response) {
            $row = [
                $survey['is_anonymous'] ? 'Anonymous' : $response['nama_lengkap'],
                $response['submitted_at']
            ];

            // Get answers for this response
            $db = \Config\Database::connect();
            $answers = $db->table('survey_answers')
                ->where('response_id', $response['id'])
                ->get()
                ->getResultArray();

            $answerMap = [];
            foreach ($answers as $answer) {
                $answerMap[$answer['question_id']] = $answer['answer_text'];
            }

            foreach ($survey['questions'] as $question) {
                $row[] = $answerMap[$question['id']] ?? '';
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Toggle survey status
     */
    public function toggleStatus($id)
    {
        $survey = $this->surveyModel->find($id);

        $this->surveyModel->update($id, [
            'is_active' => !$survey['is_active']
        ]);

        return redirect()->back()->with('success', 'Status survei berhasil diubah');
    }
}
