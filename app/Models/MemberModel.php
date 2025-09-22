<?php

// ============================================
// MODEL UNTUK DATA ANGGOTA
// ============================================

// app/Models/MemberModel.php
namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'nomor_anggota',
        'nama_lengkap',
        'email',
        'jenis_kelamin',
        'alamat_lengkap',
        'nomor_whatsapp',
        'status_kepegawaian_id',
        'pemberi_gaji_id',
        'range_gaji_id',
        'gaji_pokok',
        'provinsi_id',
        'kota_id',
        'nidn_nip',
        'jenis_pt_id',
        'kampus_id',
        'prodi_id',
        'bidang_keahlian',
        'motivasi_berserikat',
        'media_sosial',
        'foto_path',
        'bukti_pembayaran_path',
        'status_keanggotaan',
        'tanggal_bergabung',
        'tanggal_verifikasi',
        'verified_by',
        'catatan_verifikasi'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get member with all related data
    public function getMemberWithDetails($memberId)
    {
        return $this->select('members.*, 
                             sk.nama_status as status_kepegawaian,
                             pg.nama_pemberi as pemberi_gaji,
                             rg.range_gaji,
                             p.nama_provinsi,
                             k.nama_kota,
                             jpt.nama_jenis as jenis_pt,
                             kmp.nama_kampus,
                             pr.nama_prodi,
                             pr.jenjang,
                             u.email as user_email,
                             u.is_active as user_active,
                             u.last_login')
            ->join('ref_status_kepegawaian sk', 'sk.id = members.status_kepegawaian_id', 'left')
            ->join('ref_pemberi_gaji pg', 'pg.id = members.pemberi_gaji_id', 'left')
            ->join('ref_range_gaji rg', 'rg.id = members.range_gaji_id', 'left')
            ->join('ref_provinsi p', 'p.id = members.provinsi_id', 'left')
            ->join('ref_kota k', 'k.id = members.kota_id', 'left')
            ->join('ref_jenis_pt jpt', 'jpt.id = members.jenis_pt_id', 'left')
            ->join('ref_kampus kmp', 'kmp.id = members.kampus_id', 'left')
            ->join('ref_prodi pr', 'pr.id = members.prodi_id', 'left')
            ->join('users u', 'u.member_id = members.id', 'left')
            ->where('members.id', $memberId)
            ->first();
    }

    // Get pending members for verification
    public function getPendingMembers()
    {
        return $this->select('members.*, k.nama_kampus, pr.nama_prodi')
            ->join('ref_kampus k', 'k.id = members.kampus_id', 'left')
            ->join('ref_prodi pr', 'pr.id = members.prodi_id', 'left')
            ->where('members.status_keanggotaan', 'pending')
            ->orderBy('members.created_at', 'DESC')
            ->findAll();
    }

    // Get active members
    public function getActiveMembers($limit = null, $offset = null)
    {
        $builder = $this->select('members.*, k.nama_kampus, pr.nama_prodi, u.last_login')
            ->join('ref_kampus k', 'k.id = members.kampus_id', 'left')
            ->join('ref_prodi pr', 'pr.id = members.prodi_id', 'left')
            ->join('users u', 'u.member_id = members.id', 'left')
            ->where('members.status_keanggotaan', 'active')
            ->orderBy('members.nomor_anggota', 'ASC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    // Search members
    public function searchMembers($keyword)
    {
        return $this->select('members.*, k.nama_kampus')
            ->join('ref_kampus k', 'k.id = members.kampus_id', 'left')
            ->groupStart()
            ->like('members.nama_lengkap', $keyword)
            ->orLike('members.nomor_anggota', $keyword)
            ->orLike('members.email', $keyword)
            ->orLike('members.nidn_nip', $keyword)
            ->orLike('k.nama_kampus', $keyword)
            ->groupEnd()
            ->findAll();
    }

    // Get member statistics
    public function getMemberStatistics()
    {
        $db = \Config\Database::connect();

        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('status_keanggotaan', 'active')->countAllResults(),
            'pending' => $this->where('status_keanggotaan', 'pending')->countAllResults(),
            'suspended' => $this->where('status_keanggotaan', 'suspended')->countAllResults(),
            'male' => $this->where('jenis_kelamin', 'Laki-laki')->countAllResults(),
            'female' => $this->where('jenis_kelamin', 'Perempuan')->countAllResults()
        ];

        // Statistics by kampus
        $stats['by_kampus'] = $db->table('members')
            ->select('k.nama_kampus, COUNT(members.id) as total')
            ->join('ref_kampus k', 'k.id = members.kampus_id')
            ->groupBy('members.kampus_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Statistics by status kepegawaian
        $stats['by_status'] = $db->table('members')
            ->select('sk.nama_status, COUNT(members.id) as total')
            ->join('ref_status_kepegawaian sk', 'sk.id = members.status_kepegawaian_id')
            ->groupBy('members.status_kepegawaian_id')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        return $stats;
    }

    // Verify member
    public function verifyMember($memberId, $verifiedBy, $notes = '')
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update member status
            $this->update($memberId, [
                'status_keanggotaan' => 'active',
                'tanggal_verifikasi' => date('Y-m-d H:i:s'),
                'verified_by' => $verifiedBy,
                'catatan_verifikasi' => $notes,
                'tanggal_bergabung' => date('Y-m-d')
            ]);

            // Activate user account
            $db->table('users')
                ->where('member_id', $memberId)
                ->update([
                    'is_active' => 1,
                    'is_verified' => 1,
                    'email_verified_at' => date('Y-m-d H:i:s')
                ]);

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }
}
