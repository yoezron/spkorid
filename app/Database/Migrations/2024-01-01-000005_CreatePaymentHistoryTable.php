// app/Database/Migrations/2024-01-01-000005_CreatePaymentHistoryTable.php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nomor_transaksi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'jenis_pembayaran' => [
                'type' => 'ENUM',
                'constraint' => ['iuran_bulanan', 'iuran_tahunan', 'iuran_pertama', 'sumbangan', 'lainnya'],
            ],
            'periode_bulan' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
            ],
            'periode_tahun' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true,
            ],
            'jumlah' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'metode_pembayaran' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'bukti_pembayaran' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'tanggal_pembayaran' => [
                'type' => 'DATETIME',
            ],
            'status_pembayaran' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'verified', 'rejected', 'expired'],
                'default' => 'pending',
            ],
            'verified_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addKey('nomor_transaksi');
        $this->forge->addKey('status_pembayaran');
        $this->forge->addKey('tanggal_pembayaran');
        $this->forge->createTable('payment_history');
    }

    public function down()
    {
        $this->forge->dropTable('payment_history');
    }
}
