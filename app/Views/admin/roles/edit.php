<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Role
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Edit Role: <?= esc($role['role_name']) ?></h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Edit Role</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('admin/roles/update/' . $role['id']) ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="mb-3">
                    <label for="role_name" class="form-label">Nama Role</label>
                    <input type="text" class="form-control <?= (validation_show_error('role_name')) ? 'is-invalid' : '' ?>" id="role_name" name="role_name" value="<?= old('role_name', $role['role_name']) ?>" required>
                    <?php if (validation_show_error('role_name')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('role_name') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control <?= (validation_show_error('description')) ? 'is-invalid' : '' ?>" id="description" name="description" rows="3" required><?= old('description', $role['description']) ?></textarea>
                    <?php if (validation_show_error('description')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('description') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="<?= base_url('admin/roles') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>