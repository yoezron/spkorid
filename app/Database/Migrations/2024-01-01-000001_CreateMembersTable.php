// app/Database/Migrations/2024-01-01-000001_CreateMembersTable.php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMembersTable extends Migration
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
            'nomor_anggota' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
                'null' => true,
            ],
            'nama_lengkap' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'jenis_kelamin' => [
                'type' => 'ENUM',
                'constraint' => ['L', 'P'],
            ],
            'alamat_lengkap' => [
                'type' => 'TEXT',
            ],
            'nomor_whatsapp' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
            ],
            'status_kepegawaian_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'pemberi_gaji_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'range_gaji_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'gaji_pokok' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'provinsi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'kota_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nidn_nip' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'jenis_pt_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'kampus_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'prodi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'bidang_keahlian' => [
                'type' => 'TEXT',
            ],
            'motivasi_berserikat' => [
                'type' => 'TEXT',
            ],
            'media_sosial' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'foto_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'bukti_pembayaran_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status_keanggotaan' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'active', 'suspended', 'inactive'],
                'default' => 'pending',
            ],
            'tanggal_bergabung' => [
                'type' => 'DATE',
                'null' => true,
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
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('status_kepegawaian_id', 'ref_status_kepegawaian', 'id');
        $this->forge->addForeignKey('pemberi_gaji_id', 'ref_pemberi_gaji', 'id');
        $this->forge->addForeignKey('range_gaji_id', 'ref_range_gaji', 'id');
        $this->forge->addForeignKey('provinsi_id', 'ref_provinsi', 'id');
        $this->forge->addForeignKey('kota_id', 'ref_kota', 'id');
        $this->forge->addForeignKey('jenis_pt_id', 'ref_jenis_pt', 'id');
        $this->forge->addForeignKey('kampus_id', 'ref_kampus', 'id');
        $this->forge->addForeignKey('prodi_id', 'ref_prodi', 'id');

        // Add indexes for better performance
        $this->forge->addKey('email');
        $this->forge->addKey('nomor_anggota');
        $this->forge->addKey('status_keanggotaan');
        $this->forge->addKey('created_at');

        $this->forge->createTable('members');
    }

    public function down()
    {
        $this->forge->dropTable('members');
    }
}
