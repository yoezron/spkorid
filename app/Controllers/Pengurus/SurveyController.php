<?php

// app/Controllers/Pengurus/SurveyController.php
namespace App\Controllers\Pengurus;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Models\SurveyQuestionModel;
use App\Models\SurveyResponseModel;

class SurveyController extends BaseController
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
     * Create new survey
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Survey - SPK',
            'question_types' => [
                'text' => 'Text Input',
                'textarea' => 'Text Area',
                'radio' => 'Pilihan Tunggal',
                'checkbox' => 'Pilihan Ganda',
                'select' => 'Dropdown',
                'scale' => 'Skala (1-10)',
                'date' => 'Tanggal'
            ]
        ];

        return view('pengurus/survey/create', $data);
    }

    /**
     * Store survey with questions
     */
    public function store()
    {
        $rules = [
            'title' => 'required|min_length[5]',
            'description' => 'required',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'questions' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Create survey
            $surveyData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_active' => 1,
                'created_by' => session()->get('user_id')
            ];

            $surveyId = $this->surveyModel->insert($surveyData);

            // Add questions
            $questions = $this->request->getPost('questions');
            foreach ($questions as $index => $question) {
                $questionData = [
                    'survey_id' => $surveyId,
                    'question_text' => $question['text'],
                    'question_type' => $question['type'],
                    'options' => isset($question['options']) ? json_encode($question['options']) : null,
                    'is_required' => isset($question['required']) ? 1 : 0,
                    'order_index' => $index + 1
                ];

                $this->questionModel->insert($questionData);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal membuat survey');
            }

            // Send notification to all members
            $this->notifyMembers($surveyId);

            return redirect()->to('/pengurus/survey')
                ->with('success', 'Survey berhasil dibuat dan dikirim ke anggota');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * View survey results
     */
    public function results($surveyId)
    {
        $survey = $this->surveyModel->find($surveyId);

        if (!$survey) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Survey tidak ditemukan');
        }

        $data = [
            'title' => 'Hasil Survey: ' . $survey['title'],
            'survey' => $survey,
            'questions' => $this->questionModel->where('survey_id', $surveyId)->findAll(),
            'responses' => $this->responseModel->getSurveyResponses($surveyId),
            'statistics' => $this->responseModel->getSurveyStatistics($surveyId)
        ];

        return view('pengurus/survey/results', $data);
    }

    /**
     * Export survey results to Excel
     */
    public function export($surveyId)
    {
        // Implementation for Excel export
    }
}
