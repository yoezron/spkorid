<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Models\SurveyQuestionModel;
use App\Models\SurveyResponseModel;
use App\Models\SurveyAnswerModel;

class SurveyController extends BaseController
{
    protected $surveyModel;
    protected $questionModel;
    protected $responseModel;
    protected $answerModel;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->questionModel = new SurveyQuestionModel();
        $this->responseModel = new SurveyResponseModel();
        $this->answerModel = new SurveyAnswerModel();
    }

    /**
     * Daftar survei yang tersedia untuk anggota
     */
    public function index()
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get available surveys for member
        $surveys = $this->surveyModel->getSurveysForMember($memberId);

        // Separate surveys by status
        $availableSurveys = [];
        $completedSurveys = [];
        $upcomingSurveys = [];

        $now = date('Y-m-d H:i:s');

        foreach ($surveys as $survey) {
            if ($survey['has_responded'] > 0) {
                $completedSurveys[] = $survey;
            } elseif ($survey['start_date'] > $now) {
                $upcomingSurveys[] = $survey;
            } else {
                $availableSurveys[] = $survey;
            }
        }

        $data = [
            'title' => 'Survei Anggota - SPK',
            'available_surveys' => $availableSurveys,
            'completed_surveys' => $completedSurveys,
            'upcoming_surveys' => $upcomingSurveys
        ];

        return view('member/survey/index', $data);
    }

    /**
     * Form isi survei
     */
    public function take($id)
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if already responded (unless multiple submissions allowed)
        $survey = $this->surveyModel->find($id);

        if (!$survey || !$survey['is_active']) {
            return redirect()->to('/member/surveys')
                ->with('error', 'Survei tidak ditemukan atau sudah ditutup');
        }

        // Check date range
        $now = date('Y-m-d H:i:s');
        if ($now < $survey['start_date']) {
            return redirect()->to('/member/surveys')
                ->with('info', 'Survei belum dibuka');
        }

        if ($now > $survey['end_date']) {
            return redirect()->to('/member/surveys')
                ->with('info', 'Survei sudah ditutup');
        }

        // Check if already responded
        if (
            !$survey['allow_multiple_submissions'] &&
            $this->responseModel->hasResponded($id, $memberId)
        ) {
            return redirect()->to('/member/surveys')
                ->with('info', 'Anda sudah mengisi survei ini');
        }

        // Get survey with questions
        $survey = $this->surveyModel->getSurveyWithQuestions($id);

        // Randomize questions if enabled
        if ($survey['randomize_questions']) {
            shuffle($survey['questions']);
        }

        // Check for partial response
        $partialResponse = $this->responseModel->getPartialResponse($id, $memberId);

        // Set start time in session
        if (!$partialResponse) {
            session()->set('survey_start_time_' . $id, time());
        }

        $data = [
            'title' => 'Isi Survei: ' . $survey['title'],
            'survey' => $survey,
            'partial_response' => $partialResponse
        ];

        return view('member/survey/take', $data);
    }

    /**
     * Submit survei
     */
    public function submit($id)
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $survey = $this->surveyModel->find($id);

        if (!$survey || !$survey['is_active']) {
            return redirect()->to('/member/surveys')
                ->with('error', 'Survei tidak valid');
        }

        // Check if already responded
        if (
            !$survey['allow_multiple_submissions'] &&
            $this->responseModel->hasResponded($id, $memberId)
        ) {
            return redirect()->to('/member/surveys')
                ->with('error', 'Anda sudah mengisi survei ini');
        }

        // Get questions
        $questions = $this->questionModel->getQuestionsBySurvey($id);

        // Validate answers
        $errors = [];
        $answers = [];
        $files = [];

        foreach ($questions as $question) {
            $fieldName = 'question_' . $question['id'];
            $answer = $this->request->getPost($fieldName);

            // Handle file upload
            if ($question['question_type'] == 'file') {
                $file = $this->request->getFile($fieldName);
                if ($file && $file->isValid()) {
                    $files[$question['id']] = $file;
                    $answer = 'file_uploaded'; // Placeholder, actual path will be set during save
                } elseif ($question['is_required']) {
                    $errors[] = $question['question_text'] . ' wajib diupload';
                    continue;
                }
            }

            // Validate answer
            $validation = $this->questionModel->validateAnswer($question['id'], $answer);

            if (!$validation['valid']) {
                $errors[] = $question['question_text'] . ': ' . $validation['message'];
            } else {
                if (!empty($answer) || $answer === '0') {
                    $answers[$question['id']] = $answer;
                }
            }
        }

        // If validation errors
        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }

        // Save response
        $responseId = $this->responseModel->saveResponseWithAnswers($id, $memberId, $answers, $files);

        if ($responseId) {
            // Delete partial response if exists
            $partial = $this->responseModel->getPartialResponse($id, $memberId);
            if ($partial) {
                $this->responseModel->delete($partial['id']);
            }

            // Log activity
            $this->logMemberActivity('submit_survey', 'Mengisi survei: ' . $survey['title']);

            // Check if can show results
            if ($survey['show_results_to_participants']) {
                return redirect()->to('/member/surveys/result/' . $id)
                    ->with('success', 'Terima kasih! Survei berhasil dikirim.');
            } else {
                return redirect()->to('/member/surveys')
                    ->with('success', 'Terima kasih! Survei berhasil dikirim.');
            }
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan survei. Silakan coba lagi.');
        }
    }

    /**
     * Simpan partial response (auto-save)
     */
    public function autoSave($id)
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session expired'
            ]);
        }

        $questions = $this->questionModel->getQuestionsBySurvey($id);
        $answers = [];

        foreach ($questions as $question) {
            $fieldName = 'question_' . $question['id'];
            $answer = $this->request->getPost($fieldName);

            if (!empty($answer) || $answer === '0') {
                $answers[$question['id']] = $answer;
            }
        }

        $saved = $this->responseModel->savePartialResponse($id, $memberId, $answers);

        return $this->response->setJSON([
            'success' => $saved,
            'message' => $saved ? 'Progress tersimpan' : 'Gagal menyimpan',
            'timestamp' => date('H:i:s')
        ]);
    }

    /**
     * Lihat hasil survei (jika diizinkan)
     */
    public function result($id)
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $survey = $this->surveyModel->find($id);

        if (!$survey || !$survey['show_results_to_participants']) {
            return redirect()->to('/member/surveys')
                ->with('error', 'Hasil survei tidak tersedia');
        }

        // Check if member has responded
        if (!$this->responseModel->hasResponded($id, $memberId)) {
            return redirect()->to('/member/surveys/take/' . $id)
                ->with('info', 'Silakan isi survei terlebih dahulu');
        }

        // Get survey with questions
        $survey = $this->surveyModel->getSurveyWithQuestions($id);

        // Get statistics for each question
        foreach ($survey['questions'] as &$question) {
            $question['distribution'] = $this->answerModel->getAnswerDistribution($question['id']);
        }

        // Get member's own response
        $memberResponse = $this->responseModel->getMemberResponse($id, $memberId);

        // Get overall statistics
        $statistics = $this->surveyModel->getSurveyStatistics($id);

        $data = [
            'title' => 'Hasil Survei: ' . $survey['title'],
            'survey' => $survey,
            'statistics' => $statistics,
            'member_response' => $memberResponse
        ];

        return view('member/survey/result', $data);
    }

    /**
     * Lihat response sendiri
     */
    public function myResponse($surveyId)
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $survey = $this->surveyModel->getSurveyWithQuestions($surveyId);
        $response = $this->responseModel->getMemberResponse($surveyId, $memberId);

        if (!$survey || !$response) {
            return redirect()->to('/member/surveys')
                ->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Jawaban Saya - ' . $survey['title'],
            'survey' => $survey,
            'response' => $response
        ];

        return view('member/survey/my_response', $data);
    }

    /**
     * Download file attachment dari jawaban
     */
    public function downloadFile($answerId)
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $answer = $this->answerModel->find($answerId);

        if (!$answer || empty($answer['file_path'])) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        // Verify member owns this response
        $response = $this->responseModel->find($answer['response_id']);
        if ($response['member_id'] != $memberId) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $filepath = WRITEPATH . 'uploads/' . $answer['file_path'];

        if (file_exists($filepath)) {
            return $this->response->download($filepath);
        } else {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }
    }

    /**
     * Log aktivitas member
     */
    private function logMemberActivity($action, $description)
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
