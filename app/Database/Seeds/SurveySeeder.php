<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SurveySeeder extends Seeder
{
    public function run()
    {
        // Create sample surveys
        $surveys = [
            [
                'title' => 'Survei Kepuasan Anggota SPK 2025',
                'description' => 'Survei untuk mengukur tingkat kepuasan anggota terhadap layanan dan program SPK',
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'is_active' => 1,
                'is_anonymous' => 0,
                'allow_multiple_submissions' => 0,
                'require_login' => 1,
                'show_results_to_participants' => 1,
                'randomize_questions' => 0,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Evaluasi Program Pelatihan',
                'description' => 'Evaluasi terhadap program pelatihan yang telah dilaksanakan SPK',
                'start_date' => date('Y-m-d H:i:s', strtotime('+5 days')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+20 days')),
                'is_active' => 1,
                'is_anonymous' => 1,
                'allow_multiple_submissions' => 0,
                'require_login' => 1,
                'show_results_to_participants' => 0,
                'randomize_questions' => 1,
                'created_by' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Survei Kondisi Kerja dan Kesejahteraan',
                'description' => 'Survei untuk mengetahui kondisi kerja dan tingkat kesejahteraan anggota SPK',
                'start_date' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+10 days')),
                'is_active' => 1,
                'is_anonymous' => 1,
                'allow_multiple_submissions' => 0,
                'require_login' => 1,
                'show_results_to_participants' => 1,
                'randomize_questions' => 0,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert surveys
        foreach ($surveys as $survey) {
            $this->db->table('surveys')->insert($survey);
            $surveyId = $this->db->insertID();

            // Add questions for each survey
            $this->createQuestionsForSurvey($surveyId, $survey['title']);
        }
    }

    private function createQuestionsForSurvey($surveyId, $surveyTitle)
    {
        $questions = [];

        if (strpos($surveyTitle, 'Kepuasan') !== false) {
            $questions = [
                [
                    'question_text' => 'Seberapa puas Anda dengan layanan SPK secara keseluruhan?',
                    'question_type' => 'rating',
                    'options' => null,
                    'is_required' => 1,
                    'order_number' => 1
                ],
                [
                    'question_text' => 'Layanan apa saja yang pernah Anda gunakan?',
                    'question_type' => 'checkbox',
                    'options' => json_encode(['Konsultasi Hukum', 'Advokasi', 'Pelatihan', 'Bantuan Kesehatan', 'Bantuan Pendidikan']),
                    'is_required' => 1,
                    'order_number' => 2
                ],
                [
                    'question_text' => 'Bagaimana Anda menilai kualitas komunikasi pengurus SPK?',
                    'question_type' => 'radio',
                    'options' => json_encode(['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang']),
                    'is_required' => 1,
                    'order_number' => 3
                ],
                [
                    'question_text' => 'Apa saran Anda untuk meningkatkan layanan SPK?',
                    'question_type' => 'textarea',
                    'options' => null,
                    'is_required' => 0,
                    'order_number' => 4,
                    'placeholder' => 'Tuliskan saran dan masukan Anda...',
                    'min_length' => 10,
                    'max_length' => 1000
                ],
                [
                    'question_text' => 'Apakah Anda akan merekomendasikan SPK kepada rekan kerja?',
                    'question_type' => 'radio',
                    'options' => json_encode(['Ya, pasti', 'Mungkin', 'Tidak yakin', 'Tidak']),
                    'is_required' => 1,
                    'order_number' => 5
                ],
                [
                    'question_text' => 'Berapa lama Anda telah menjadi anggota SPK?',
                    'question_type' => 'dropdown',
                    'options' => json_encode(['Kurang dari 1 tahun', '1-2 tahun', '2-5 tahun', 'Lebih dari 5 tahun']),
                    'is_required' => 1,
                    'order_number' => 6
                ]
            ];
        } elseif (strpos($surveyTitle, 'Pelatihan') !== false) {
            $questions = [
                [
                    'question_text' => 'Pelatihan apa yang Anda ikuti?',
                    'question_type' => 'dropdown',
                    'options' => json_encode(['Leadership', 'Komunikasi Efektif', 'Negosiasi', 'Hukum Ketenagakerjaan', 'Digital Skills']),
                    'is_required' => 1,
                    'order_number' => 1
                ],
                [
                    'question_text' => 'Bagaimana penilaian Anda terhadap materi pelatihan?',
                    'question_type' => 'rating',
                    'options' => null,
                    'is_required' => 1,
                    'order_number' => 2,
                    'min_value' => 1,
                    'max_value' => 5
                ],
                [
                    'question_text' => 'Bagaimana penilaian Anda terhadap instruktur/fasilitator?',
                    'question_type' => 'rating',
                    'options' => null,
                    'is_required' => 1,
                    'order_number' => 3,
                    'min_value' => 1,
                    'max_value' => 5
                ],
                [
                    'question_text' => 'Apakah waktu pelaksanaan pelatihan sudah sesuai?',
                    'question_type' => 'radio',
                    'options' => json_encode(['Sangat Sesuai', 'Sesuai', 'Kurang Sesuai', 'Tidak Sesuai']),
                    'is_required' => 1,
                    'order_number' => 4
                ],
                [
                    'question_text' => 'Apa yang paling bermanfaat dari pelatihan ini?',
                    'question_type' => 'textarea',
                    'options' => null,
                    'is_required' => 0,
                    'order_number' => 5,
                    'placeholder' => 'Ceritakan manfaat yang Anda peroleh...',
                    'max_length' => 500
                ],
                [
                    'question_text' => 'Topik pelatihan apa yang Anda harapkan di masa mendatang?',
                    'question_type' => 'text',
                    'options' => null,
                    'is_required' => 0,
                    'order_number' => 6,
                    'placeholder' => 'Sebutkan topik pelatihan yang diinginkan'
                ]
            ];
        } else {
            // Survei Kondisi Kerja
            $questions = [
                [
                    'question_text' => 'Bagaimana kondisi lingkungan kerja Anda saat ini?',
                    'question_type' => 'radio',
                    'options' => json_encode(['Sangat Baik', 'Baik', 'Cukup', 'Buruk', 'Sangat Buruk']),
                    'is_required' => 1,
                    'order_number' => 1
                ],
                [
                    'question_text' => 'Berapa jam kerja Anda per minggu?',
                    'question_type' => 'number',
                    'options' => null,
                    'is_required' => 1,
                    'order_number' => 2,
                    'min_value' => 0,
                    'max_value' => 100,
                    'placeholder' => 'Masukkan angka (contoh: 40)'
                ],
                [
                    'question_text' => 'Apakah beban kerja Anda proporsional dengan kompensasi yang diterima?',
                    'question_type' => 'scale',
                    'options' => null,
                    'is_required' => 1,
                    'order_number' => 3,
                    'min_value' => 1,
                    'max_value' => 10,
                    'help_text' => '1 = Sangat Tidak Proporsional, 10 = Sangat Proporsional'
                ],
                [
                    'question_text' => 'Fasilitas apa saja yang tersedia di tempat kerja Anda?',
                    'question_type' => 'checkbox',
                    'options' => json_encode(['Asuransi Kesehatan', 'Transportasi', 'Makan Siang', 'Ruang Istirahat', 'Fasilitas Olahraga', 'Parkir', 'WiFi', 'Perpustakaan']),
                    'is_required' => 0,
                    'order_number' => 4
                ],
                [
                    'question_text' => 'Apakah Anda pernah mengalami diskriminasi di tempat kerja?',
                    'question_type' => 'radio',
                    'options' => json_encode(['Tidak Pernah', 'Jarang', 'Kadang-kadang', 'Sering', 'Sangat Sering']),
                    'is_required' => 1,
                    'order_number' => 5
                ],
                [
                    'question_text' => 'Jika pernah mengalami diskriminasi, mohon ceritakan pengalaman Anda',
                    'question_type' => 'textarea',
                    'options' => null,
                    'is_required' => 0,
                    'order_number' => 6,
                    'placeholder' => 'Cerita Anda akan dijaga kerahasiaannya',
                    'help_text' => 'Informasi ini akan membantu kami memberikan advokasi yang lebih baik'
                ],
                [
                    'question_text' => 'Bagaimana tingkat stress kerja Anda?',
                    'question_type' => 'rating',
                    'options' => null,
                    'is_required' => 1,
                    'order_number' => 7,
                    'min_value' => 1,
                    'max_value' => 5,
                    'help_text' => '1 = Sangat Rendah, 5 = Sangat Tinggi'
                ],
                [
                    'question_text' => 'Tanggal lahir Anda',
                    'question_type' => 'date',
                    'options' => null,
                    'is_required' => 0,
                    'order_number' => 8
                ],
                [
                    'question_text' => 'Upload dokumen pendukung (jika ada)',
                    'question_type' => 'file',
                    'options' => null,
                    'is_required' => 0,
                    'order_number' => 9,
                    'help_text' => 'Format: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)'
                ],
                [
                    'question_text' => 'Email alternatif untuk kontak',
                    'question_type' => 'email',
                    'options' => null,
                    'is_required' => 0,
                    'order_number' => 10,
                    'placeholder' => 'email@example.com'
                ],
                [
                    'question_text' => 'Nomor telepon untuk kontak darurat',
                    'question_type' => 'phone',
                    'options' => null,
                    'is_required' => 0,
                    'order_number' => 11,
                    'placeholder' => '08xxxxxxxxxx'
                ]
            ];
        }

        // Insert questions
        foreach ($questions as $question) {
            $question['survey_id'] = $surveyId;
            $question['created_at'] = date('Y-m-d H:i:s');
            $question['updated_at'] = date('Y-m-d H:i:s');

            $this->db->table('survey_questions')->insert($question);
        }

        // Add some sample responses for the third survey (yang sudah berjalan)
        if (strpos($surveyTitle, 'Kondisi Kerja') !== false) {
            $this->createSampleResponses($surveyId);
        }
    }

    private function createSampleResponses($surveyId)
    {
        // Get questions for this survey
        $questions = $this->db->table('survey_questions')
            ->where('survey_id', $surveyId)
            ->get()
            ->getResultArray();

        // Create 5 sample responses
        for ($i = 1; $i <= 5; $i++) {
            // Create response
            $responseData = [
                'survey_id' => $surveyId,
                'member_id' => $i, // Assuming member IDs 1-5 exist
                'ip_address' => '192.168.1.' . $i,
                'user_agent' => 'Mozilla/5.0 Sample Browser',
                'started_at' => date('Y-m-d H:i:s', strtotime('-' . (10 - $i) . ' days')),
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-' . (10 - $i) . ' days +30 minutes')),
                'is_complete' => 1,
                'completion_time' => 1800 // 30 minutes in seconds
            ];

            $this->db->table('survey_responses')->insert($responseData);
            $responseId = $this->db->insertID();

            // Create answers for each question
            foreach ($questions as $question) {
                $answerData = [
                    'response_id' => $responseId,
                    'question_id' => $question['id'],
                    'answer_text' => $this->generateSampleAnswer($question),
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . (10 - $i) . ' days +30 minutes'))
                ];

                $this->db->table('survey_answers')->insert($answerData);
            }
        }
    }

    private function generateSampleAnswer($question)
    {
        switch ($question['question_type']) {
            case 'radio':
            case 'dropdown':
                $options = json_decode($question['options'], true);
                return $options[array_rand($options)];

            case 'checkbox':
                $options = json_decode($question['options'], true);
                $selected = array_rand(array_flip($options), rand(1, min(3, count($options))));
                if (!is_array($selected)) $selected = [$selected];
                return json_encode($selected);

            case 'rating':
                return (string) rand(3, 5);

            case 'scale':
                return (string) rand($question['min_value'] ?? 1, $question['max_value'] ?? 10);

            case 'number':
                return (string) rand(30, 60);

            case 'text':
                $samples = [
                    'Peningkatan sistem manajemen',
                    'Digitalisasi proses kerja',
                    'Pelatihan soft skills',
                    'Workshop kepemimpinan',
                    'Seminar hukum ketenagakerjaan'
                ];
                return $samples[array_rand($samples)];

            case 'textarea':
                $samples = [
                    'Lingkungan kerja sudah cukup baik, namun perlu peningkatan dalam hal fasilitas kesehatan.',
                    'Komunikasi antar divisi perlu ditingkatkan untuk koordinasi yang lebih baik.',
                    'Apresiasi terhadap kinerja karyawan sudah baik dan memotivasi.',
                    'Beban kerja cukup proporsional dengan kompensasi yang diterima.',
                    'Fasilitas kerja memadai dan mendukung produktivitas.'
                ];
                return $samples[array_rand($samples)];

            case 'date':
                return date('Y-m-d', strtotime('-' . rand(20, 50) . ' years'));

            case 'email':
                return 'alternative' . rand(1, 100) . '@example.com';

            case 'phone':
                return '08' . rand(100000000, 999999999);

            default:
                return '';
        }
    }
}
