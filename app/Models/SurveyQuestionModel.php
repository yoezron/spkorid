<?php

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
        'order_number',
        'min_value',
        'max_value',
        'min_length',
        'max_length',
        'placeholder',
        'help_text',
        'validation_rules',
        'conditional_logic',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'survey_id' => 'required|integer',
        'question_text' => 'required|min_length[3]',
        'question_type' => 'required|in_list[text,textarea,number,radio,checkbox,dropdown,date,rating,file,email,phone,scale,matrix,time]',
        'is_required' => 'required|in_list[0,1]',
        'order_number' => 'integer'
    ];

    protected $validationMessages = [
        'question_text' => [
            'required' => 'Teks pertanyaan wajib diisi',
            'min_length' => 'Teks pertanyaan minimal 3 karakter'
        ],
        'question_type' => [
            'required' => 'Tipe pertanyaan wajib dipilih',
            'in_list' => 'Tipe pertanyaan tidak valid'
        ]
    ];

    /**
     * Get questions by survey
     */
    public function getQuestionsBySurvey($surveyId)
    {
        $questions = $this->where('survey_id', $surveyId)
            ->orderBy('order_number', 'ASC')
            ->findAll();

        // Parse options and other JSON fields
        foreach ($questions as &$question) {
            if (!empty($question['options'])) {
                $question['options'] = json_decode($question['options'], true);
            }
            if (!empty($question['validation_rules'])) {
                $question['validation_rules'] = json_decode($question['validation_rules'], true);
            }
            if (!empty($question['conditional_logic'])) {
                $question['conditional_logic'] = json_decode($question['conditional_logic'], true);
            }
        }

        return $questions;
    }

    /**
     * Create questions for a survey
     */
    public function createQuestions($surveyId, $questions)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($questions as $index => $question) {
            $data = [
                'survey_id' => $surveyId,
                'question_text' => $question['text'],
                'question_type' => $question['type'],
                'is_required' => $question['required'] ?? 0,
                'order_number' => $question['order'] ?? ($index + 1),
                'placeholder' => $question['placeholder'] ?? null,
                'help_text' => $question['help_text'] ?? null
            ];

            // Handle options for choice questions
            if (in_array($question['type'], ['radio', 'checkbox', 'dropdown', 'scale', 'matrix'])) {
                $data['options'] = json_encode($question['options'] ?? []);
            }

            // Handle validation rules
            if (!empty($question['validation'])) {
                $data['validation_rules'] = json_encode($question['validation']);
            }

            // Handle number/scale specific fields
            if (in_array($question['type'], ['number', 'scale', 'rating'])) {
                $data['min_value'] = $question['min_value'] ?? null;
                $data['max_value'] = $question['max_value'] ?? null;
            }

            // Handle text length constraints
            if (in_array($question['type'], ['text', 'textarea'])) {
                $data['min_length'] = $question['min_length'] ?? null;
                $data['max_length'] = $question['max_length'] ?? null;
            }

            // Handle conditional logic
            if (!empty($question['conditional_logic'])) {
                $data['conditional_logic'] = json_encode($question['conditional_logic']);
            }

            $this->insert($data);
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Update question order
     */
    public function updateOrder($questionId, $newOrder)
    {
        return $this->update($questionId, ['order_number' => $newOrder]);
    }

    /**
     * Reorder questions
     */
    public function reorderQuestions($surveyId, $questionIds)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($questionIds as $index => $questionId) {
            $this->update($questionId, [
                'order_number' => $index + 1
            ]);
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Duplicate questions from one survey to another
     */
    public function duplicateQuestions($fromSurveyId, $toSurveyId)
    {
        $questions = $this->where('survey_id', $fromSurveyId)->findAll();

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($questions as $question) {
            unset($question['id']);
            $question['survey_id'] = $toSurveyId;
            $question['created_at'] = date('Y-m-d H:i:s');
            $question['updated_at'] = date('Y-m-d H:i:s');

            $this->insert($question);
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Validate answer based on question type and rules
     */
    public function validateAnswer($questionId, $answer)
    {
        $question = $this->find($questionId);
        if (!$question) return false;

        // Parse validation rules if exist
        $validationRules = !empty($question['validation_rules'])
            ? json_decode($question['validation_rules'], true)
            : [];

        // Check required
        if ($question['is_required'] && empty($answer)) {
            return ['valid' => false, 'message' => 'Pertanyaan ini wajib diisi'];
        }

        // Type-specific validation
        switch ($question['question_type']) {
            case 'number':
                if (!is_numeric($answer)) {
                    return ['valid' => false, 'message' => 'Jawaban harus berupa angka'];
                }
                if ($question['min_value'] !== null && $answer < $question['min_value']) {
                    return ['valid' => false, 'message' => 'Nilai minimum adalah ' . $question['min_value']];
                }
                if ($question['max_value'] !== null && $answer > $question['max_value']) {
                    return ['valid' => false, 'message' => 'Nilai maksimum adalah ' . $question['max_value']];
                }
                break;

            case 'email':
                if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                    return ['valid' => false, 'message' => 'Format email tidak valid'];
                }
                break;

            case 'phone':
                // Simple phone validation
                if (!preg_match('/^[0-9+\-\s\(\)]+$/', $answer)) {
                    return ['valid' => false, 'message' => 'Format nomor telepon tidak valid'];
                }
                break;

            case 'text':
            case 'textarea':
                if ($question['min_length'] !== null && strlen($answer) < $question['min_length']) {
                    return ['valid' => false, 'message' => 'Minimal ' . $question['min_length'] . ' karakter'];
                }
                if ($question['max_length'] !== null && strlen($answer) > $question['max_length']) {
                    return ['valid' => false, 'message' => 'Maksimal ' . $question['max_length'] . ' karakter'];
                }
                break;

            case 'date':
                if (!strtotime($answer)) {
                    return ['valid' => false, 'message' => 'Format tanggal tidak valid'];
                }
                break;

            case 'radio':
            case 'dropdown':
                $options = json_decode($question['options'], true);
                if (!in_array($answer, $options)) {
                    return ['valid' => false, 'message' => 'Pilihan tidak valid'];
                }
                break;

            case 'checkbox':
                $options = json_decode($question['options'], true);
                $answers = is_array($answer) ? $answer : json_decode($answer, true);
                foreach ($answers as $ans) {
                    if (!in_array($ans, $options)) {
                        return ['valid' => false, 'message' => 'Satu atau lebih pilihan tidak valid'];
                    }
                }
                break;

            case 'rating':
            case 'scale':
                $min = $question['min_value'] ?? 1;
                $max = $question['max_value'] ?? 5;
                if (!is_numeric($answer) || $answer < $min || $answer > $max) {
                    return ['valid' => false, 'message' => "Nilai harus antara $min dan $max"];
                }
                break;
        }

        // Custom validation rules
        if (!empty($validationRules)) {
            // Implement custom validation logic here if needed
        }

        return ['valid' => true];
    }

    /**
     * Get question types with descriptions
     */
    public static function getQuestionTypes()
    {
        return [
            'text' => ['label' => 'Teks Singkat', 'icon' => 'type', 'description' => 'Input teks satu baris'],
            'textarea' => ['label' => 'Paragraf', 'icon' => 'notes', 'description' => 'Input teks multi baris'],
            'number' => ['label' => 'Angka', 'icon' => 'tag', 'description' => 'Input angka'],
            'radio' => ['label' => 'Pilihan Ganda', 'icon' => 'radio_button_checked', 'description' => 'Pilih satu dari beberapa opsi'],
            'checkbox' => ['label' => 'Kotak Centang', 'icon' => 'check_box', 'description' => 'Pilih satu atau lebih opsi'],
            'dropdown' => ['label' => 'Dropdown', 'icon' => 'arrow_drop_down_circle', 'description' => 'Pilih dari menu dropdown'],
            'date' => ['label' => 'Tanggal', 'icon' => 'calendar_today', 'description' => 'Pilih tanggal'],
            'time' => ['label' => 'Waktu', 'icon' => 'schedule', 'description' => 'Pilih waktu'],
            'rating' => ['label' => 'Rating', 'icon' => 'star', 'description' => 'Rating bintang (1-5)'],
            'scale' => ['label' => 'Skala', 'icon' => 'linear_scale', 'description' => 'Skala linier'],
            'file' => ['label' => 'Upload File', 'icon' => 'attach_file', 'description' => 'Upload file'],
            'email' => ['label' => 'Email', 'icon' => 'email', 'description' => 'Input email dengan validasi'],
            'phone' => ['label' => 'Telepon', 'icon' => 'phone', 'description' => 'Input nomor telepon'],
            'matrix' => ['label' => 'Matrix', 'icon' => 'grid_on', 'description' => 'Pertanyaan matrix/grid']
        ];
    }
}
