<?php

// ============================================
// PAYMENT CONTROLLERS
// ============================================

// app/Controllers/PaymentController.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PaymentHistoryModel;
use App\Models\MemberModel;
use App\Models\UserModel;

class PaymentController extends BaseController
{
    protected $paymentModel;
    protected $memberModel;
    protected $userModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentHistoryModel();
        $this->memberModel = new MemberModel();
        $this->userModel = new UserModel();
    }

    /**
     * Payment history for member
     */
    public function history()
    {
        $memberId = session()->get('member_id');

        $data = [
            'title' => 'Riwayat Pembayaran - SPK',
            'payments' => $this->paymentModel->getMemberPayments($memberId),
            'pager'    => $this->paymentModel->pager
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

    /**
     * Process payment proof upload from profile page
     */
    public function uploadProof()
    {
        $rules = [
            'amount' => 'required|numeric',
            'payment_date' => 'required|valid_date',
            'metode_pembayaran' => 'required|string|max_length[100]',
            'payment_proof' => [
                'rules' => 'uploaded[payment_proof]|max_size[payment_proof,5120]|ext_in[payment_proof,png,jpg,jpeg,pdf]',
                'errors' => [
                    'uploaded' => 'Anda harus mengunggah bukti pembayaran.',
                    'max_size' => 'Ukuran file maksimal adalah 5MB.',
                    'ext_in' => 'Format file yang diizinkan hanya PNG, JPG, JPEG, atau PDF.',
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $memberId = session()->get('member_id');
        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        // Handle file upload
        $bukti = $this->request->getFile('payment_proof');
        if ($bukti->isValid() && !$bukti->hasMoved()) {
            $buktiName = $bukti->getRandomName();
            $bukti->move(FCPATH . 'uploads/payments', $buktiName);

            // Extract month and year from payment date
            $paymentDate = $this->request->getPost('payment_date');
            $dateObject = new \DateTime($paymentDate);

            $paymentData = [
                'member_id' => $memberId,
                'nomor_transaksi' => 'TRX-' . date('YmdHis') . '-' . $memberId,
                'jenis_pembayaran' => $this->request->getPost('payment_type') ?? 'Iuran Rutin',
                'periode_bulan' => $dateObject->format('n'),
                'periode_tahun' => $dateObject->format('Y'),
                'jumlah' => $this->request->getPost('amount'),
                'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
                'bukti_pembayaran' => 'uploads/payments/' . $buktiName,
                'tanggal_pembayaran' => $paymentDate,
                'status_pembayaran' => 'pending',
                'catatan' => $this->request->getPost('notes')
            ];

            if ($this->paymentModel->insert($paymentData)) {
                return redirect()->to('member/profile')->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi.');
            }
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mengunggah bukti pembayaran. Silakan coba lagi.');
    }

    /**
     * Menampilkan halaman invoice untuk pembayaran.
     */
    public function invoice($id)
    {
        $memberId = session()->get('member_id');
        $payment = $this->paymentModel->find($id);

        if (!$payment || $payment['member_id'] != $memberId) {
            return redirect()->to('member/payment/history')->with('error', 'Invoice tidak ditemukan.');
        }

        $data = [
            'title'   => 'Invoice ' . esc($payment['nomor_transaksi']),
            'payment' => $payment,
            'member'  => $this->memberModel->find($memberId),
            'user'    => $this->userModel->where('member_id', $memberId)->first() // INI PENTING
        ];

        return view('payment/invoice', $data);
    }

    /**
     * Mengunduh invoice sebagai PDF.
     */
    public function downloadInvoice($id)
    {
        $memberId = session()->get('member_id');
        $payment = $this->paymentModel->find($id);

        if (!$payment || $payment['member_id'] != $memberId) {
            return redirect()->to('member/payment/history')->with('error', 'Invoice tidak ditemukan.');
        }

        $data = [
            'payment' => $payment,
            'member'  => $this->memberModel->find($memberId),
            'user'    => $this->userModel->where('member_id', $memberId)->first() // INI JUGA PENTING
        ];

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('payment/invoice_pdf_template', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("invoice-" . $payment['nomor_transaksi'] . ".pdf", ['Attachment' => 0]);
    }
}
