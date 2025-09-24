<?php
// app/Models/MemberModel.php
namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

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
        'catatan_verifikasi',
        'tanggal_suspend',
        'alasan_suspend',
        'tanggal_terminate',
        'alasan_terminate'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get member with all related data
     */
    public function getMemberComplete($id)
    {
        return $this->select('members.*, 
                             ref_status_kepegawaian.nama_status as status_kepegawaian,
                             ref_pemberi_gaji.nama_pemberi as pemberi_gaji,
                             ref_range_gaji.range_gaji,
                             ref_provinsi.nama_provinsi,
                             ref_kota.nama_kota,
                             ref_jenis_pt.nama_jenis as jenis_pt,
                             ref_kampus.nama_kampus,
                             ref_prodi.nama_prodi,
                             users.email as user_email,
                             users.is_active as user_active,
                             users.last_login')
            ->join('ref_status_kepegawaian', 'ref_status_kepegawaian.id = members.status_kepegawaian_id', 'left')
            ->join('ref_pemberi_gaji', 'ref_pemberi_gaji.id = members.pemberi_gaji_id', 'left')
            ->join('ref_range_gaji', 'ref_range_gaji.id = members.range_gaji_id', 'left')
            ->join('ref_provinsi', 'ref_provinsi.id = members.provinsi_id', 'left')
            ->join('ref_kota', 'ref_kota.id = members.kota_id', 'left')
            ->join('ref_jenis_pt', 'ref_jenis_pt.id = members.jenis_pt_id', 'left')
            ->join('ref_kampus', 'ref_kampus.id = members.kampus_id', 'left')
            ->join('ref_prodi', 'ref_prodi.id = members.prodi_id', 'left')
            ->join('users', 'users.member_id = members.id', 'left')
            ->where('members.id', $id)
            ->first();
    }

    /**
     * Get pending members for verification
     */
    public function getPendingMembers()
    {
        return $this->select('members.*, users.email as user_email')
            ->join('users', 'users.member_id = members.id', 'left')
            ->where('members.status_keanggotaan', 'pending')
            ->orderBy('members.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get active members
     */
    public function getActiveMembers($limit = null, $offset = null)
    {
        $builder = $this->select('members.*, 
                                 ref_kampus.nama_kampus,
                                 ref_prodi.nama_prodi,
                                 users.last_login')
            ->join('ref_kampus', 'ref_kampus.id = members.kampus_id', 'left')
            ->join('ref_prodi', 'ref_prodi.id = members.prodi_id', 'left')
            ->join('users', 'users.member_id = members.id', 'left')
            ->where('members.status_keanggotaan', 'active');

        if ($limit !== null && $offset !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }



    /**
     * Export members to array for Excel/CSV
     */
    public function exportMembers($filters = [])
    {
        $builder = $this->select('
            members.nomor_anggota,
            members.nama_lengkap,
            members.email,
            members.jenis_kelamin,
            members.nomor_whatsapp,
            ref_status_kepegawaian.nama_status as status_kepegawaian,
            ref_pemberi_gaji.nama_pemberi as pemberi_gaji,
            ref_kampus.nama_kampus,
            ref_prodi.nama_prodi,
            members.nidn_nip,
            members.status_keanggotaan,
            members.tanggal_bergabung
        ')
            ->join('ref_status_kepegawaian', 'ref_status_kepegawaian.id = members.status_kepegawaian_id', 'left')
            ->join('ref_pemberi_gaji', 'ref_pemberi_gaji.id = members.pemberi_gaji_id', 'left')
            ->join('ref_kampus', 'ref_kampus.id = members.kampus_id', 'left')
            ->join('ref_prodi', 'ref_prodi.id = members.prodi_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('members.status_keanggotaan', $filters['status']);
        }

        return $builder->orderBy('members.nama_lengkap', 'ASC')->findAll();
    }



    /**
     * Search members
     */
    public function searchMembers($keyword, $filters = [])
    {
        $builder = $this->select('members.*, 
                                 ref_kampus.nama_kampus,
                                 ref_prodi.nama_prodi')
            ->join('ref_kampus', 'ref_kampus.id = members.kampus_id', 'left')
            ->join('ref_prodi', 'ref_prodi.id = members.prodi_id', 'left');

        // Apply keyword search
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('members.nama_lengkap', $keyword)
                ->orLike('members.nomor_anggota', $keyword)
                ->orLike('members.email', $keyword)
                ->orLike('members.nidn_nip', $keyword)
                ->groupEnd();
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('members.status_keanggotaan', $filters['status']);
        }

        if (!empty($filters['kampus_id'])) {
            $builder->where('members.kampus_id', $filters['kampus_id']);
        }

        if (!empty($filters['provinsi_id'])) {
            $builder->where('members.provinsi_id', $filters['provinsi_id']);
        }

        if (!empty($filters['status_kepegawaian_id'])) {
            $builder->where('members.status_kepegawaian_id', $filters['status_kepegawaian_id']);
        }

        return $builder->orderBy('members.nama_lengkap', 'ASC')->findAll();
    }

    /**
     * Generate member number
     */
    public function generateNomorAnggota()
    {
        $year = date('Y');
        $month = date('m');

        // Get last member number for current year
        $lastMember = $this->select('nomor_anggota')
            ->like('nomor_anggota', "SPK{$year}", 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastMember && preg_match('/SPK' . $year . '(\d{5})/', $lastMember['nomor_anggota'], $matches)) {
            $lastNumber = intval($matches[1]);
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return "SPK{$year}{$newNumber}";
    }

    /**
     * Update member status
     */
    public function updateStatus($memberId, $status, $notes = null, $updatedBy = null)
    {
        $data = ['status_keanggotaan' => $status];

        switch ($status) {
            case 'active':
                $data['tanggal_verifikasi'] = date('Y-m-d H:i:s');
                $data['verified_by'] = $updatedBy;
                $data['catatan_verifikasi'] = $notes;
                break;

            case 'suspended':
                $data['tanggal_suspend'] = date('Y-m-d H:i:s');
                $data['alasan_suspend'] = $notes;
                break;

            case 'terminated':
                $data['tanggal_terminate'] = date('Y-m-d H:i:s');
                $data['alasan_terminate'] = $notes;
                break;
        }

        return $this->update($memberId, $data);
    }

    /**
     * Get member statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('status_keanggotaan', 'active')->countAllResults(),
            'pending' => $this->where('status_keanggotaan', 'pending')->countAllResults(),
            'suspended' => $this->where('status_keanggotaan', 'suspended')->countAllResults(),
            'terminated' => $this->where('status_keanggotaan', 'terminated')->countAllResults()
        ];

        // Get by gender
        $stats['by_gender'] = $this->select('jenis_kelamin, COUNT(*) as total')
            ->where('status_keanggotaan', 'active')
            ->groupBy('jenis_kelamin')
            ->findAll();

        // Get by kampus (top 10)
        $stats['by_kampus'] = $this->select('ref_kampus.nama_kampus, COUNT(*) as total')
            ->join('ref_kampus', 'ref_kampus.id = members.kampus_id', 'left')
            ->where('members.status_keanggotaan', 'active')
            ->groupBy('members.kampus_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->findAll();

        // Get by provinsi
        $stats['by_provinsi'] = $this->select('ref_provinsi.nama_provinsi, COUNT(*) as total')
            ->join('ref_provinsi', 'ref_provinsi.id = members.provinsi_id', 'left')
            ->where('members.status_keanggotaan', 'active')
            ->groupBy('members.provinsi_id')
            ->orderBy('total', 'DESC')
            ->findAll();

        // Get recent members
        $stats['recent'] = $this->where('status_keanggotaan', 'active')
            ->orderBy('tanggal_bergabung', 'DESC')
            ->limit(5)
            ->findAll();

        return $stats;
    }

    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Get members for bulk email
     */
    public function getMembersForEmail($filters = [])
    {
        $builder = $this->select('id, nama_lengkap, email')
            ->where('status_keanggotaan', 'active');

        if (!empty($filters['kampus_ids'])) {
            $builder->whereIn('kampus_id', $filters['kampus_ids']);
        }

        if (!empty($filters['provinsi_ids'])) {
            $builder->whereIn('provinsi_id', $filters['provinsi_ids']);
        }

        if (!empty($filters['status_kepegawaian_ids'])) {
            $builder->whereIn('status_kepegawaian_id', $filters['status_kepegawaian_ids']);
        }

        return $builder->findAll();
    }

    /**
     * Mengambil data detail anggota dengan menggabungkan tabel-tabel terkait.
     *
     * @param int $id ID Anggota
     * @return array|null
     */
    public function getMemberWithDetails(int $id)
    {
        return $this->select('
                members.*,
                rk.nama_kampus,
                rp.nama_prodi,
                rsk.nama_status as status_kepegawaian,
                u.username,
                u.is_active as user_status
            ')
            ->join('ref_kampus rk', 'rk.id = members.kampus_id', 'left')
            ->join('ref_prodi rp', 'rp.id = members.prodi_id', 'left')
            ->join('ref_status_kepegawaian rsk', 'rsk.id = members.status_kepegawaian_id', 'left')
            ->join('users u', 'u.member_id = members.id', 'left')
            ->where('members.id', $id)
            ->first();
    }
}
