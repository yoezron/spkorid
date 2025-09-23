<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Anggota: <?= esc($member['nama_lengkap']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/select2/select2.min.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Edit Detail Anggota</h4>
                <p>Ubah informasi anggota di bawah ini dan klik simpan.</p>
                <hr>

                <form action="<?= base_url('admin/members/update/' . $member['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="POST">
                    <h5>Data Pribadi & Akun</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', $member['nama_lengkap']) ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $member['user_email']) ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nomor_whatsapp">Nomor WhatsApp</label>
                            <input type="text" class="form-control" id="nomor_whatsapp" name="nomor_whatsapp" value="<?= old('nomor_whatsapp', $member['nomor_whatsapp']) ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="status_keanggotaan">Status Keanggotaan</label>
                            <select class="form-control" id="status_keanggotaan" name="status_keanggotaan">
                                <option value="active" <?= $member['status_keanggotaan'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="suspended" <?= $member['status_keanggotaan'] == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                <option value="terminated" <?= $member['status_keanggotaan'] == 'terminated' ? 'selected' : '' ?>>Terminated</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="alamat_lengkap">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3"><?= old('alamat_lengkap', $member['alamat_lengkap']) ?></textarea>
                    </div>

                    <hr>
                    <h5>Data Kepegawaian & Akademik</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nidn_nip">NIDN/NIP</label>
                            <input type="text" class="form-control" id="nidn_nip" name="nidn_nip" value="<?= old('nidn_nip', $member['nidn_nip']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="kampus_id">Kampus</label>
                            <select class="form-control select2" id="kampus_id" name="kampus_id">
                                <?php foreach ($kampus_list as $kampus): ?>
                                    <option value="<?= $kampus['id'] ?>" <?= $member['kampus_id'] == $kampus['id'] ? 'selected' : '' ?>><?= esc($kampus['nama_kampus']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="status_kepegawaian_id">Status Kepegawaian</label>
                            <select class="form-control select2" id="status_kepegawaian_id" name="status_kepegawaian_id">
                                <?php foreach ($status_kepegawaian as $status): ?>
                                    <option value="<?= $status['id'] ?>" <?= $member['status_kepegawaian_id'] == $status['id'] ? 'selected' : '' ?>><?= esc($status['nama_status']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="gaji_pokok">Gaji Pokok</label>
                            <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" value="<?= old('gaji_pokok', $member['gaji_pokok']) ?>">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-secondary">Batal</a>
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
        // Inisialisasi Select2 untuk dropdown yang lebih baik
        $('.select2').select2({
            // Opsi tambahan jika diperlukan
        });
    });
</script>
<?= $this->endSection() ?>