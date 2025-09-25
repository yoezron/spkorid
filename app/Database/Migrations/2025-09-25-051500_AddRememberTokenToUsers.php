<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberTokenToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'remember_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'locked_until',
            ],
            'remember_expires' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => 'remember_token',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['remember_token', 'remember_expires']);
    }
}
