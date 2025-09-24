<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Pengguna
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Edit Pengguna: <?= esc($user['username']) ?></h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Data Pengguna</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('admin/users/update/' . $user['id']) ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control <?= (validation_show_error('username')) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username', $user['username']) ?>" required>
                            <?php if (validation_show_error('username')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('username') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control <?= (validation_show_error('email')) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                            <?php if (validation_show_error('email')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('email') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control <?= (validation_show_error('password')) ? 'is-invalid' : '' ?>" id="password" name="password">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah password.</div>
                            <?php if (validation_show_error('password')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('password') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role</label>
                            <select class="form-select <?= (validation_show_error('role_id')) ? 'is-invalid' : '' ?>" id="role_id" name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= set_select('role_id', $role['id'], ($user['role_id'] == $role['id'])) ?>><?= esc($role['role_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (validation_show_error('role_id')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('role_id') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" id="active" name="active" value="1" <?= $user['active'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="active">Pengguna Aktif</label>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>