<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Anggota Baru
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/select2/select2.min.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Formulir Anggota Baru</h4>
                <p>Isi semua informasi yang diperlukan untuk mendaftarkan anggota baru.</p>
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

                <form action="<?= base_url('admin/members/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <h5>Data Pribadi & Akun</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password Akun</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small>Password default untuk login anggota baru.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="Laki-laki" <?= old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="alamat_lengkap">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3" required><?= old('alamat_lengkap') ?></textarea>
                    </div>

                    <hr>
                    <h5>Data Kepegawaian & Akademik</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="kampus_id">Kampus</label>
                            <select class="form-control select2" id="kampus_id" name="kampus_id" required>
                                <option value="">Pilih Kampus</option>
                                <?php foreach ($kampus_list as $kampus): ?>
                                    <option value="<?= $kampus['id'] ?>" <?= old('kampus_id') == $kampus['id'] ? 'selected' : '' ?>><?= esc($kampus['nama_kampus']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="status_kepegawaian_id">Status Kepegawaian</label>
                            <select class="form-control select2" id="status_kepegawaian_id" name="status_kepegawaian_id" required>
                                <option value="">Pilih Status</option>
                                <?php foreach ($status_kepegawaian as $status): ?>
                                    <option value="<?= $status['id'] ?>" <?= old('status_kepegawaian_id') == $status['id'] ? 'selected' : '' ?>><?= esc($status['nama_status']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Daftarkan Anggota</button>
                        <a href="<?= base_url('admin/members') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/select2.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2({
            placeholder: "Pilih salah satu",
            allowClear: true
        });
    });
</script>
<?= $this->endSection() ?>