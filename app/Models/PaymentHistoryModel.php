<?php
// ============================================
// MODEL UNTUK PAYMENT & IURAN
// ============================================

// app/Models/PaymentHistoryModel.php
namespace App\Models;

use CodeIgniter\Model;

class PaymentHistoryModel extends Model
{
    protected $table = 'payment_history';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'member_id',
        'nomor_transaksi',
        'jenis_pembayaran',
        'periode_bulan',
        'periode_tahun',
        'jumlah',
        'metode_pembayaran',
        'bukti_pembayaran',
        'tanggal_pembayaran',
        'status_pembayaran',
        'verified_by',
        'verified_at',
        'catatan'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get payment history for a member
    public function getMemberPayments($memberId)
    {
        return $this->where('member_id', $memberId)
            ->orderBy('tanggal_pembayaran', 'DESC')
            ->findAll();
    }

    // Get pending payments
    public function getPendingPayments()
    {
        return $this->select('payment_history.*, members.nama_lengkap, members.nomor_anggota')
            ->join('members', 'members.id = payment_history.member_id')
            ->where('payment_history.status_pembayaran', 'pending')
            ->orderBy('payment_history.created_at', 'DESC')
            ->findAll();
    }

    // Verify payment
    public function verifyPayment($paymentId, $verifiedBy, $notes = '')
    {
        return $this->update($paymentId, [
            'status_pembayaran' => 'verified',
            'verified_by' => $verifiedBy,
            'verified_at' => date('Y-m-d H:i:s'),
            'catatan' => $notes
        ]);
    }

    // Check if member has paid for specific period
    public function hasPaidForPeriod($memberId, $month, $year)
    {
        $payment = $this->where('member_id', $memberId)
            ->where('periode_bulan', $month)
            ->where('periode_tahun', $year)
            ->where('status_pembayaran', 'verified')
            ->first();

        return $payment !== null;
    }

    // Get payment summary
    public function getPaymentSummary($startDate = null, $endDate = null)
    {
        $builder = $this->select('
                            COUNT(id) as total_transactions,
                            SUM(CASE WHEN status_pembayaran = "verified" THEN jumlah ELSE 0 END) as total_verified,
                            SUM(CASE WHEN status_pembayaran = "pending" THEN jumlah ELSE 0 END) as total_pending,
                            COUNT(CASE WHEN status_pembayaran = "pending" THEN 1 END) as count_pending
                         ');

        if ($startDate && $endDate) {
            $builder->where('tanggal_pembayaran >=', $startDate)
                ->where('tanggal_pembayaran <=', $endDate);
        }

        return $builder->first();
    }

    // Get payments by date range with member and verifier names
    public function getPaymentsByDateRange($startDate, $endDate)
    {
        return $this->select('payment_history.*, m.nama_lengkap, u.username as verifier_name')
            ->join('members m', 'm.id = payment_history.member_id', 'left')
            ->join('users u', 'u.id = payment_history.verified_by', 'left')
            ->where('payment_history.tanggal_pembayaran >=', $startDate . ' 00:00:00')
            ->where('payment_history.tanggal_pembayaran <=', $endDate . ' 23:59:59')
            ->orderBy('payment_history.tanggal_pembayaran', 'DESC')
            ->findAll();
    }
}
