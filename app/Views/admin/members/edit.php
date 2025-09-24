<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Anggota
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Edit Anggota: <?= esc($member['nama_lengkap']) ?></h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Data Anggota</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('admin/members/update/' . $member['id']) ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control <?= (validation_show_error('nama_lengkap')) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', $member['nama_lengkap'] ?? '') ?>" required>
                            <?php if (validation_show_error('nama_lengkap')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('nama_lengkap') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="nomor_anggota" class="form-label">Nomor Anggota</label>
                            <input type="text" class="form-control <?= (validation_show_error('nomor_anggota')) ? 'is-invalid' : '' ?>" id="nomor_anggota" name="nomor_anggota" value="<?= old('nomor_anggota', $member['nomor_anggota'] ?? '') ?>" required>
                            <?php if (validation_show_error('nomor_anggota')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('nomor_anggota') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="nomor_telepon" name="nomor_telepon" value="<?= old('nomor_telepon', $member['nomor_telepon'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?= esc($user['email']) ?>" disabled readonly>
                            <div class="form-text">Email tidak dapat diubah dari halaman ini.</div>
                        </div>

                        <div class="mb-3">
                            <label for="status_keanggotaan" class="form-label">Status Keanggotaan</label>
                            <select class="form-select <?= (validation_show_error('status_keanggotaan')) ? 'is-invalid' : '' ?>" id="status_keanggotaan" name="status_keanggotaan" required>
                                <option value="pending" <?= ($member['status_keanggotaan'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="active" <?= ($member['status_keanggotaan'] == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="suspended" <?= ($member['status_keanggotaan'] == 'suspended') ? 'selected' : '' ?>>Suspended</option>
                            </select>
                            <?php if (validation_show_error('status_keanggotaan')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('status_keanggotaan') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                            <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="<?= old('tanggal_bergabung', date('Y-m-d', strtotime($member['tanggal_bergabung']))) ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= old('alamat', $member['alamat'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>