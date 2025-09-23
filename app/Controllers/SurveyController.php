<?php
// ============================================
// SURVEY CONTROLLERS
// ============================================

// app/Controllers/SurveyController.php
namespace App\Controllers;

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
     * List active surveys for members
     */
    public function index()
    {
        $memberId = session()->get('member_id');
        $surveys = $this->surveyModel->getActiveSurveys();

        // Check which surveys member has completed
        foreach ($surveys as &$survey) {
            $survey['has_responded'] = $this->surveyModel->hasResponded($survey['id'], $memberId);
        }

        $data = [
            'title' => 'Survei Anggota - SPK',
            'surveys' => $surveys
        ];

        return view('survey/index', $data);
    }

    /**
     * Take survey
     */
    public function take($id)
    {
        $memberId = session()->get('member_id');

        // Check if already responded
        if ($this->surveyModel->hasResponded($id, $memberId)) {
            return redirect()->to('/member/surveys')->with('info', 'Anda sudah mengisi survei ini');
        }

        $survey = $this->surveyModel->getSurveyWithQuestions($id);

        if (!$survey || !$survey['is_active']) {
            return redirect()->back()->with('error', 'Survei tidak ditemukan atau sudah ditutup');
        }

        $data = [
            'title' => $survey['title'] . ' - Survei SPK',
            'survey' => $survey
        ];

        return view('survey/take', $data);
    }

    /**
     * Submit survey response
     */
    public function submit($id)
    {
        $memberId = session()->get('member_id');

        // Check if already responded
        if ($this->surveyModel->hasResponded($id, $memberId)) {
            return redirect()->to('/member/surveys')->with('error', 'Anda sudah mengisi survei ini');
        }

        $questions = $this->questionModel->getQuestionsBySurvey($id);
        $answers = [];

        // Validate required questions
        foreach ($questions as $question) {
            $answer = $this->request->getPost('question_' . $question['id']);

            if ($question['is_required'] && empty($answer)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Mohon jawab semua pertanyaan yang wajib');
            }

            if (!empty($answer)) {
                // For checkbox questions, convert array to JSON
                if (is_array($answer)) {
                    $answer = json_encode($answer);
                }
                $answers[$question['id']] = $answer;
            }
        }

        // Save response
        $result = $this->responseModel->saveResponseWithAnswers($id, $memberId, $answers);

        if ($result) {
            return redirect()->to('/member/surveys')->with('success', 'Terima kasih telah mengisi survei');
        }

        return redirect()->back()->with('error', 'Gagal menyimpan response survei');
    }
}
