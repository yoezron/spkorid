<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAllowMultipleSubmissionsToSurveys extends Migration
{
    public function up()
    {
        $fields = [
            'allow_multiple_submissions' => [
                'type'       => 'BOOLEAN', // atau 'TINYINT', '1'
                'default'    => false,     // Default-nya tidak diizinkan
                'null'       => false,
                'after'      => 'status',   // Letakkan setelah kolom 'status' (sesuaikan jika perlu)
            ],
        ];
        $this->forge->addColumn('surveys', $fields); // Asumsi nama tabelnya adalah 'surveys'
    }

    public function down()
    {
        $this->forge->dropColumn('surveys', 'allow_multiple_submissions');
    }
}
