<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit User: <?= esc($user['username']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/forms/switches.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Formulir Edit User</h4>
                <p>Ubah informasi untuk user <strong><?= esc($user['username']) ?></strong>.</p>
                <hr>

                <?php // Menampilkan error validasi jika ada 
                ?>
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <h5>Informasi Akun</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= old('username', $user['username']) ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama_lengkap">Nama Lengkap (dari data member)</label>
                            <input type="text" class="form-control" id="nama_lengkap" value="<?= esc($user['nama_lengkap'] ?? 'Tidak tertaut ke data member') ?>" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="role_id">Role</label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= (old('role_id', $user['role_id']) == $role['id']) ? 'selected' : '' ?>>
                                        <?= esc(ucwords(str_replace('_', ' ', $role['role_name']))) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status Akun</label>
                        <div>
                            <label class="switch s-icons s-outline s-outline-success">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" <?= (old('is_active', $user['is_active'])) ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                            <span class="ml-2">Aktif</span>
                        </div>
                    </div>

                    <hr>
                    <h5>Ubah Password (Opsional)</h5>
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small>Kosongkan jika tidak ingin mengubah password.</small>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>