<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Anggota Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Tambah Anggota Baru</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Pendaftaran Anggota</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('admin/members/store') ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control <?= (validation_show_error('nama_lengkap')) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" required>
                            <?php if (validation_show_error('nama_lengkap')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('nama_lengkap') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control <?= (validation_show_error('email')) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" required>
                            <?php if (validation_show_error('email')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('email') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control <?= (validation_show_error('password')) ? 'is-invalid' : '' ?>" id="password" name="password" required>
                            <?php if (validation_show_error('password')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('password') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nomor_anggota" class="form-label">Nomor Anggota</label>
                            <input type="text" class="form-control <?= (validation_show_error('nomor_anggota')) ? 'is-invalid' : '' ?>" id="nomor_anggota" name="nomor_anggota" value="<?= old('nomor_anggota') ?>" placeholder="Akan dibuat otomatis jika kosong">
                            <?php if (validation_show_error('nomor_anggota')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('nomor_anggota') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="nomor_telepon" name="nomor_telepon" value="<?= old('nomor_telepon') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="status_keanggotaan" class="form-label">Status Keanggotaan</label>
                            <select class="form-select" id="status_keanggotaan" name="status_keanggotaan" required>
                                <option value="pending" <?= (old('status_keanggotaan') == 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="active" <?= (old('status_keanggotaan') == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="suspended" <?= (old('status_keanggotaan') == 'suspended') ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Anggota</button>
                    <a href="<?= base_url('admin/members') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>