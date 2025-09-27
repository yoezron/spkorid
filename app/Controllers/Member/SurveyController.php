<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Models\SurveyQuestionModel;
use App\Models\SurveyResponseModel;
use App\Models\SurveyAnswerModel;

/**
 * Class SurveyController
 * Handles all survey-related actions for members.
 */
class SurveyController extends BaseController
{
    protected $surveyModel;
    protected $questionModel;
    protected $responseModel;
    protected $answerModel;
    protected $db;
    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->questionModel = new SurveyQuestionModel();
        $this->responseModel = new SurveyResponseModel();
        $this->answerModel = new SurveyAnswerModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan daftar survei yang tersedia untuk anggota.
     * Memisahkan survei ke dalam kategori: tersedia, selesai, dan akan datang.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function index()
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Ambil semua survei yang relevan untuk anggota
        $surveys = $this->surveyModel->getSurveysForMember($memberId);

        // Inisialisasi array untuk setiap status survei
        $availableSurveys = [];
        $completedSurveys = [];
        $upcomingSurveys = [];
        $now = date('Y-m-d H:i:s');

        // Kelompokkan survei berdasarkan statusnya
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
            'title'             => 'Survei Anggota - SPK',
            'available_surveys' => $availableSurveys,
            'completed_surveys' => $completedSurveys,
            'upcoming_surveys'  => $upcomingSurveys
        ];

        return view('member/survey/index', $data);
    }

    /**
     * Menampilkan form untuk mengisi survei.
     *
     * @param int $id ID Survei
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function take($id)
    {
        $memberId = session()->get('member_id');
        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $survey = $this->surveyModel->find($id);

        // Validasi survei
        if (!$survey || !$survey['is_active']) {
            return redirect()->to('/member/surveys')->with('error', 'Survei tidak ditemukan atau sudah ditutup');
        }

        // Validasi rentang waktu survei
        $now = date('Y-m-d H:i:s');
        if ($now < $survey['start_date']) {
            return redirect()->to('/member/surveys')->with('info', 'Survei belum dibuka');
        }
        if ($now > $survey['end_date']) {
            return redirect()->to('/member/surveys')->with('info', 'Survei sudah ditutup');
        }

        // Validasi apakah anggota sudah pernah mengisi
        if (!$survey['allow_multiple_submissions'] && $this->responseModel->hasResponded($id, $memberId)) {
            return redirect()->to('/member/surveys')->with('info', 'Anda sudah mengisi survei ini');
        }

        $surveyData = $this->surveyModel->getSurveyWithQuestions($id);
        if ($surveyData['randomize_questions']) {
            shuffle($surveyData['questions']);
        }

        $partialResponse = $this->responseModel->getPartialResponse($id, $memberId);
        if (!$partialResponse) {
            session()->set('survey_start_time_' . $id, time());
        }

        $data = [
            'title'             => 'Isi Survei: ' . $surveyData['title'],
            'survey'            => $surveyData,
            'partial_response'  => $partialResponse
        ];

        return view('member/survey/take', $data);
    }

    /**
     * Memproses data yang disubmit dari form survei.
     *
     * @param int $id ID Survei
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function submit($id)
    {
        $memberId = session()->get('member_id');
        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $survey = $this->surveyModel->find($id);
        if (!$survey || !$survey['is_active']) {
            return redirect()->to('/member/surveys')->with('error', 'Survei tidak valid');
        }

        if (!$survey['allow_multiple_submissions'] && $this->responseModel->hasResponded($id, $memberId)) {
            return redirect()->to('/member/surveys')->with('error', 'Anda sudah mengisi survei ini');
        }

        $questions = $this->questionModel->getQuestionsBySurvey($id);
        $errors = [];
        $answers = [];
        $files = [];

        // Validasi setiap jawaban
        foreach ($questions as $question) {
            $fieldName = 'question_' . $question['id'];
            $answer = $this->request->getPost($fieldName);

            // Penanganan upload file
            if ($question['question_type'] == 'file') {
                $file = $this->request->getFile($fieldName);
                if ($file && $file->isValid()) {
                    $files[$question['id']] = $file;
                    $answer = 'file_uploaded'; // Placeholder
                } elseif ($question['is_required']) {
                    $errors[] = $question['question_text'] . ' wajib diupload';
                    continue;
                }
            }

            $validation = $this->questionModel->validateAnswer($question['id'], $answer);
            if (!$validation['valid']) {
                $errors[] = $question['question_text'] . ': ' . $validation['message'];
            } else {
                if (!empty($answer) || $answer === '0') {
                    $answers[$question['id']] = $answer;
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Simpan response dan jawaban
        $responseId = $this->responseModel->saveResponseWithAnswers($id, $memberId, $answers, $files);

        if ($responseId) {
            // Hapus partial response jika ada
            $partial = $this->responseModel->getPartialResponse($id, $memberId);
            if ($partial) {
                $this->responseModel->delete($partial['id']);
            }

            $this->logMemberActivity('submit_survey', 'Mengisi survei: ' . $survey['title']);

            $redirectPath = $survey['show_results_to_participants'] ? '/member/surveys/result/' . $id : '/member/surveys';
            return redirect()->to($redirectPath)->with('success', 'Terima kasih! Survei berhasil dikirim.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan survei. Silakan coba lagi.');
        }
    }

    /**
     * Menyimpan progres pengisian survei secara otomatis (auto-save).
     *
     * @param int $id ID Survei
     * @return \CodeIgniter\HTTP\Response
     */
    public function autoSave($id)
    {
        $memberId = session()->get('member_id');
        if (!$memberId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi berakhir'])->setStatusCode(401);
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
            'success'   => $saved,
            'message'   => $saved ? 'Progres tersimpan' : 'Gagal menyimpan',
            'timestamp' => date('H:i:s')
        ]);
    }

    /**
     * Menampilkan hasil agregat dari sebuah survei jika diizinkan.
     *
     * @param int $id ID Survei
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function result($id)
    {
        $memberId = session()->get('member_id');
        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $survey = $this->surveyModel->find($id);
        if (!$survey || !$survey['show_results_to_participants']) {
            return redirect()->to('/member/surveys')->with('error', 'Hasil survei tidak tersedia');
        }

        if (!$this->responseModel->hasResponded($id, $memberId)) {
            return redirect()->to('/member/surveys/take/' . $id)->with('info', 'Silakan isi survei terlebih dahulu untuk melihat hasil');
        }

        $surveyData = $this->surveyModel->getSurveyWithQuestions($id);

        foreach ($surveyData['questions'] as &$question) {
            $question['distribution'] = $this->answerModel->getAnswerDistribution($question['id']);
        }

        $data = [
            'title'           => 'Hasil Survei: ' . $surveyData['title'],
            'survey'          => $surveyData,
            'statistics'      => $this->surveyModel->getSurveyStatistics($id),
            'member_response' => $this->responseModel->getMemberResponse($id, $memberId)
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
