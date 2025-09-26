<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Verifikasi Pembayaran
<?= $this->endSection() ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('neptune-assets/plugins/datatables/datatables.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('neptune-assets/plugins/datatables/dt-global_style.css') ?>">
<link href="<?= base_url('neptune-assets/plugins/lightbox/photoswipe.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('neptune-assets/plugins/lightbox/default-skin/default-skin.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Pembayaran Menunggu Verifikasi</h1>
            <p>Daftar pembayaran iuran dari anggota yang membutuhkan persetujuan Anda.</p>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>
                <div class="table-responsive">
                    <table id="payment-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Nama Anggota</th>
                                <th>Jumlah</th>
                                <th>Jenis Pembayaran</th>
                                <th>Tgl. Upload</th>
                                <th>Bukti</th>
                                <th class="text-center">Aksi</th>
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
                                            <?php if (!empty($payment['bukti_pembayaran']) && file_exists(FCPATH . $payment['bukti_pembayaran'])): ?>
                                                <a href="<?= base_url($payment['bukti_pembayaran']) ?>" data-size="1024x768" data-author="Bukti Pembayaran">
                                                    <img alt="bukti" src="<?= base_url($payment['bukti_pembayaran']) ?>" class="img-fluid" style="max-width: 60px; border-radius: 4px;">
                                                </a>
                                            <?php else: ?>
                                                <span class="badge badge-light-danger">Tidak ada bukti</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
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
</div>

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyModalLabel">Konfirmasi Verifikasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verifyForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin memverifikasi pembayaran ini?</p>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" id="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Anda akan menolak pembayaran dari <strong id="rejectMemberName"></strong>.</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan (Wajib diisi)</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('neptune-assets/plugins/datatables/datatables.min.js') ?>"></script>
<script src="<?= base_url('neptune-assets/plugins/lightbox/photoswipe.min.js') ?>"></script>
<script src="<?= base_url('neptune-assets/plugins/lightbox/photoswipe-ui-default.min.js') ?>"></script>
<script src="<?= base_url('neptune-assets/plugins/lightbox/custom-photswipe.js') ?>"></script>
<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#payment-table').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.22/i18n/Indonesian.json"
            }
        });
    });

    // --- PERBAIKAN JAVASCRIPT MODAL UNTUK BOOTSTRAP 5 ---

    // Get modal instances
    var verifyModal = new bootstrap.Modal(document.getElementById('verifyModal'));
    var rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));

    // Fungsi untuk membuka modal verifikasi
    function openVerifyModal(paymentId) {
        const form = document.getElementById('verifyForm');
        form.action = `<?= base_url('admin/payments/verify/') ?>${paymentId}`;
        verifyModal.show();
    }

    // Fungsi untuk membuka modal penolakan
    function openRejectModal(paymentId, memberName) {
        const form = document.getElementById('rejectForm');
        form.action = `<?= base_url('admin/payments/reject/') ?>${paymentId}`;
        document.getElementById('rejectMemberName').innerText = memberName;
        rejectModal.show();
    }
</script>
<?= $this->endSection() ?>