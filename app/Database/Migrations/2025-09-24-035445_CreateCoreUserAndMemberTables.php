<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoreUserAndMemberTables extends Migration
{
    public function up()
    {
        // Tabel roles
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'role_name'        => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'role_description' => ['type' => 'TEXT', 'null' => true],
            'is_active'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'       => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'       => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles');

        // Tabel menus
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'menu_name'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'menu_url'   => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'menu_icon'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'parent_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'menu_order' => ['type' => 'INT', 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('menus');

        // Tabel members
        $this->forge->addField([
            'id'                      => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nomor_anggota'           => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true, 'null' => true],
            'nama_lengkap'            => ['type' => 'VARCHAR', 'constraint' => 200],
            'email'                   => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'jenis_kelamin'           => ['type' => 'ENUM', 'constraint' => ['Laki-laki', 'Perempuan']],
            'alamat_lengkap'          => ['type' => 'TEXT'],
            'nomor_whatsapp'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'status_kepegawaian_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'pemberi_gaji_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'range_gaji_id'           => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'gaji_pokok'              => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'provinsi_id'             => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'kota_id'                 => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'nidn_nip'                => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'jenis_pt_id'             => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'kampus_id'               => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'prodi_id'                => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'bidang_keahlian'         => ['type' => 'TEXT', 'null' => true],
            'motivasi_berserikat'     => ['type' => 'TEXT', 'null' => true],
            'media_sosial'            => ['type' => 'TEXT', 'null' => true],
            'foto_path'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bukti_pembayaran_path'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status_keanggotaan'      => ['type' => 'ENUM', 'constraint' => ['pending', 'active', 'suspended', 'terminated'], 'default' => 'pending'],
            'tanggal_bergabung'       => ['type' => 'DATE', 'null' => true],
            'tanggal_verifikasi'      => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'verified_by'             => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'catatan_verifikasi'      => ['type' => 'TEXT', 'null' => true],
            'created_at'              => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'              => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('status_kepegawaian_id', 'ref_status_kepegawaian', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('pemberi_gaji_id', 'ref_pemberi_gaji', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('range_gaji_id', 'ref_range_gaji', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('provinsi_id', 'ref_provinsi', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('kota_id', 'ref_kota', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('jenis_pt_id', 'ref_jenis_pt', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('kampus_id', 'ref_kampus', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('prodi_id', 'ref_prodi', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('members');

        // Tabel users
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'member_id'             => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'username'              => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true, 'null' => true],
            'email'                 => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'password'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'role_id'               => ['type' => 'INT', 'unsigned' => true, 'default' => 3],
            'is_active'             => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_verified'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'email_verified_at'     => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'verification_token'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'reset_token'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'reset_token_expires'   => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'last_login'            => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'last_activity'         => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'login_attempts'        => ['type' => 'INT', 'default' => 0],
            'locked_until'          => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'created_at'            => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'            => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'CASCADE', 'SET NULL'); // Foreign key di tabel members
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
        $this->forge->dropTable('members');
        $this->forge->dropTable('menus');
        $this->forge->dropTable('roles');
    }
}
