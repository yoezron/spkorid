<?php

// ============================================
// MODEL UNTUK SURVEI
// ============================================

// app/Models/SurveyModel.php
namespace App\Models;

use CodeIgniter\Model;

class SurveyModel extends Model
{
    protected $table = 'surveys';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'is_anonymous',
        'created_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get active surveys
    public function getActiveSurveys()
    {
        return $this->where('is_active', 1)
            ->where('start_date <=', date('Y-m-d H:i:s'))
            ->where('end_date >=', date('Y-m-d H:i:s'))
            ->findAll();
    }

    // Get surveys created by user
    public function getSurveysByCreator($userId)
    {
        return $this->where('created_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    // Get survey with questions
    public function getSurveyWithQuestions($surveyId)
    {
        $survey = $this->find($surveyId);
        if (!$survey) return null;

        $db = \Config\Database::connect();
        $questions = $db->table('survey_questions')
            ->where('survey_id', $surveyId)
            ->orderBy('order_number', 'ASC')
            ->get()
            ->getResultArray();

        $survey['questions'] = $questions;
        return $survey;
    }

    // Check if member has responded
    public function hasResponded($surveyId, $memberId)
    {
        $db = \Config\Database::connect();
        $response = $db->table('survey_responses')
            ->where('survey_id', $surveyId)
            ->where('member_id', $memberId)
            ->get()
            ->getRowArray();

        return $response !== null;
    }

    // Get survey statistics
    public function getSurveyStatistics($surveyId)
    {
        $db = \Config\Database::connect();

        // Total responses
        $totalResponses = $db->table('survey_responses')
            ->where('survey_id', $surveyId)
            ->countAllResults();

        // Get questions with answer statistics
        $questions = $db->table('survey_questions sq')
            ->select('sq.*, COUNT(DISTINCT sr.id) as response_count')
            ->join('survey_answers sa', 'sa.question_id = sq.id', 'left')
            ->join('survey_responses sr', 'sr.id = sa.response_id', 'left')
            ->where('sq.survey_id', $surveyId)
            ->groupBy('sq.id')
            ->orderBy('sq.order_number', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'total_responses' => $totalResponses,
            'questions' => $questions
        ];
    }
}
