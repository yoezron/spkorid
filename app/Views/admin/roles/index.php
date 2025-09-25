<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Role
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-role_description d-flex align-items-center">
            <h1 class="flex-grow-1">Manajemen Role</h1>
            <a href="<?= base_url('admin/roles/create') ?>" class="btn btn-primary">
                <i class="material-icons-outlined">add</i>Tambah Role Baru
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
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Role</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Jumlah Pengguna</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($roles)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($role['role_name']) ?></td>
                                        <td><?= esc($role['role_description']) ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-primary"><?= $role['user_count'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('admin/roles/permissions/' . $role['id']) ?>" class="btn btn-sm btn-outline-info">Hak Akses</a>
                                                <a href="<?= base_url('admin/roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <?php if (!in_array($role['id'], [1, 2, 3])): // Protect default roles 
                                                ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $role['id'] ?>">
                                                        Hapus
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="deleteModal<?= $role['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus role "<?= esc($role['role_name']) ?>"?
                                                </div>
                                                <div class="modal-footer">
                                                    <?= form_open('admin/roles/delete/' . $role['id'], 'class="d-inline"') ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                                    <?= form_close() ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <p class="mt-4">Belum ada role yang ditambahkan.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>