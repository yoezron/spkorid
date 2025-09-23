<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Riwayat Pembayaran Iuran
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4">

                <div class="d-flex justify-content-between mb-4">
                    <h4>Riwayat Pembayaran Saya</h4>
                    <a href="<?= base_url('member/payment/create') ?>" class="btn btn-success">
                        <i data-feather="plus"></i> Lakukan Pembayaran Baru
                    </a>
                </div>

                <table id="payment-history-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Jenis Pembayaran</th>
                            <th>Jumlah</th>
                            <th>Tanggal Bayar</th>
                            <th>Status</th>
                            <th class="no-content">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payments)): ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><strong><?= esc($payment['nomor_transaksi']) ?></strong></td>
                                    <td><?= esc(ucfirst(str_replace('_', ' ', $payment['jenis_pembayaran']))) ?></td>
                                    <td>Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($payment['tanggal_pembayaran'])) ?></td>
                                    <td>
                                        <?php
                                        $status_class = 'secondary';
                                        if ($payment['status_pembayaran'] == 'verified') $status_class = 'success';
                                        if ($payment['status_pembayaran'] == 'pending') $status_class = 'warning';
                                        if ($payment['status_pembayaran'] == 'rejected') $status_class = 'danger';
                                        ?>
                                        <span class="badge badge-<?= $status_class ?>"><?= esc(ucfirst($payment['status_pembayaran'])) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('member/payment/invoice/' . $payment['id']) ?>" class="btn btn-sm btn-info">
                                            <i data-feather="file-text"></i> Invoice
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Anda belum memiliki riwayat pembayaran.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#payment-history-table').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                // Terjemahan Bahasa Indonesia...
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Cari...",
                "sLengthMenu": "Hasil :  _MENU_",
            },
            "order": [
                [3, "desc"]
            ], // Urutkan berdasarkan Tanggal Bayar terbaru
            "stripeClasses": [],
            "lengthMenu": [10, 20, 50],
            "pageLength": 10
        });
    });
</script>
<?= $this->endSection() ?>