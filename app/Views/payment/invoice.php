<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Invoice <?= esc($payment['invoice_number']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Detail Invoice</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="invoice-header">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="invoice-logo">
                                <span class="logo-text fs-3">SPK - INVOICE</span>
                            </div>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <p class="invoice-id">Invoice #<?= esc($payment['invoice_number']) ?></p>
                            <p class="invoice-date">Tanggal: <?= date('d F Y', strtotime($payment['tanggal_pembayaran'])) ?></p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="invoice-info">
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="invoice-info-title">Ditagihkan Kepada:</p>
                            <p class="invoice-info-text">
                                <strong><?= esc($member['nama_lengkap']) ?></strong><br>
                                <?= esc($member['nomor_anggota']) ?><br>
                                <?= esc($user['email']) ?><br>
                                <?= esc($member['nomor_telepon']) ?>
                            </p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <p class="invoice-info-title">Status:</p>
                            <?php
                            $statusClass = 'badge-secondary';
                            if ($payment['status_pembayaran'] == 'verified') {
                                $statusClass = 'badge-success';
                            } elseif ($payment['status_pembayaran'] == 'pending') {
                                $statusClass = 'badge-warning';
                            } elseif ($payment['status_pembayaran'] == 'rejected') {
                                $statusClass = 'badge-danger';
                            }
                            ?>
                            <span class="badge <?= $statusClass ?> fs-6"><?= esc(ucfirst($payment['status_pembayaran'])) ?></span>
                        </div>
                    </div>
                </div>

                <div class="invoice-body mt-4">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Iuran Keanggotaan Serikat Pekerja Kampus</td>
                                    <td class="text-end">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="1" class="text-end">Total</th>
                                    <th class="text-end">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="invoice-footer mt-4">
                    <p>Terima kasih atas pembayaran Anda. Dana ini akan digunakan untuk mendukung kegiatan dan perjuangan serikat.</p>
                </div>

            </div>
        </div>

        <div class="invoice-actions text-end mt-4">
            <a href="javascript:window.print()" class="btn btn-primary">
                <i class="material-icons-outlined">print</i> Cetak
            </a>
            <a href="<?= base_url('member/payment/download-invoice/' . $payment['id']) ?>" class="btn btn-info">
                <i class="material-icons-outlined">download</i> Unduh PDF
            </a>
            <a href="<?= base_url('member/payment/history') ?>" class="btn btn-light">
                <i class="material-icons-outlined">arrow_back</i> Kembali
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>