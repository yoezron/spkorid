<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Ubah Password
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Ubah Password</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Keamanan</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('member/change-password') ?>

                <div class="mb-3">
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <input type="password" class="form-control <?= (validation_show_error('current_password')) ? 'is-invalid' : '' ?>" id="current_password" name="current_password" required>
                    <?php if (validation_show_error('current_password')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('current_password') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control <?= (validation_show_error('new_password')) ? 'is-invalid' : '' ?>" id="new_password" name="new_password" required>
                    <?php if (validation_show_error('new_password')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('new_password') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control <?= (validation_show_error('confirm_password')) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required>
                    <?php if (validation_show_error('confirm_password')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('confirm_password') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Perbarui Password</button>
                    <a href="<?= base_url('member/profile') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>