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
            // Create response record
            $startTime = session()->get('survey_start_time_' . $surveyId);
            $completionTime = $startTime ? (time() - $startTime) : null;
            
            $responseData = [
                'survey_id' => $surveyId,
                'member_id' => $memberId,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'started_at' => $startTime ? date('Y-m-d H:i:s', $startTime) : date('Y-m-d H:i:s'),
                'submitted_at' => date('Y-m-d H:i:s'),
                'is_complete' => 1,
                'completion_time' => $completionTime
            ];

            $responseId = $this->insert($responseData);

            // Insert answers
            $answerModel = new SurveyAnswerModel();
            
            foreach ($answers as $questionId => $answerText) {
                // Handle array answers (checkbox)
                if (is_array($answerText)) {
                    $answerText = json_encode($answerText);
                }
                
                $answerData = [
                    'response_id' => $responseId,
                    'question_id' => $questionId,
                    'answer_text' => $answerText,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Handle file uploads
                if (isset($files[$questionId])) {
                    $answerData['file_path'] = $this->uploadFile($files[$questionId], $responseId, $questionId);
                }
                
                $answerModel->insert($answerData);
            }

            // Clear session start time
            session()->remove('survey_start_time_' . $surveyId);

            $db->transComplete();
            return $db->transStatus() ? $responseId : false;
            
        } catch (\Exception $e) {
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
            $newName = $responseId . '_' . $questionId . '_' . uniqid() . '.' . $file->getExtension();