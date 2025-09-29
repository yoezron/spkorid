<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveyResponseModel extends Model
{
    protected $table = 'survey_responses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'survey_id',
        'member_id',
        'ip_address',
        'user_agent',
        'started_at',
        'submitted_at',
        'is_complete',
        'completion_time'
    ];

    protected $useTimestamps = false; // We manage timestamps manually

    protected $validationRules = [
        'survey_id' => 'required|integer',
        'member_id' => 'required|integer'
    ];

    /**
     * Save response with answers
     */
    public function saveResponseWithAnswers($surveyId, $memberId, $answers, $files = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Cari response existing (draft/complete) untuk pasangan survey & member ini
            $existing = $this->where('survey_id', $surveyId)
                ->where('member_id', $memberId)
                ->first();

            // Hitung completion time
            $startTime = null;
            if ($existing && !empty($existing['started_at'])) {
                $startTime = strtotime($existing['started_at']);
            } else {
                $startTime = session()->get('survey_start_time_' . $surveyId);
                if (!is_int($startTime)) {
                    $startTime = $startTime ? strtotime($startTime) : null;
                }
            }
            $completionTime = $startTime ? (time() - $startTime) : null;

            if ($existing) {
                // Pakai baris existing (biasanya draft dari auto-save), jadikan complete
                $responseId = $existing['id'];

                $this->update($responseId, [
                    'ip_address'      => service('request')->getIPAddress(),
                    'user_agent'      => service('request')->getUserAgent()->getAgentString(),
                    'submitted_at'    => date('Y-m-d H:i:s'),
                    'is_complete'     => 1,
                    'completion_time' => $completionTime,
                ]);

                // Hapus jawaban lama agar bersih
                $db->table('survey_answers')->where('response_id', $responseId)->delete();
            } else {
                // Belum ada: buat baris baru (seperti sebelumnya)
                $responseData = [
                    'survey_id'       => $surveyId,
                    'member_id'       => $memberId,
                    'ip_address'      => service('request')->getIPAddress(),
                    'user_agent'      => service('request')->getUserAgent()->getAgentString(),
                    'started_at'      => $startTime ? date('Y-m-d H:i:s', $startTime) : date('Y-m-d H:i:s'),
                    'submitted_at'    => date('Y-m-d H:i:s'),
                    'is_complete'     => 1,
                    'completion_time' => $completionTime,
                ];

                $responseId = $this->insert($responseData);
            }

            // Simpan jawaban
            $answerModel = new SurveyAnswerModel();

            foreach ($answers as $questionId => $answerText) {
                // Array (checkbox) → simpan JSON
                if (is_array($answerText)) {
                    $answerText = json_encode($answerText);
                }

                $answerData = [
                    'response_id' => $responseId,
                    'question_id' => $questionId,
                    'answer_text' => $answerText,
                    'created_at'  => date('Y-m-d H:i:s'),
                ];

                // File upload (jika ada)
                if (isset($files[$questionId])) {
                    $answerData['file_path'] = $this->uploadFile($files[$questionId], $responseId, $questionId);
                }

                $answerModel->insert($answerData);
            }

            // Bersihkan penanda waktu mulai di sesi
            session()->remove('survey_start_time_' . $surveyId);

            $db->transComplete();
            return $db->transStatus() ? $responseId : false;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Error saving survey response: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Upload file for survey answer
     */
    private function uploadFile($file, $responseId, $questionId)
    {
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $responseId . '_' . $questionId . '_' . $file->getRandomName();
            $path = WRITEPATH . 'uploads/survey_files/' . date('Y/m');

            // Create directory if not exists
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $file->move($path, $newName);
            return 'survey_files/' . date('Y/m') . '/' . $newName;
        }
        return null;
    }

    /**
     * Get responses with member info
     */
    public function getResponsesWithMembers($surveyId)
    {
        return $this->select('survey_responses.*, members.nama_lengkap, members.nomor_anggota, members.email')
            ->join('members', 'members.id = survey_responses.member_id')
            ->where('survey_responses.survey_id', $surveyId)
            ->orderBy('survey_responses.submitted_at', 'DESC')
            ->findAll();
    }

    /**
     * Get response with all answers
     */
    public function getResponseWithAnswers($responseId)
    {
        $response = $this->find($responseId);
        if (!$response) return null;

        $db = \Config\Database::connect();
        $answers = $db->table('survey_answers sa')
            ->select('sa.*, sq.question_text, sq.question_type')
            ->join('survey_questions sq', 'sq.id = sa.question_id')
            ->where('sa.response_id', $responseId)
            ->orderBy('sq.order_number', 'ASC')
            ->get()
            ->getResultArray();

        $response['answers'] = $answers;
        return $response;
    }

    /**
     * Get all responses for a survey with answers
     */
    public function getFullResponses($surveyId)
    {
        $responses = $this->getResponsesWithMembers($surveyId);

        $db = \Config\Database::connect();
        foreach ($responses as &$response) {
            $answers = $db->table('survey_answers')
                ->where('response_id', $response['id'])
                ->get()
                ->getResultArray();

            // Convert to associative array for easy access
            $response['answers'] = array_column($answers, 'answer_text', 'question_id');
        }

        return $responses;
    }

    /**
     * Check if member has responded to survey
     */
    public function hasResponded($surveyId, $memberId)
    {
        return $this->where('survey_id', $surveyId)
            ->where('member_id', $memberId)
            ->where('is_complete', 1)
            ->countAllResults() > 0;
    }

    /**
     * Get member's response to a survey
     */
    public function getMemberResponse($surveyId, $memberId)
    {
        $response = $this->where('survey_id', $surveyId)
            ->where('member_id', $memberId)
            ->where('is_complete', 1)
            ->first();

        if ($response) {
            return $this->getResponseWithAnswers($response['id']);
        }

        return null;
    }

    /**
     * Save partial response (for auto-save feature)
     */
    public function savePartialResponse($surveyId, $memberId, $answers)
    {
        // Check if partial response exists
        $existing = $this->where('survey_id', $surveyId)
            ->where('member_id', $memberId)
            ->where('is_complete', 0)
            ->first();

        $db = \Config\Database::connect();
        $db->transStart();

        if ($existing) {
            // Update existing partial response
            $responseId = $existing['id'];

            // Delete old answers
            $db->table('survey_answers')
                ->where('response_id', $responseId)
                ->delete();
        } else {
            // Create new partial response
            $responseData = [
                'survey_id' => $surveyId,
                'member_id' => $memberId,
                'ip_address' => service('request')->getIPAddress(),
                'user_agent' => service('request')->getUserAgent()->getAgentString(),
                'started_at' => date('Y-m-d H:i:s'),
                'is_complete' => 0
            ];
            $responseId = $this->insert($responseData);
        }

        // Insert new answers
        foreach ($answers as $questionId => $answerText) {
            if (!empty($answerText)) {
                $db->table('survey_answers')->insert([
                    'response_id' => $responseId,
                    'question_id' => $questionId,
                    'answer_text' => is_array($answerText) ? json_encode($answerText) : $answerText,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Get partial response
     */
    public function getPartialResponse($surveyId, $memberId)
    {
        $response = $this->where('survey_id', $surveyId)
            ->where('member_id', $memberId)
            ->where('is_complete', 0)
            ->first();

        if ($response) {
            return $this->getResponseWithAnswers($response['id']);
        }

        return null;
    }

    /**
     * Convert partial to complete response
     */
    public function completePartialResponse($responseId)
    {
        $startTime = $this->find($responseId)['started_at'];
        $completionTime = $startTime ? (time() - strtotime($startTime)) : null;

        return $this->update($responseId, [
            'submitted_at' => date('Y-m-d H:i:s'),
            'is_complete' => 1,
            'completion_time' => $completionTime
        ]);
    }

    /**
     * Get response statistics for a survey
     */
    public function getResponseStatistics($surveyId)
    {
        $db = \Config\Database::connect();

        $total = $this->where('survey_id', $surveyId)
            ->where('is_complete', 1)
            ->countAllResults();

        $partial = $this->where('survey_id', $surveyId)
            ->where('is_complete', 0)
            ->countAllResults();

        // Average completion time
        $avgTime = $db->table($this->table)
            ->selectAvg('completion_time', 'avg_time')
            ->where('survey_id', $surveyId)
            ->where('is_complete', 1)
            ->where('completion_time IS NOT NULL')
            ->get()
            ->getRowArray();

        // Response rate by date
        $dailyResponses = $db->table($this->table)
            ->select('DATE(submitted_at) as date, COUNT(*) as count')
            ->where('survey_id', $surveyId)
            ->where('is_complete', 1)
            ->groupBy('DATE(submitted_at)')
            ->orderBy('date', 'DESC')
            ->limit(30)
            ->get()
            ->getResultArray();

        return [
            'total_complete' => $total,
            'total_partial' => $partial,
            'avg_completion_time' => $avgTime['avg_time'] ? round($avgTime['avg_time'] / 60, 2) : 0, // in minutes
            'daily_responses' => $dailyResponses
        ];
    }

    /**
     * Export responses to array for CSV/Excel
     */
    public function exportResponses($surveyId)
    {
        $db = \Config\Database::connect();

        // Get survey details
        $survey = $db->table('surveys')->where('id', $surveyId)->get()->getRowArray();

        // Get questions
        $questions = $db->table('survey_questions')
            ->where('survey_id', $surveyId)
            ->orderBy('order_number', 'ASC')
            ->get()
            ->getResultArray();

        // Get responses
        $responses = $this->getResponsesWithMembers($surveyId);

        $exportData = [];

        // Build header row
        $header = ['ID Response', 'Tanggal Submit'];

        if (!$survey['is_anonymous']) {
            $header[] = 'Nama';
            $header[] = 'Email';
            $header[] = 'Nomor Anggota';
        }

        foreach ($questions as $question) {
            $header[] = $question['question_text'];
        }

        $exportData[] = $header;

        // Build data rows
        foreach ($responses as $response) {
            $row = [
                $response['id'],
                $response['submitted_at']
            ];

            if (!$survey['is_anonymous']) {
                $row[] = $response['nama_lengkap'];
                $row[] = $response['email'];
                $row[] = $response['nomor_anggota'];
            }

            // Get answers for this response
            $answers = $db->table('survey_answers')
                ->where('response_id', $response['id'])
                ->get()
                ->getResultArray();

            $answerMap = array_column($answers, 'answer_text', 'question_id');

            foreach ($questions as $question) {
                $answer = $answerMap[$question['id']] ?? '';

                // Decode JSON for checkbox answers
                if ($question['question_type'] == 'checkbox' && !empty($answer)) {
                    $decoded = json_decode($answer, true);
                    if (is_array($decoded)) {
                        $answer = implode(', ', $decoded);
                    }
                }

                $row[] = $answer;
            }

            $exportData[] = $row;
        }

        return $exportData;
    }

    /**
     * Delete response and its answers
     */
    public function deleteResponse($responseId)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Delete answers first (due to foreign key)
        $db->table('survey_answers')
            ->where('response_id', $responseId)
            ->delete();

        // Delete response
        $this->delete($responseId);

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Get response summary for dashboard
     */
    public function getResponseSummary($userId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        if ($userId) {
            $builder->join('surveys', 'surveys.id = survey_responses.survey_id')
                ->where('surveys.created_by', $userId);
        }

        $summary = [
            'total_responses' => $builder->countAllResults(false),
            'complete_responses' => $builder->where('is_complete', 1)->countAllResults(false),
            'partial_responses' => $builder->where('is_complete', 0)->countAllResults(false),
            'today_responses' => $builder->where('DATE(submitted_at)', date('Y-m-d'))->countAllResults(false),
            'this_week' => $builder->where('submitted_at >=', date('Y-m-d', strtotime('-7 days')))->countAllResults(false),
            'this_month' => $builder->where('MONTH(submitted_at)', date('m'))
                ->where('YEAR(submitted_at)', date('Y'))
                ->countAllResults()
        ];

        return $summary;
    }

    /**
     * Clean up old partial responses (older than 7 days)
     */
    public function cleanupPartialResponses()
    {
        $date = date('Y-m-d H:i:s', strtotime('-7 days'));

        $oldPartials = $this->where('is_complete', 0)
            ->where('started_at <', $date)
            ->findAll();

        $deleted = 0;
        foreach ($oldPartials as $partial) {
            if ($this->deleteResponse($partial['id'])) {
                $deleted++;
            }
        }

        log_message('info', "Cleaned up {$deleted} old partial responses");
        return $deleted;
    }
}
