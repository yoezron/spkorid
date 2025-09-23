<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key check sementara
        $this->db->disableForeignKeyChecks();

        // 1. Data untuk tabel users (tanpa member_id dulu)
        $userData = [
            'username'           => 'superadmin',
            'email'              => 'admin@spk.com',
            // Ganti 'password123' dengan password yang Anda inginkan
            'password'           => password_hash('isman123', PASSWORD_DEFAULT),
            'role_id'            => 1, // ID untuk super_admin
            'is_active'          => 1,
            'is_verified'        => 1,
            'email_verified_at'  => date('Y-m-d H:i:s'),
        ];

        // Masukkan data user dan ambil ID-nya
        $this->db->table('users')->insert($userData);
        $userId = $this->db->insertID();

        // 2. Data untuk tabel members
        $memberData = [
            'nama_lengkap'        => 'Admin Utama',
            'email'               => 'admin@spk.com',
            'jenis_kelamin'       => 'Laki-laki',
            'alamat_lengkap'      => 'Kantor Pusat',
            'nomor_whatsapp'      => '08123456789',
            'status_keanggotaan'  => 'active',
            'tanggal_bergabung'   => date('Y-m-d'),
            'tanggal_verifikasi'  => date('Y-m-d H:i:s'),
            'verified_by'         => $userId, // Gunakan ID user yang baru dibuat
        ];

        // Masukkan data member dan ambil ID-nya
        $this->db->table('members')->insert($memberData);
        $memberId = $this->db->insertID();

        // 3. Update tabel user dengan member_id yang benar
        $this->db->table('users')->where('id', $userId)->update(['member_id' => $memberId]);

        // Aktifkan kembali foreign key check
        $this->db->enableForeignKeyChecks();

        echo "Super Admin account created successfully!\n";
    }
}
