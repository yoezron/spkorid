<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSurveyTables extends Migration
{
    public function up()
    {
        // Modify surveys table (add missing columns if not exist)
        $this->forge->addColumn('surveys', [
            'allow_multiple_submissions' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'is_anonymous'
            ],
            'require_login' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'allow_multiple_submissions'
            ],
            'show_results_to_participants' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'require_login'
            ],
            'randomize_questions' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'show_results_to_participants'
            ]
        ]);

        // Modify survey_questions table (add missing columns)
        $this->forge->addColumn('survey_questions', [
            'min_value' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'order_number'
            ],
            'max_value' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'min_value'
            ],
            'min_length' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'max_value'
            ],
            'max_length' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'min_length'
            ],
            'placeholder' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'max_length'
            ],
            'help_text' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'placeholder'
            ],
            'validation_rules' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'help_text'
            ],
            'conditional_logic' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'validation_rules'
            ]
        ]);

        // Modify survey_responses table (add missing columns)
        $this->forge->addColumn('survey_responses', [
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'after' => 'member_id'
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'ip_address'
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'user_agent'
            ],
            'is_complete' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'submitted_at'
            ],
            'completion_time' => [
                'type' => 'INT',
                'null' => true,
                'comment' => 'Time in seconds',
                'after' => 'is_complete'
            ]
        ]);

        // Modify survey_answers table (add missing columns)
        $this->forge->addColumn('survey_answers', [
            'answer_value' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'answer_text'
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'answer_value'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'on_update' => 'CURRENT_TIMESTAMP',
                'after' => 'created_at'
            ]
        ]);

        // Add indexes for better performance
        $this->forge->addKey('surveys', 'is_active');
        $this->forge->addKey('surveys', 'start_date');
        $this->forge->addKey('surveys', 'end_date');
        $this->forge->addKey('survey_questions', 'survey_id');
        $this->forge->addKey('survey_questions', 'order_number');
        $this->forge->addKey('survey_responses', 'is_complete');
        $this->forge->addKey('survey_responses', 'submitted_at');
    }

    public function down()
    {
        // Drop added columns
        $this->forge->dropColumn('surveys', [
            'allow_multiple_submissions',
            'require_login',
            'show_results_to_participants',
            'randomize_questions'
        ]);

        $this->forge->dropColumn('survey_questions', [
            'min_value',
            'max_value',
            'min_length',
            'max_length',
            'placeholder',
            'help_text',
            'validation_rules',
            'conditional_logic'
        ]);

        $this->forge->dropColumn('survey_responses', [
            'ip_address',
            'user_agent',
            'started_at',
            'is_complete',
            'completion_time'
        ]);

        $this->forge->dropColumn('survey_answers', [
            'answer_value',
            'file_path',
            'updated_at'
        ]);
    }
}
