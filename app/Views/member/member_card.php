<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Kartu Anggota
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Kartu Anggota Digital</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Tanda Pengenal Anggota</h5>
            </div>
            <div class="card-body text-center">
                <p class="card-description">Ini adalah kartu anggota digital Anda. Anda dapat mengunduhnya sebagai PDF.</p>

                <div id="member-card-container" class="mt-4 mb-4">
                    <!-- Menampilkan kartu sebagai HTML biasa -->
                    <div style="width: 336px; height: 210px; border: 1px solid #ccc; border-radius: 10px; margin: auto; padding: 15px; text-align: left; position: relative;">
                        <h6 style="font-weight: bold; margin-bottom: 20px;">KARTU ANGGOTA SPK</h6>
                        <img src="<?= base_url($member['foto_path'] ?? 'assets/images/avatars/avatar.png') ?>" style="width: 80px; height: 100px; object-fit: cover; position: absolute; top: 50px; left: 15px;">
                        <div style="margin-left: 105px; font-size: 12px;">
                            <p><strong>Nama:</strong><br><?= esc($member['nama_lengkap']) ?></p>
                            <p><strong>No. Anggota:</strong><br><?= esc($member['nomor_anggota']) ?></p>
                            <p><strong>Kampus:</strong><br><?= esc($member['nama_kampus']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="<?= base_url('member/card/download') ?>" class="btn btn-primary">
                        <i class="material-icons-outlined">download</i> Unduh PDF
                    </a>
                    <a href="<?= base_url('member/profile') ?>" class="btn btn-light">
                        <i class="material-icons-outlined">arrow_back</i> Kembali ke Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card widget widget-info">
            <div class="card-body">
                <div class="widget-info-container">
                    <div class="widget-info-image" style="background: url(<?= base_url('neptune-assets/images/widgets/security.svg') ?>)"></div>
                    <h5 class="widget-info-title">Jaga Kerahasiaan Data</h5>
                    <p class="widget-info-text m-t-n-xs">
                        Harap jaga kerahasiaan informasi yang tertera pada kartu anggota Anda. Jangan membagikan QR Code secara sembarangan untuk menghindari penyalahgunaan data.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>