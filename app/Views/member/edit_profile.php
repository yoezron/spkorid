<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Profil Anggota
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Edit Profil</h1>
            <span>Lengkapi dan perbarui data keanggotaan Anda.</span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?= form_open_multipart('member/profile/update') ?>
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal" type="button">
                            <i class="material-icons">person</i> Data Pribadi
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#employment" type="button">
                            <i class="material-icons">work</i> Kepegawaian
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#academic" type="button">
                            <i class="material-icons">school</i> Akademik & Lainnya
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="personal">
                        <h5 class="mb-4">Informasi Pribadi</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control <?= (validation_show_error('nama_lengkap')) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', $member['nama_lengkap'] ?? '') ?>" required>
                                <div class="invalid-feedback"><?= validation_show_error('nama_lengkap') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="<?= esc($member['email']) ?>" disabled readonly>
                                <div class="form-text">Email tidak dapat diubah.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select <?= (validation_show_error('jenis_kelamin')) ? 'is-invalid' : '' ?>" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="" disabled>-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" <?= old('jenis_kelamin', $member['jenis_kelamin']) == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= old('jenis_kelamin', $member['jenis_kelamin']) == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                                <div class="invalid-feedback"><?= validation_show_error('jenis_kelamin') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="nomor_whatsapp" class="form-label">Nomor WhatsApp</label>
                                <input type="tel" class="form-control <?= (validation_show_error('nomor_whatsapp')) ? 'is-invalid' : '' ?>" id="nomor_whatsapp" name="nomor_whatsapp" value="<?= old('nomor_whatsapp', $member['nomor_whatsapp'] ?? '') ?>" placeholder="Contoh: 628123456789" required>
                                <div class="invalid-feedback"><?= validation_show_error('nomor_whatsapp') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control <?= (validation_show_error('tempat_lahir')) ? 'is-invalid' : '' ?>" id="tempat_lahir" name="tempat_lahir" value="<?= old('tempat_lahir', $member['tempat_lahir'] ?? '') ?>">
                                <div class="invalid-feedback"><?= validation_show_error('tempat_lahir') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control <?= (validation_show_error('tanggal_lahir')) ? 'is-invalid' : '' ?>" id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir', $member['tanggal_lahir'] ?? '') ?>">
                                <div class="invalid-feedback"><?= validation_show_error('tanggal_lahir') ?></div>
                            </div>
                            <div class="col-12">
                                <label for="alamat_lengkap" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control <?= (validation_show_error('alamat_lengkap')) ? 'is-invalid' : '' ?>" id="alamat_lengkap" name="alamat_lengkap" rows="3" required><?= old('alamat_lengkap', $member['alamat_lengkap'] ?? '') ?></textarea>
                                <div class="invalid-feedback"><?= validation_show_error('alamat_lengkap') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="media_sosial" class="form-label">Media Sosial (Opsional)</label>
                                <input type="text" class="form-control" id="media_sosial" name="media_sosial" value="<?= old('media_sosial', $member['media_sosial'] ?? '') ?>" placeholder="cth: Instagram @serikatpekerjakampus">
                            </div>
                            <div class="col-md-6">
                                <label for="foto_profil" class="form-label">Ganti Foto Profil (Opsional)</label>
                                <input class="form-control" type="file" id="foto_profil" name="foto_profil" accept="image/png, image/jpeg, image/jpg">
                                <div class="form-text">Kosongkan jika tidak ingin mengganti. Maks: 2MB.</div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="employment">
                        <h5 class="mb-4">Informasi Kepegawaian</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nidn_nip" class="form-label">NIDN/NIP</label>
                                <input type="text" class="form-control <?= (validation_show_error('nidn_nip')) ? 'is-invalid' : '' ?>" id="nidn_nip" name="nidn_nip" value="<?= old('nidn_nip', $member['nidn_nip'] ?? '') ?>">
                                <div class="invalid-feedback"><?= validation_show_error('nidn_nip') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
                                <input type="text" class="form-control <?= (validation_show_error('jabatan_fungsional')) ? 'is-invalid' : '' ?>" id="jabatan_fungsional" name="jabatan_fungsional" value="<?= old('jabatan_fungsional', $member['jabatan_fungsional'] ?? '') ?>">
                                <div class="invalid-feedback"><?= validation_show_error('jabatan_fungsional') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="golongan_pangkat" class="form-label">Golongan/Pangkat</label>
                                <input type="text" class="form-control <?= (validation_show_error('golongan_pangkat')) ? 'is-invalid' : '' ?>" id="golongan_pangkat" name="golongan_pangkat" value="<?= old('golongan_pangkat', $member['golongan_pangkat'] ?? '') ?>">
                                <div class="invalid-feedback"><?= validation_show_error('golongan_pangkat') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="status_kepegawaian_id" class="form-label">Status Kepegawaian</label>
                                <select class="form-select" name="status_kepegawaian_id">
                                    <option value="">-- Pilih Status --</option>
                                    <?php /*
                                        // TODO: Loop data dari tabel ref_status_kepegawaian
                                        // Contoh:
                                        foreach($status_kepegawaian as $status) {
                                            $selected = old('status_kepegawaian_id', $member['status_kepegawaian_id']) == $status['id'] ? 'selected' : '';
                                            echo "<option value='{$status['id']}' {$selected}>{$status['nama_status']}</option>";
                                        }
                                    */ ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="academic">
                        <h5 class="mb-4">Informasi Akademik & Lainnya</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
                                <select class="form-select <?= (validation_show_error('pendidikan_terakhir')) ? 'is-invalid' : '' ?>" id="pendidikan_terakhir" name="pendidikan_terakhir">
                                    <option value="" disabled>-- Pilih Pendidikan --</option>
                                    <option value="S1" <?= old('pendidikan_terakhir', $member['pendidikan_terakhir']) == 'S1' ? 'selected' : '' ?>>S1</option>
                                    <option value="S2" <?= old('pendidikan_terakhir', $member['pendidikan_terakhir']) == 'S2' ? 'selected' : '' ?>>S2</option>
                                    <option value="S3" <?= old('pendidikan_terakhir', $member['pendidikan_terakhir']) == 'S3' ? 'selected' : '' ?>>S3</option>
                                    <option value="Profesi" <?= old('pendidikan_terakhir', $member['pendidikan_terakhir']) == 'Profesi' ? 'selected' : '' ?>>Profesi</option>
                                </select>
                                <div class="invalid-feedback"><?= validation_show_error('pendidikan_terakhir') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="kampus_id" class="form-label">Asal Kampus</label>
                                <select class="form-select" name="kampus_id">
                                    <option value="">-- Pilih Kampus --</option>
                                    <?php /*
                                        // TODO: Loop data dari tabel ref_kampus
                                        foreach($kampus as $k) {
                                            $selected = old('kampus_id', $member['kampus_id']) == $k['id'] ? 'selected' : '';
                                            echo "<option value='{$k['id']}' {$selected}>{$k['nama_kampus']}</option>";
                                        }
                                    */ ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="bidang_keahlian" class="form-label">Bidang Keahlian</label>
                                <textarea class="form-control" name="bidang_keahlian" rows="2"><?= old('bidang_keahlian', $member['bidang_keahlian'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="motivasi_berserikat" class="form-label">Motivasi Berserikat</label>
                                <textarea class="form-control" name="motivasi_berserikat" rows="3"><?= old('motivasi_berserikat', $member['motivasi_berserikat'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?= base_url('member/profile') ?>" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="material-icons">save</i> Simpan Perubahan
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>