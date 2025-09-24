<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Profil
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Edit Profil</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Data Diri</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open_multipart('member/profile/update') ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control <?= (validation_show_error('nama_lengkap')) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', $member['nama_lengkap'] ?? '') ?>" required>
                            <?php if (validation_show_error('nama_lengkap')): ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('nama_lengkap') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control <?= (validation_show_error('nomor_telepon')) ? 'is-invalid' : '' ?>" id="nomor_telepon" name="nomor_telepon" value="<?= old('nomor_telepon', $member['nomor_telepon'] ?? '') ?>">
                            <?php if (validation_show_error('nomor_telepon')): ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('nomor_telepon') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control <?= (validation_show_error('alamat')) ? 'is-invalid' : '' ?>" id="alamat" name="alamat" rows="3"><?= old('alamat', $member['alamat'] ?? '') ?></textarea>
                            <?php if (validation_show_error('alamat')): ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('alamat') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?= esc($user['email']) ?>" disabled readonly>
                            <div class="form-text">Email tidak dapat diubah.</div>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Ganti Foto Profil</label>
                            <input class="form-control <?= (session()->getFlashdata('error_avatar')) ? 'is-invalid' : '' ?>" type="file" id="avatar" name="avatar">
                            <?php if (session()->getFlashdata('error_avatar')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('error_avatar') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="<?= base_url('member/profile') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>