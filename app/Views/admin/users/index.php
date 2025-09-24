<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Pengguna
<?= $this->endSection() ?>

<?= $this->section('pageStyles') ?>
<link href="<?= base_url('neptune-assets/plugins/datatables/datatables.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description d-flex align-items-center">
            <h1 class="flex-grow-1">Manajemen Pengguna</h1>
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
                <i class="material-icons-outlined">add</i>Tambah Pengguna Baru
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>
                <div class="table-responsive">
                    <table id="users-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-center">Status</th>
                                <th>Terakhir Login</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <img src="<?= base_url('uploads/avatars/' . ($user['avatar'] ?? 'default.png')) ?>" alt="avatar">
                                                </div>
                                                <div>
                                                    <?= esc($user['username']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td>
                                            <span class="badge badge-secondary"><?= esc($user['role_name']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($user['active']): ?>
                                                <span class="badge badge-style-light rounded-pill badge-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-style-light rounded-pill badge-danger">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $user['last_login'] ? date('d M Y, H:i', strtotime($user['last_login'])) : 'Belum Pernah' ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $user['id'] ?>" <?= ($user['id'] == session()->get('user_id')) ? 'disabled' : '' ?>>
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="deleteModal<?= $user['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus pengguna "<?= esc($user['username']) ?>"? Tindakan ini tidak dapat dibatalkan.
                                                </div>
                                                <div class="modal-footer">
                                                    <?= form_open('admin/users/delete/' . $user['id'], 'class="d-inline"') ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
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
        $('#users-table').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.22/i18n/Indonesian.json"
            }
        });
    });
</script>
<?= $this->endSection() ?>