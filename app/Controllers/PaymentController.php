<?php

// ============================================
// PAYMENT CONTROLLERS
// ============================================

// app/Controllers/PaymentController.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PaymentHistoryModel;
use App\Models\MemberModel;

class PaymentController extends BaseController
{
    protected $paymentModel;
    protected $memberModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentHistoryModel();
        $this->memberModel = new MemberModel();
    }

    /**
     * Payment history for member
     */
    public function history()
    {
        $memberId = session()->get('member_id');

        $data = [
            'title' => 'Riwayat Pembayaran - SPK',
            'payments' => $this->paymentModel->getMemberPayments($memberId)
        ];

        return view('payment/history', $data);
    }

    /**
     * Make payment
     */
    public function create()
    {
        $data = [
            'title' => 'Pembayaran Iuran - SPK',
            'member' => $this->memberModel->find(session()->get('member_id'))
        ];

        return view('payment/create', $data);
    }

    /**
     * Process payment
     */
    public function store()
    {
        $rules = [
            'jenis_pembayaran' => 'required|in_list[iuran_bulanan,iuran_tahunan,sumbangan]',
            'jumlah' => 'required|numeric|greater_than[0]',
            'metode_pembayaran' => 'required',
            'bukti_pembayaran' => 'uploaded[bukti_pembayaran]|max_size[bukti_pembayaran,5120]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $memberId = session()->get('member_id');

        // Handle file upload
        $bukti = $this->request->getFile('bukti_pembayaran');
        $buktiName = $bukti->getRandomName();
        $bukti->move(ROOTPATH . 'public/uploads/payments', $buktiName);

        $paymentData = [
            'member_id' => $memberId,
            'nomor_transaksi' => 'TRX-' . date('YmdHis') . '-' . $memberId,
            'jenis_pembayaran' => $this->request->getPost('jenis_pembayaran'),
            'periode_bulan' => $this->request->getPost('periode_bulan') ?? date('n'),
            'periode_tahun' => $this->request->getPost('periode_tahun') ?? date('Y'),
            'jumlah' => $this->request->getPost('jumlah'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'bukti_pembayaran' => 'uploads/payments/' . $buktiName,
            'tanggal_pembayaran' => date('Y-m-d H:i:s'),
            'status_pembayaran' => 'pending'
        ];

        $this->paymentModel->insert($paymentData);

        return redirect()->to('/member/payment/history')
            ->with('success', 'Pembayaran berhasil disubmit dan menunggu verifikasi');
    }
}
