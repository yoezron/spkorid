<?php

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
        'allow_multiple_submissions',
        'require_login',
        'show_results_to_participants',
        'randomize_questions',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'description' => 'required',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Judul survei wajib diisi',
            'min_length' => 'Judul survei minimal 3 karakter',
            'max_length' => 'Judul survei maksimal 255 karakter'
        ],
        'description' => [
            'required' => 'Deskripsi survei wajib diisi'
        ]
    ];

    /**
     * Get active surveys
     */
    public function getActiveSurveys()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('is_active', 1)
            ->where('start_date <=', $now)
            ->where('end_date >=', $now)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get surveys for member (yang belum diisi)
     */
    public function getSurveysForMember($memberId)
    {
        $now = date('Y-m-d H:i:s');

        $builder = $this->db->table($this->table);
        $builder->select('surveys.*, 
                         (SELECT COUNT(*) FROM survey_responses 
                          WHERE survey_id = surveys.id AND member_id = ' . $memberId . ') as has_responded,
                         (SELECT COUNT(DISTINCT member_id) FROM survey_responses 
                          WHERE survey_id = surveys.id) as total_responses')
            ->where('is_active', 1)
            ->where('start_date <=', $now)
            ->where('end_date >=', $now)
            ->orderBy('created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get upcoming surveys
     */
    public function getUpcomingSurveys()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('is_active', 1)
            ->where('start_date >', $now)
            ->orderBy('start_date', 'ASC')
            ->findAll();
    }

    /**
     * Get expired surveys
     */
    public function getExpiredSurveys()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('end_date <', $now)
            ->orderBy('end_date', 'DESC')
            ->findAll();
    }

    /**
     * Get surveys created by user
     */
    public function getSurveysByCreator($userId)
    {
        return $this->where('created_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get survey with questions
     */
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

        // Parse options for questions that have them
        foreach ($questions as &$question) {
            if (!empty($question['options'])) {
                $question['options'] = json_decode($question['options'], true);
            }
        }

        $survey['questions'] = $questions;
        return $survey;
    }

    /**
     * Check if member has responded to survey
     */
    public function hasResponded($surveyId, $memberId)
    {
        $db = \Config\Database::connect();
        $count = $db->table('survey_responses')
            ->where('survey_id', $surveyId)
            ->where('member_id', $memberId)
            ->countAllResults();

        return $count > 0;
    }

    /**
     * Get survey statistics
     */
    public function getSurveyStatistics($surveyId)
    {
        $db = \Config\Database::connect();

        // Total responses
        $totalResponses = $db->table('survey_responses')
            ->where('survey_id', $surveyId)
            ->countAllResults();

        // Total members who haven't responded
        $totalMembers = $db->table('members')
            ->where('status', 'active')
            ->countAllResults();

        $responseRate = $totalMembers > 0 ? round(($totalResponses / $totalMembers) * 100, 2) : 0;

        // Get questions with answer statistics
        $questions = $db->table('survey_questions')
            ->where('survey_id', $surveyId)
            ->orderBy('order_number', 'ASC')
            ->get()
            ->getResultArray();

        $questionStats = [];
        foreach ($questions as $question) {
            $stats = $this->getQuestionStatistics($question);
            $questionStats[] = $stats;
        }

        return [
            'total_responses' => $totalResponses,
            'total_members' => $totalMembers,
            'response_rate' => $responseRate,
            'questions' => $questionStats
        ];
    }

    /**
     * Get statistics for a specific question
     */
    private function getQuestionStatistics($question)
    {
        $db = \Config\Database::connect();

        $question['total_answers'] = $db->table('survey_answers')
            ->where('question_id', $question['id'])
            ->countAllResults();

        // Different statistics based on question type
        switch ($question['question_type']) {
            case 'radio':
            case 'checkbox':
            case 'dropdown':
                $question['answer_distribution'] = $this->getChoiceDistribution($question['id']);
                break;

            case 'rating':
                $question['average_rating'] = $this->getAverageRating($question['id']);
                $question['rating_distribution'] = $this->getRatingDistribution($question['id']);
                break;

            case 'number':
                $question['statistics'] = $this->getNumberStatistics($question['id']);
                break;

            default:
                // For text, textarea, date - just get sample answers
                $question['sample_answers'] = $this->getSampleAnswers($question['id'], 5);
        }

        return $question;
    }

    /**
     * Get distribution for choice questions
     */
    private function getChoiceDistribution($questionId)
    {
        $db = \Config\Database::connect();

        $answers = $db->table('survey_answers')
            ->select('answer_text, COUNT(*) as count')
            ->where('question_id', $questionId)
            ->groupBy('answer_text')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        return $answers;
    }

    /**
     * Get average rating
     */
    private function getAverageRating($questionId)
    {
        $db = \Config\Database::connect();

        $result = $db->table('survey_answers')
            ->selectAvg('CAST(answer_text AS UNSIGNED)', 'average')
            ->where('question_id', $questionId)
            ->get()
            ->getRowArray();

        return round($result['average'] ?? 0, 2);
    }

    /**
     * Get rating distribution
     */
    private function getRatingDistribution($questionId)
    {
        $db = \Config\Database::connect();

        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $db->table('survey_answers')
                ->where('question_id', $questionId)
                ->where('answer_text', $i)
                ->countAllResults();

            $distribution[$i] = $count;
        }

        return $distribution;
    }

    /**
     * Get statistics for number questions
     */
    private function getNumberStatistics($questionId)
    {
        $db = \Config\Database::connect();

        $result = $db->table('survey_answers')
            ->select('
                MIN(CAST(answer_text AS DECIMAL(10,2))) as min,
                MAX(CAST(answer_text AS DECIMAL(10,2))) as max,
                AVG(CAST(answer_text AS DECIMAL(10,2))) as average,
                COUNT(*) as count
            ')
            ->where('question_id', $questionId)
            ->get()
            ->getRowArray();

        return [
            'min' => $result['min'] ?? 0,
            'max' => $result['max'] ?? 0,
            'average' => round($result['average'] ?? 0, 2),
            'count' => $result['count'] ?? 0
        ];
    }

    /**
     * Get sample answers for text questions
     */
    private function getSampleAnswers($questionId, $limit = 5)
    {
        $db = \Config\Database::connect();

        return $db->table('survey_answers')
            ->select('answer_text, created_at')
            ->where('question_id', $questionId)
            ->where('answer_text !=', '')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Clone a survey
     */
    public function cloneSurvey($surveyId, $newTitle = null)
    {
        $survey = $this->find($surveyId);
        if (!$survey) return false;

        $db = \Config\Database::connect();
        $db->transStart();

        // Clone survey
        unset($survey['id']);
        $survey['title'] = $newTitle ?? $survey['title'] . ' (Copy)';
        $survey['created_at'] = date('Y-m-d H:i:s');
        $survey['updated_at'] = date('Y-m-d H:i:s');
        $survey['is_active'] = 0; // Set as inactive by default

        $newSurveyId = $this->insert($survey);

        // Clone questions
        $questions = $db->table('survey_questions')
            ->where('survey_id', $surveyId)
            ->get()
            ->getResultArray();

        foreach ($questions as $question) {
            unset($question['id']);
            $question['survey_id'] = $newSurveyId;
            $question['created_at'] = date('Y-m-d H:i:s');
            $question['updated_at'] = date('Y-m-d H:i:s');

            $db->table('survey_questions')->insert($question);
        }

        $db->transComplete();

        return $db->transStatus() ? $newSurveyId : false;
    }

    /**
     * Toggle survey status
     */
    public function toggleStatus($surveyId)
    {
        $survey = $this->find($surveyId);
        if (!$survey) return false;

        return $this->update($surveyId, [
            'is_active' => !$survey['is_active']
        ]);
    }

    /**
     * Get survey summary for dashboard
     */
    public function getSurveySummary()
    {
        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();

        return [
            'total_surveys' => $this->countAllResults(),
            'active_surveys' => $this->where('is_active', 1)
                ->where('start_date <=', $now)
                ->where('end_date >=', $now)
                ->countAllResults(),
            'upcoming_surveys' => $this->where('is_active', 1)
                ->where('start_date >', $now)
                ->countAllResults(),
            'total_responses' => $db->table('survey_responses')
                ->countAllResults()
        ];
    }
}
