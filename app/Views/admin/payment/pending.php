<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Verifikasi Pembayaran
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<link href="<?= base_url('plugins/lightbox/photoswipe.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('plugins/lightbox/default-skin/default-skin.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4">

                <h4 class="mb-4">Pembayaran Menunggu Verifikasi</h4>

                <table id="payment-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Nama Anggota</th>
                            <th>Jumlah</th>
                            <th>Jenis Pembayaran</th>
                            <th>Tgl. Upload</th>
                            <th>Bukti</th>
                            <th class="no-content">Aksi</th>
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
                                        <a href="<?= base_url($payment['bukti_pembayaran']) ?>" data-size="1024x768" data-author="Bukti Pembayaran">
                                            <img alt="bukti" src="<?= base_url($payment['bukti_pembayaran']) ?>" class="img-fluid" style="max-width: 60px;">
                                        </a>
                                    </td>
                                    <td>
                                        <button class="btn btn-success btn-sm" onclick="openVerifyModal(<?= $payment['id'] ?>)">Verifikasi</button>
                                        <button class="btn btn-danger btn-sm" onclick="openRejectModal(<?= $payment['id'] ?>, '<?= esc($payment['nama_lengkap']) ?>')">Tolak</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verifyModal" tabindex="-1" role="dialog" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyModalLabel">Konfirmasi Verifikasi Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="verifyForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin memverifikasi pembayaran ini?</p>
                    <div class="form-group">
                        <label for="catatan">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" id="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Anda akan menolak pembayaran dari <strong id="rejectMemberName"></strong>.</p>
                    <div class="form-group">
                        <label for="rejection_reason">Alasan Penolakan (Wajib diisi)</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script src="<?= base_url('plugins/lightbox/photoswipe.min.js') ?>"></script>
<script src="<?= base_url('plugins/lightbox/photoswipe-ui-default.min.js') ?>"></script>
<script src="<?= base_url('plugins/lightbox/custom-photswipe.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#payment-table').DataTable({
            // Konfigurasi DataTable sama seperti sebelumnya
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
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
            "stripeClasses": [],
            "lengthMenu": [10, 20, 50],
            "pageLength": 10
        });
    });

    // Fungsi untuk membuka modal verifikasi
    function openVerifyModal(paymentId) {
        const form = document.getElementById('verifyForm');
        form.action = `<?= base_url('admin/payment/verify/') ?>${paymentId}`;
        $('#verifyModal').modal('show');
    }

    // Fungsi untuk membuka modal penolakan
    function openRejectModal(paymentId, memberName) {
        const form = document.getElementById('rejectForm');
        form.action = `<?= base_url('admin/payment/reject/') ?>${paymentId}`;
        document.getElementById('rejectMemberName').innerText = memberName;
        $('#rejectModal').modal('show');
    }
</script>
<?= $this->endSection() ?>