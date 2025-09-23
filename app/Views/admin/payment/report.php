<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Laporan Pembayaran
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<link href="<?= base_url('plugins/flatpickr/flatpickr.css') ?>" rel="stylesheet" type="text/css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Laporan Keuangan & Pembayaran</h4>
                <p>Filter berdasarkan rentang tanggal untuk melihat riwayat transaksi.</p>

                <form method="get" class="form-inline mb-4">
                    <div class="form-group mr-2">
                        <label for="start_date" class="mr-2">Dari Tanggal</label>
                        <input id="start_date" name="start_date" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Pilih tanggal.." value="<?= esc($startDate) ?>">
                    </div>
                    <div class="form-group mr-2">
                        <label for="end_date" class="mr-2">Sampai Tanggal</label>
                        <input id="end_date" name="end_date" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Pilih tanggal.." value="<?= esc($endDate) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                <hr>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="widget-content widget-content-area bg-gradient-success text-white br-6 p-3">
                            <h5>Total Terverifikasi</h5>
                            <h2>Rp <?= number_format($summary['total_verified'] ?? 0, 0, ',', '.') ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget-content widget-content-area bg-gradient-warning text-white br-6 p-3">
                            <h5>Total Pending</h5>
                            <h2>Rp <?= number_format($summary['total_pending'] ?? 0, 0, ',', '.') ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget-content widget-content-area bg-gradient-info text-white br-6 p-3">
                            <h5>Total Transaksi</h5>
                            <h2><?= number_format($summary['total_transactions'] ?? 0) ?></h2>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="report-table" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Nama Anggota</th>
                                <th>Jumlah</th>
                                <th>Jenis</th>
                                <th>Tgl. Pembayaran</th>
                                <th>Status</th>
                                <th>Diverifikasi Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($payments)): ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><strong><?= esc($payment['nomor_transaksi']) ?></strong></td>
                                        <td><?= esc($payment['nama_lengkap']) ?></td>
                                        <td>Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                        <td><?= esc(ucfirst(str_replace('_', ' ', $payment['jenis_pembayaran']))) ?></td>
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
                                        <td><?= esc($payment['verifier_name'] ?? 'N/A') // Asumsi ada join ke user untuk nama verifikator 
                                            ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script src="<?= base_url('plugins/flatpickr/flatpickr.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Date Picker
        var f1 = flatpickr(document.getElementById('start_date'));
        var f2 = flatpickr(document.getElementById('end_date'));

        // Inisialisasi DataTable
        $('#report-table').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'fB>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            buttons: {
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-sm'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm'
                    }
                ]
            },
            "oLanguage": {
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
                [4, "desc"]
            ],
            "stripeClasses": [],
            "lengthMenu": [10, 25, 50, 100],
            "pageLength": 25
        });
    });
</script>
<?= $this->endSection() ?>