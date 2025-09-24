<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Anggota Menunggu Verifikasi
<?= $this->endSection() ?>

<?= $this->section('pageStyles') ?>
<link href="<?= base_url('neptune-assets/plugins/datatables/datatables.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Anggota Menunggu Verifikasi</h1>
            <p>Daftar calon anggota yang telah mendaftar dan menunggu persetujuan Anda.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>
                <div class="table-responsive">
                    <table id="pending-members-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Tanggal Daftar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_members)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($pending_members as $member): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($member['nama_lengkap']) ?></td>
                                        <td><?= esc($member['email']) ?></td>
                                        <td><?= date('d M Y, H:i', strtotime($member['created_at'])) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
                                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#verifyModal<?= $member['id'] ?>">Verifikasi</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $member['id'] ?>">Tolak</button>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="verifyModal<?= $member['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Verifikasi</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Yakin ingin memverifikasi pendaftaran "<?= esc($member['nama_lengkap']) ?>"?
                                                </div>
                                                <div class="modal-footer">
                                                    <?= form_open('admin/members/verify/' . $member['id']) ?>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Ya, Verifikasi</button>
                                                    <?= form_close() ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="rejectModal<?= $member['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Penolakan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Yakin ingin menolak pendaftaran "<?= esc($member['nama_lengkap']) ?>"?
                                                </div>
                                                <div class="modal-footer">
                                                    <?= form_open('admin/members/reject/' . $member['id']) ?>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                                                    <?= form_close() ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('neptune-assets/plugins/datatables/datatables.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#pending-members-table').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.22/i18n/Indonesian.json"
            }
        });
    });
</script>
<?= $this->endSection() ?>