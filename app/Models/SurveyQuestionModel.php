<?php

// app/Models/SurveyQuestionModel.php
namespace App\Models;

use CodeIgniter\Model;

class SurveyQuestionModel extends Model
{
    protected $table = 'survey_questions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'survey_id',
        'question_text',
        'question_type',
        'options',
        'is_required',
        'order_number'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get questions by survey
    public function getQuestionsBySurvey($surveyId)
    {
        return $this->where('survey_id', $surveyId)
            ->orderBy('order_number', 'ASC')
            ->findAll();
    }
}
