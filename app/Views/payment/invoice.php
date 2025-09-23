<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Invoice #<?= esc($payment['nomor_transaksi']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/css/apps/invoice-preview.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row invoice layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="app-invoice-preview">
            <div class="invoice-container">
                <div class="invoice-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="invoice-logo">
                                <div class="brand-logo">
                                    <img src="<?= base_url('assets/img/logo.svg') ?>" alt="logo">
                                    <span>Serikat Pekerja Kampus</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <p class="invoice-number">Invoice #<?= esc($payment['nomor_transaksi']) ?></p>
                            <div class="invoice-date">
                                <p><span>Tanggal Invoice:</span> <?= date('d M Y', strtotime($payment['created_at'])) ?></p>
                                <p><span>Tanggal Bayar:</span> <?= date('d M Y', strtotime($payment['tanggal_pembayaran'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4 mb-4">

                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="inv-title">Dibayarkan Oleh:</h5>
                            <div class="inv-to">
                                <p><?= esc($member['nama_lengkap']) ?></p>
                                <p><?= esc($member['email']) ?></p>
                                <p><?= esc($member['nomor_whatsapp']) ?></p>
                                <p><?= esc($member['alamat_lengkap']) ?></p>
                            </div>
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <h5 class="inv-title">Dibayarkan Kepada:</h5>
                            <div class="inv-from">
                                <p>Serikat Pekerja Kampus</p>
                                <p>Sekretariat SPK</p>
                                <p>email@spk.org</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="invoice-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="">Deskripsi</th>
                                    <th class="text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <p class="m-0">Pembayaran <?= esc(ucwords(str_replace('_', ' ', $payment['jenis_pembayaran']))) ?></p>
                                        <p class="m-0 text-muted">Untuk periode: <?= date('F Y', mktime(0, 0, 0, $payment['periode_bulan'], 1, $payment['periode_tahun'])) ?></p>
                                    </td>
                                    <td class="text-right">Rp <?= number_format($payment['jumlah'], 2, ',', '.') ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1" class="text-right"><strong>Total</strong></td>
                                    <td class="text-right"><strong>Rp <?= number_format($payment['jumlah'], 2, ',', '.') ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="invoice-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="inv-status">Status Pembayaran:
                                <span class="badge badge-<?= $payment['status_pembayaran'] == 'verified' ? 'success' : 'warning' ?>">
                                    <?= esc(ucfirst($payment['status_pembayaran'])) ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <button class="btn btn-primary" onclick="window.print();">
                                <i data-feather="printer" class="mr-2"></i> Cetak Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/apps/invoice-preview.js') ?>"></script>
<?= $this->endSection() ?>