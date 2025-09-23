<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Kartu Anggota Digital
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .member-card-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .member-card {
        width: 500px;
        height: 300px;
        background: linear-gradient(135deg, #4361ee, #303f9f);
        color: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .member-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .card-header {
        display: flex;
        align-items: center;
    }

    .card-header img {
        width: 40px;
        height: 40px;
        margin-right: 15px;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 600;
    }

    .card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .member-info {
        flex-grow: 1;
    }

    .member-info p {
        margin: 0;
        font-size: 14px;
        opacity: 0.8;
    }

    .member-info h4 {
        margin: 5px 0 10px 0;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .member-photo {
        width: 80px;
        height: 80px;
        border: 3px solid white;
        border-radius: 10px;
        object-fit: cover;
    }

    .qr-code {
        padding: 5px;
        background: white;
        border-radius: 5px;
    }

    .qr-code img {
        width: 80px;
        height: 80px;
        display: block;
    }

    .card-footer {
        font-size: 12px;
        opacity: 0.7;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-6 p-4">
                <div class="d-flex justify-content-between">
                    <h4>Kartu Anggota Digital Saya</h4>
                    <a href="<?= base_url('member/card/download') ?>" class="btn btn-success">
                        <i data-feather="download"></i> Unduh PDF
                    </a>
                </div>
                <p>Berikut adalah kartu tanda anggota digital Anda. Anda dapat menunjukkannya atau mengunduh versi PDF.</p>
                <hr>

                <div class="member-card-container">
                    <div class="member-card">
                        <div class="card-header">
                            <img src="<?= base_url('assets/img/logo.svg') ?>" alt="Logo">
                            <h5>SERIKAT PEKERJA KAMPUS</h5>
                        </div>

                        <div class="card-body">
                            <div class="member-info">
                                <p>Nama Lengkap</p>
                                <h4><?= esc(strtoupper($member['nama_lengkap'])) ?></h4>
                                <p>Nomor Anggota</p>
                                <h4><?= esc($member['nomor_anggota']) ?></h4>
                            </div>
                            <?php
                            $foto = $member['foto_path'];
                            $default_foto = base_url('assets/img/90x90.jpg');
                            $user_foto = ($foto && file_exists(FCPATH . $foto)) ? base_url($foto) : $default_foto;
                            ?>
                            <img src="<?= $user_foto ?>" alt="Foto Profil" class="member-photo">
                        </div>

                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <span>Bergabung Sejak: <?= date('d M Y', strtotime($member['tanggal_bergabung'])) ?></span>
                            <div class="qr-code">
                                <?php
                                // Logika untuk generate QR Code (misalnya menggunakan library)
                                // Untuk sementara, kita gunakan placeholder
                                $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($member['nomor_anggota']);
                                ?>
                                <img src="<?= $qrCodeUrl ?>" alt="QR Code">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>