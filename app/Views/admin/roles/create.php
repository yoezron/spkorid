<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Role Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Tambah Role Baru</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Role</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('admin/roles/store') ?>

                <div class="mb-3">
                    <label for="role_name" class="form-label">Nama Role</label>
                    <input type="text" class="form-control <?= (validation_show_error('role_name')) ? 'is-invalid' : '' ?>" id="role_name" name="role_name" value="<?= old('role_name') ?>" placeholder="Contoh: Bendahara" required>
                    <?php if (validation_show_error('role_name')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('role_name') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="role_description" class="form-label">Deskripsi</label>
                    <textarea class="form-control <?= (validation_show_error('role_description')) ? 'is-invalid' : '' ?>" id="role_description" name="role_description" rows="3" placeholder="Jelaskan secara singkat fungsi dari role ini" required><?= old('role_description') ?></textarea>
                    <?php if (validation_show_error('role_description')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('role_description') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Role</button>
                    <a href="<?= base_url('admin/roles') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>