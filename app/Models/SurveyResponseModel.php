<?php

// app/Models/SurveyResponseModel.php
namespace App\Models;

use CodeIgniter\Model;

class SurveyResponseModel extends Model
{
    protected $table = 'survey_responses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['survey_id', 'member_id', 'submitted_at'];

    // Save response with answers
    public function saveResponseWithAnswers($surveyId, $memberId, $answers)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create response record
            $responseData = [
                'survey_id' => $surveyId,
                'member_id' => $memberId,
                'submitted_at' => date('Y-m-d H:i:s')
            ];

            $responseId = $this->insert($responseData);

            // Insert answers
            foreach ($answers as $questionId => $answerText) {
                $db->table('survey_answers')->insert([
                    'response_id' => $responseId,
                    'question_id' => $questionId,
                    'answer_text' => $answerText
                ]);
            }

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }

    // Get responses with member info
    public function getResponsesWithMembers($surveyId)
    {
        return $this->select('survey_responses.*, members.nama_lengkap, members.nomor_anggota')
            ->join('members', 'members.id = survey_responses.member_id')
            ->where('survey_responses.survey_id', $surveyId)
            ->orderBy('survey_responses.submitted_at', 'DESC')
            ->findAll();
    }
}
