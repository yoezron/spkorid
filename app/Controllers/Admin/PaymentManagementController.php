<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentHistoryModel;
use App\Models\MemberModel;
use CodeIgniter\I18n\Time;
use Exception;

class PaymentManagementController extends BaseController
{
    protected PaymentHistoryModel $paymentModel;
    protected MemberModel $memberModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentHistoryModel();
        $this->memberModel = new MemberModel();
        // Sebaiknya Anda melindungi controller ini dengan filter auth untuk admin/pengurus
        // misalnya: $this->middleware('auth:admin,pengurus');
    }

    /**
     * Menampilkan daftar pembayaran yang statusnya masih 'pending'.
     *
     * @return string
     */
    public function pending(): string
    {
        $data = [
            'title'    => 'Verifikasi Pembayaran - SPK',
            'payments' => $this->paymentModel->getPendingPaymentsWithMemberData(), // Asumsi method ini mengambil data pembayaran beserta data member
        ];

        return view('admin/payment/pending', $data);
    }

    /**
     * Memverifikasi pembayaran berdasarkan ID.
     *
     * @param int $id ID Pembayaran
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function verify(int $id)
    {
        $payment = $this->paymentModel->find($id);

        if (!$payment) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Memastikan hanya pembayaran 'pending' yang bisa diverifikasi
        if ($payment['status_pembayaran'] !== 'pending') {
            return redirect()->back()->with('warning', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $adminId = session()->get('user_id'); // ID admin/pengurus yang login
        $notes = $this->request->getPost('catatan') ?? '';

        $dataToUpdate = [
            'status_pembayaran' => 'verified',
            'verified_by'       => $adminId,
            'verified_at'       => Time::now('Asia/Jakarta', 'en_US'),
            'catatan'           => $notes,
        ];

        if ($this->paymentModel->update($id, $dataToUpdate)) {
            // Kirim notifikasi email ke anggota
            $this->_sendNotificationEmail($payment['member_id'], 'verified');

            return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi.');
        }

        return redirect()->back()->with('error', 'Gagal memverifikasi pembayaran.');
    }

    /**
     * Menolak pembayaran berdasarkan ID.
     *
     * @param int $id ID Pembayaran
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function reject(int $id)
    {
        $payment = $this->paymentModel->find($id);

        if (!$payment) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment['status_pembayaran'] !== 'pending') {
            return redirect()->back()->with('warning', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $reason = $this->request->getPost('rejection_reason');
        if (empty($reason)) {
            return redirect()->back()->withInput()->with('error', 'Alasan penolakan wajib diisi.');
        }

        $adminId = session()->get('user_id');

        $dataToUpdate = [
            'status_pembayaran' => 'rejected',
            'verified_by'       => $adminId,
            'verified_at'       => Time::now('Asia/Jakarta', 'en_US'),
            'catatan'           => $reason,
        ];

        if ($this->paymentModel->update($id, $dataToUpdate)) {
            // Kirim notifikasi email ke anggota
            $this->_sendNotificationEmail($payment['member_id'], 'rejected', ['reason' => $reason]);

            return redirect()->back()->with('success', 'Pembayaran berhasil ditolak.');
        }

        return redirect()->back()->with('error', 'Gagal menolak pembayaran.');
    }

    /**
     * Menampilkan laporan pembayaran dalam rentang tanggal tertentu.
     *
     * @return string
     */
    public function report(): string
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?? date('Y-m-t');

        $data = [
            'title'      => 'Laporan Pembayaran - SPK',
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'summary'    => $this->paymentModel->getPaymentSummary($startDate, $endDate),
            'payments'   => $this->paymentModel->getPaymentsByDateRange($startDate, $endDate), // Gunakan method baru
        ];

        return view('admin/payment/report', $data);
    }

    /**
     * [Helper] Mengirim email notifikasi ke anggota.
     *
     * @param int    $memberId ID anggota penerima email
     * @param string $status   Status verifikasi ('verified' atau 'rejected')
     * @param array  $data     Data tambahan (misal: alasan penolakan)
     * @return void
     */
    private function _sendNotificationEmail(int $memberId, string $status, array $data = []): void
    {
        try {
            $member = $this->memberModel->find($memberId);
            if (!$member) {
                return; // Anggota tidak ditemukan
            }

            $email = \Config\Services::email();
            $email->setTo($member['email']);

            if ($status === 'verified') {
                $email->setSubject('Pembayaran Iuran SPK Berhasil Diverifikasi');
                $email->setMessage(view('emails/payment_verified', ['member' => $member]));
            } elseif ($status === 'rejected') {
                $email->setSubject('Pembayaran Iuran SPK Ditolak');
                $email->setMessage(view('emails/payment_rejected', ['member' => $member, 'reason' => $data['reason']]));
            }

            $email->send();
        } catch (Exception $e) {
            // Sebaiknya catat error jika pengiriman email gagal
            log_message('error', 'Gagal mengirim email notifikasi pembayaran: ' . $e->getMessage());
        }
    }
}
