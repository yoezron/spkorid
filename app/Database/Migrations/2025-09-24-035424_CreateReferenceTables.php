<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReferenceTables extends Migration
{
    public function up()
    {
        // Tabel ref_status_kepegawaian
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_status'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan'   => ['type' => 'TEXT', 'null' => true],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'   => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_status_kepegawaian');

        // Tabel ref_pemberi_gaji
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_pemberi' => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan'   => ['type' => 'TEXT', 'null' => true],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'   => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_pemberi_gaji');

        // Tabel ref_range_gaji
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'range_gaji' => ['type' => 'VARCHAR', 'constraint' => 100],
            'min_gaji'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'max_gaji'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_range_gaji');

        // Tabel ref_provinsi
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_provinsi' => ['type' => 'VARCHAR', 'constraint' => 100],
            'kode_provinsi' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_provinsi');

        // Tabel ref_kota
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'provinsi_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'nama_kota'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'kode_kota'   => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('provinsi_id', 'ref_provinsi', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('ref_kota');

        // Tabel ref_jenis_pt
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_jenis' => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_jenis_pt');

        // Tabel ref_kampus
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'jenis_pt_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'kode_pt'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'nama_kampus' => ['type' => 'VARCHAR', 'constraint' => 200],
            'alamat'      => ['type' => 'TEXT', 'null' => true],
            'kota_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'website'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'  => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jenis_pt_id', 'ref_jenis_pt', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('kota_id', 'ref_kota', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('ref_kampus');

        // Tabel ref_prodi
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kampus_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'kode_prodi' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'nama_prodi' => ['type' => 'VARCHAR', 'constraint' => 200],
            'jenjang'    => ['type' => 'ENUM', 'constraint' => ['D3', 'D4', 'S1', 'S2', 'S3', 'Profesi', 'Spesialis'], 'default' => 'S1'],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kampus_id', 'ref_kampus', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('ref_prodi');
    }

    public function down()
    {
        // Urutan drop harus terbalik dari create
        $this->forge->dropTable('ref_prodi');
        $this->forge->dropTable('ref_kampus');
        $this->forge->dropTable('ref_jenis_pt');
        $this->forge->dropTable('ref_kota');
        $this->forge->dropTable('ref_provinsi');
        $this->forge->dropTable('ref_range_gaji');
        $this->forge->dropTable('ref_pemberi_gaji');
        $this->forge->dropTable('ref_status_kepegawaian');
    }
}
