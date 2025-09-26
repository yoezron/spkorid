<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveyAnswerModel extends Model
{
    protected $table = 'survey_answers';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'response_id',
        'question_id',
        'answer_text',
        'answer_value',
        'file_path',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'response_id' => 'required|integer',
        'question_id' => 'required|integer'
    ];

    /**
     * Get answers for a response
     */
    public function getAnswersByResponse($responseId)
    {
        return $this->select('survey_answers.*, survey_questions.question_text, survey_questions.question_type')
            ->join('survey_questions', 'survey_questions.id = survey_answers.question_id')
            ->where('survey_answers.response_id', $responseId)
            ->orderBy('survey_questions.order_number', 'ASC')
            ->findAll();
    }

    /**
     * Get answers for a specific question
     */
    public function getAnswersByQuestion($questionId)
    {
        return $this->where('question_id', $questionId)
            ->findAll();
    }

    /**
     * Get answer distribution for a question
     */
    public function getAnswerDistribution($questionId)
    {
        $db = \Config\Database::connect();

        // Get question type first
        $question = $db->table('survey_questions')
            ->where('id', $questionId)
            ->get()
            ->getRowArray();

        if (!$question) return [];

        switch ($question['question_type']) {
            case 'radio':
            case 'dropdown':
                return $this->getChoiceDistribution($questionId);

            case 'checkbox':
                return $this->getMultiChoiceDistribution($questionId);

            case 'rating':
            case 'scale':
                return $this->getRatingDistribution($questionId, $question);

            case 'number':
                return $this->getNumberDistribution($questionId);

            default:
                return $this->getTextAnswers($questionId);
        }
    }

    /**
     * Get distribution for single choice questions
     */
    private function getChoiceDistribution($questionId)
    {
        $db = \Config\Database::connect();

        return $db->table($this->table)
            ->select('answer_text as value, COUNT(*) as count, ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM survey_answers WHERE question_id = ' . $questionId . '), 2) as percentage')
            ->where('question_id', $questionId)
            ->where('answer_text !=', '')
            ->groupBy('answer_text')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get distribution for multiple choice questions
     */
    private function getMultiChoiceDistribution($questionId)
    {
        $db = \Config\Database::connect();

        $answers = $this->where('question_id', $questionId)
            ->where('answer_text !=', '')
            ->findAll();

        $distribution = [];
        $totalResponses = count($answers);

        foreach ($answers as $answer) {
            $choices = json_decode($answer['answer_text'], true);
            if (is_array($choices)) {
                foreach ($choices as $choice) {
                    if (!isset($distribution[$choice])) {
                        $distribution[$choice] = 0;
                    }
                    $distribution[$choice]++;
                }
            }
        }

        // Convert to array format with percentages
        $result = [];
        foreach ($distribution as $value => $count) {
            $result[] = [
                'value' => $value,
                'count' => $count,
                'percentage' => round(($count / $totalResponses) * 100, 2)
            ];
        }

        // Sort by count descending
        usort($result, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        return $result;
    }

    /**
     * Get distribution for rating/scale questions
     */
    private function getRatingDistribution($questionId, $question)
    {
        $db = \Config\Database::connect();

        $min = $question['min_value'] ?? 1;
        $max = $question['max_value'] ?? 5;

        $distribution = [];
        $total = 0;
        $sum = 0;

        for ($i = $min; $i <= $max; $i++) {
            $count = $this->where('question_id', $questionId)
                ->where('answer_text', $i)
                ->countAllResults();

            $distribution[] = [
                'value' => $i,
                'count' => $count,
                'percentage' => 0 // Will calculate after getting total
            ];

            $total += $count;
            $sum += $i * $count;
        }

        // Calculate percentages and average
        foreach ($distribution as &$item) {
            $item['percentage'] = $total > 0 ? round(($item['count'] / $total) * 100, 2) : 0;
        }

        $average = $total > 0 ? round($sum / $total, 2) : 0;

        return [
            'distribution' => $distribution,
            'average' => $average,
            'total_responses' => $total
        ];
    }

    /**
     * Get distribution for number questions
     */
    private function getNumberDistribution($questionId)
    {
        $db = \Config\Database::connect();

        $stats = $db->table($this->table)
            ->select('
                MIN(CAST(answer_text AS DECIMAL(10,2))) as min,
                MAX(CAST(answer_text AS DECIMAL(10,2))) as max,
                AVG(CAST(answer_text AS DECIMAL(10,2))) as average,
                COUNT(*) as count,
                STDDEV(CAST(answer_text AS DECIMAL(10,2))) as std_dev
            ')
            ->where('question_id', $questionId)
            ->where('answer_text !=', '')
            ->where("answer_text REGEXP '^[0-9]+\\.?[0-9]*$'") // PERBAIKAN DI SINI
            ->get()
            ->getRowArray();

        // Get median
        $values = $db->table($this->table)
            ->select('CAST(answer_text AS DECIMAL(10,2)) as value')
            ->where('question_id', $questionId)
            ->where('answer_text !=', '')
            ->where("answer_text REGEXP '^[0-9]+\\.?[0-9]*$'") // PERBAIKAN DI SINI JUGA
            ->orderBy('value', 'ASC')
            ->get()
            ->getResultArray();

        $median = 0;
        if (count($values) > 0) {
            $middle = floor(count($values) / 2);
            if (count($values) % 2 == 0) {
                $median = ($values[$middle - 1]['value'] + $values[$middle]['value']) / 2;
            } else {
                $median = $values[$middle]['value'];
            }
        }

        return [
            'min' => round($stats['min'] ?? 0, 2),
            'max' => round($stats['max'] ?? 0, 2),
            'average' => round($stats['average'] ?? 0, 2),
            'median' => round($median, 2),
            'std_dev' => round($stats['std_dev'] ?? 0, 2),
            'count' => $stats['count'] ?? 0
        ];
    }

    /**
     * Get text answers with word cloud data
     */
    private function getTextAnswers($questionId, $limit = 10)
    {
        $answers = $this->where('question_id', $questionId)
            ->where('answer_text !=', '')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get word frequency for word cloud
        $wordFrequency = $this->getWordFrequency($answers);

        return [
            'recent_answers' => array_slice($answers, 0, $limit),
            'total_answers' => count($answers),
            'word_frequency' => array_slice($wordFrequency, 0, 50) // Top 50 words
        ];
    }

    /**
     * Calculate word frequency for word cloud
     */
    private function getWordFrequency($answers)
    {
        $words = [];
        $stopWords = ['dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'dengan', 'adalah', 'pada', 'dalam', 'ini', 'itu', 'akan', 'telah', 'sudah', 'ada', 'tidak', 'bisa', 'saya', 'anda', 'kami', 'kita'];

        foreach ($answers as $answer) {
            $text = strtolower($answer['answer_text']);
            $text = preg_replace('/[^a-z0-9\s]/i', '', $text);
            $wordList = explode(' ', $text);

            foreach ($wordList as $word) {
                $word = trim($word);
                if (strlen($word) > 2 && !in_array($word, $stopWords)) {
                    if (!isset($words[$word])) {
                        $words[$word] = 0;
                    }
                    $words[$word]++;
                }
            }
        }

        // Sort by frequency
        arsort($words);

        // Convert to array format
        $result = [];
        foreach ($words as $word => $count) {
            $result[] = [
                'text' => $word,
                'count' => $count,
                'size' => min(40, 10 + ($count * 2)) // Font size for word cloud
            ];
        }

        return $result;
    }

    /**
     * Bulk insert answers
     */
    public function bulkInsert($data)
    {
        if (empty($data)) return false;

        $db = \Config\Database::connect();
        return $db->table($this->table)->insertBatch($data);
    }

    /**
     * Delete answers by response ID
     */
    public function deleteByResponse($responseId)
    {
        return $this->where('response_id', $responseId)->delete();
    }

    /**
     * Get answer summary for export
     */
    public function getAnswerSummary($surveyId)
    {
        $db = \Config\Database::connect();

        return $db->table($this->table . ' sa')
            ->select('sa.*, sq.question_text, sq.question_type, sq.order_number, sr.member_id, sr.submitted_at')
            ->join('survey_questions sq', 'sq.id = sa.question_id')
            ->join('survey_responses sr', 'sr.id = sa.response_id')
            ->where('sr.survey_id', $surveyId)
            ->where('sr.is_complete', 1)
            ->orderBy('sr.submitted_at', 'DESC')
            ->orderBy('sq.order_number', 'ASC')
            ->get()
            ->getResultArray();
    }
}
