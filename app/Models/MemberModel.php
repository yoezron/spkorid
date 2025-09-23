// app/Models/MemberModel.php
<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
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
        'card_path',
        'status_keanggotaan',
        'tanggal_bergabung',
        'verified_by',
        'verified_at',
        'suspended_at',
        'suspended_reason'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|is_unique[members.email,id,{id}]',
        'jenis_kelamin' => 'required|in_list[L,P]',
        'alamat_lengkap' => 'required|min_length[10]',
        'nomor_whatsapp' => 'required|regex_match[/^(\+62|62|0)8[1-9][0-9]{6,11}$/]',
        'status_kepegawaian_id' => 'required|numeric',
        'pemberi_gaji_id' => 'required|numeric',
        'range_gaji_id' => 'required|numeric',
        'gaji_pokok' => 'required|numeric',
        'provinsi_id' => 'required|numeric',
        'kota_id' => 'required|numeric',
        'jenis_pt_id' => 'required|numeric',
        'kampus_id' => 'required|numeric',
        'prodi_id' => 'required|numeric'
    ];

    protected $validationMessages = [
        'nama_lengkap' => [
            'required' => 'Nama lengkap wajib diisi',
            'min_length' => 'Nama lengkap minimal 3 karakter',
            'max_length' => 'Nama lengkap maksimal 100 karakter'
        ],
        'email' => [
            'required' => 'Email wajib diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah terdaftar'
        ]
    ];

    protected $beforeInsert = ['generateNomorAnggota'];
    protected $afterInsert = ['createInitialPayment'];

    /**
     * Generate nomor anggota before insert
     */
    protected function generateNomorAnggota(array $data)
    {
        if (!isset($data['data']['nomor_anggota'])) {
            helper('text');
            $year = date('Y');
            $month = date('m');

            $lastMember = $this->select('nomor_anggota')
                ->like('nomor_anggota', "SPK/{$year}/{$month}/", 'after')
                ->orderBy('id', 'DESC')
                ->first();

            if ($lastMember && $lastMember['nomor_anggota']) {
                $parts = explode('/', $lastMember['nomor_anggota']);
                $lastSequence = isset($parts[3]) ? intval($parts[3]) : 0;
                $nextSequence = $lastSequence + 1;
            } else {
                $nextSequence = 1;
            }

            $data['data']['nomor_anggota'] = sprintf('SPK/%04d/%02d/%05d', $year, $month, $nextSequence);
        }

        return $data;
    }

    /**
     * Create initial payment record after member insert
     */
    protected function createInitialPayment(array $data)
    {
        if ($data['result']) {
            $paymentModel = new PaymentHistoryModel();

            $paymentData = [
                'member_id' => $data['id'],
                'nomor_transaksi' => 'TRX-' . date('YmdHis') . '-' . $data['id'],
                'jenis_pembayaran' => 'iuran_pertama',
                'periode_bulan' => date('n'),
                'periode_tahun' => date('Y'),
                'jumlah' => 100000, // Get from settings
                'metode_pembayaran' => 'transfer',
                'bukti_pembayaran' => $data['data']['bukti_pembayaran_path'] ?? null,
                'tanggal_pembayaran' => date('Y-m-d H:i:s'),
                'status_pembayaran' => 'pending'
            ];

            $paymentModel->insert($paymentData);
        }

        return $data;
    }

    /**
     * Get member with all related data
     */
    public function getMemberWithDetails($id)
    {
        return $this->select('members.*, 
                             sk.nama as status_kepegawaian,
                             pg.nama as pemberi_gaji,
                             rg.range as range_gaji,
                             prov.nama as provinsi,
                             kota.nama as kota,
                             jpt.nama as jenis_pt,
                             kampus.nama as nama_kampus,
                             prodi.nama as nama_prodi,
                             users.username, users.email as user_email,
                             users.last_login, users.is_active as user_active')
            ->join('ref_status_kepegawaian sk', 'sk.id = members.status_kepegawaian_id', 'left')
            ->join('ref_pemberi_gaji pg', 'pg.id = members.pemberi_gaji_id', 'left')
            ->join('ref_range_gaji rg', 'rg.id = members.range_gaji_id', 'left')
            ->join('ref_provinsi prov', 'prov.id = members.provinsi_id', 'left')
            ->join('ref_kota kota', 'kota.id = members.kota_id', 'left')
            ->join('ref_jenis_pt jpt', 'jpt.id = members.jenis_pt_id', 'left')
            ->join('ref_kampus kampus', 'kampus.id = members.kampus_id', 'left')
            ->join('ref_prodi prodi', 'prodi.id = members.prodi_id', 'left')
            ->join('users', 'users.member_id = members.id', 'left')
            ->where('members.id', $id)
            ->first();
    }

    /**
     * Get active members with pagination
     */
    public function getActiveMembers($limit = 10, $offset = 0)
    {
        return $this->select('members.*, kampus.nama as nama_kampus')
            ->join('ref_kampus kampus', 'kampus.id = members.kampus_id', 'left')
            ->where('status_keanggotaan', 'active')
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    }

    /**
     * Get pending members for verification
     */
    public function getPendingMembers()
    {
        return $this->select('members.*, kampus.nama as nama_kampus')
            ->join('ref_kampus kampus', 'kampus.id = members.kampus_id', 'left')
            ->where('status_keanggotaan', 'pending')
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Verify member
     */
    public function verifyMember($id, $verifiedBy, $notes = '')
    {
        $data = [
            'status_keanggotaan' => 'active',
            'verified_by' => $verifiedBy,
            'verified_at' => date('Y-m-d H:i:s'),
            'tanggal_bergabung' => date('Y-m-d')
        ];

        $result = $this->update($id, $data);

        if ($result) {
            // Update user status
            $userModel = new UserModel();
            $user = $userModel->where('member_id', $id)->first();

            if ($user) {
                $userModel->update($user['id'], [
                    'is_active' => 1,
                    'is_verified' => 1
                ]);
            }

            // Update payment status if exists
            $paymentModel = new PaymentHistoryModel();
            $payment = $paymentModel->where('member_id', $id)
                ->where('jenis_pembayaran', 'iuran_pertama')
                ->where('status_pembayaran', 'pending')
                ->first();

            if ($payment) {
                $paymentModel->update($payment['id'], [
                    'status_pembayaran' => 'verified',
                    'verified_by' => $verifiedBy,
                    'verified_at' => date('Y-m-d H:i:s'),
                    'catatan' => $notes
                ]);
            }
        }

        return $result;
    }

    /**
     * Get member statistics
     */
    public function getMemberStatistics()
    {
        $stats = [];

        // Total members by status
        $stats['total_active'] = $this->where('status_keanggotaan', 'active')->countAllResults(false);
        $stats['total_pending'] = $this->where('status_keanggotaan', 'pending')->countAllResults(false);
        $stats['total_suspended'] = $this->where('status_keanggotaan', 'suspended')->countAllResults(false);
        $stats['total_inactive'] = $this->where('status_keanggotaan', 'inactive')->countAllResults(false);

        // Members by month (last 12 months)
        $stats['monthly_data'] = [];
        $stats['monthly_labels'] = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-{$i} months"));
            $stats['monthly_labels'][] = date('M Y', strtotime("-{$i} months"));

            $count = $this->where('DATE_FORMAT(created_at, "%Y-%m")', $date)
                ->where('status_keanggotaan', 'active')
                ->countAllResults(false);

            $stats['monthly_data'][] = $count;
        }

        // Members by status kepegawaian
        $statusKepegawaian = $this->select('sk.nama, COUNT(members.id) as total')
            ->join('ref_status_kepegawaian sk', 'sk.id = members.status_kepegawaian_id')
            ->where('members.status_keanggotaan', 'active')
            ->groupBy('sk.id')
            ->findAll();

        $stats['status_labels'] = array_column($statusKepegawaian, 'nama');
        $stats['status_data'] = array_column($statusKepegawaian, 'total');

        // Members by kampus (top 10)
        $stats['top_kampus'] = $this->select('kampus.nama, COUNT(members.id) as total')
            ->join('ref_kampus kampus', 'kampus.id = members.kampus_id')
            ->where('members.status_keanggotaan', 'active')
            ->groupBy('kampus.id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->findAll();

        return $stats;
    }

    /**
     * Search members
     */
    public function searchMembers($keyword, $filters = [])
    {
        $builder = $this->table($this->table);

        if ($keyword) {
            $builder->groupStart()
                ->like('nama_lengkap', $keyword)
                ->orLike('email', $keyword)
                ->orLike('nomor_anggota', $keyword)
                ->orLike('nidn_nip', $keyword)
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('status_keanggotaan', $filters['status']);
        }

        if (!empty($filters['kampus_id'])) {
            $builder->where('kampus_id', $filters['kampus_id']);
        }

        if (!empty($filters['status_kepegawaian_id'])) {
            $builder->where('status_kepegawaian_id', $filters['status_kepegawaian_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to']);
        }

        return $builder->paginate(20);
    }

    /**
     * Export members to CSV
     */
    public function exportToCSV($filters = [])
    {
        $members = $this->searchMembers('', $filters);

        $csvData = [];
        $csvData[] = [
            'Nomor Anggota',
            'Nama Lengkap',
            'Email',
            'Jenis Kelamin',
            'No. WhatsApp',
            'NIDN/NIP',
            'Status Kepegawaian',
            'Kampus',
            'Program Studi',
            'Status',
            'Tanggal Bergabung'
        ];

        foreach ($members as $member) {
            $csvData[] = [
                $member['nomor_anggota'],
                $member['nama_lengkap'],
                $member['email'],
                $member['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan',
                $member['nomor_whatsapp'],
                $member['nidn_nip'],
                $member['status_kepegawaian'] ?? '-',
                $member['nama_kampus'] ?? '-',
                $member['nama_prodi'] ?? '-',
                $member['status_keanggotaan'],
                $member['tanggal_bergabung'] ?? '-'
            ];
        }

        return $csvData;
    }
}
