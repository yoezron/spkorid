<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Profil Saya
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/file-upload/file-upload-with-preview.min.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('plugins/select2/select2.min.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row layout-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 layout-top-spacing">
            <div class="widget-content widget-content-area br-6 p-4">
                <h4>Ubah Informasi Profil</h4>
                <p>Pastikan data Anda selalu yang terbaru. Perbarui informasi di bawah ini dan klik simpan.</p>
                <hr>

                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <form action="<?= base_url('member/update-profile') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" value="<?= old('nama_lengkap', $member['nama_lengkap']) ?>" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email (tidak dapat diubah)</label>
                                    <input type="email" class="form-control" id="email" value="<?= esc($member['email']) ?>" disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="nomor_whatsapp">Nomor WhatsApp</label>
                                    <input type="text" class="form-control" name="nomor_whatsapp" id="nomor_whatsapp" value="<?= old('nomor_whatsapp', $member['nomor_whatsapp']) ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="alamat_lengkap">Alamat Lengkap</label>
                                <textarea class="form-control" name="alamat_lengkap" id="alamat_lengkap" rows="3" required><?= old('alamat_lengkap', $member['alamat_lengkap']) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="bidang_keahlian">Bidang Keahlian</label>
                                <input type="text" class="form-control" name="bidang_keahlian" id="bidang_keahlian" value="<?= old('bidang_keahlian', $member['bidang_keahlian']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="media_sosial">Media Sosial</label>
                                <input type="text" class="form-control" name="media_sosial" id="media_sosial" placeholder="Instagram: @username, LinkedIn: nama" value="<?= old('media_sosial', $member['media_sosial']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="upload-profile-photo">
                                <label>Foto Profil</label>
                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                    <label>Ganti Foto <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                                    <label class="custom-file-container__custom-file">
                                        <input type="file" name="foto" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                    </label>
                                    <div class="custom-file-container__image-preview"></div>
                                </div>
                                <small>Unggah foto baru untuk menggantikan yang lama.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="<?= base_url('member/profile') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/file-upload/file-upload-with-preview.min.js') ?>"></script>
<script>
    // Inisialisasi upload gambar
    var firstUpload = new FileUploadWithPreview('myFirstImage', {
        images: {
            // Set gambar yang sudah ada sebagai default
            baseImage: '<?= base_url($member['foto_path'] ?? 'assets/img/90x90.jpg') ?>',
        },
    })
</script>
<?= $this->endSection() ?>