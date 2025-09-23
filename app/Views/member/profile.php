<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Profil Saya
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/css/users/user-profile.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('assets/css/components/tabs-accordian/custom-tabs.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row layout-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 layout-top-spacing">
            <div class="user-profile layout-spacing">
                <div class="widget-content widget-content-area">
                    <div class="d-flex justify-content-between">
                        <h3 class="">Profil Saya</h3>
                        <a href="<?= base_url('member/edit-profile') ?>" class="mt-2 edit-profile">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                            <span class="ml-2">Edit Profil</span>
                        </a>
                    </div>
                    <div class="text-center user-info">
                        <?php
                        $foto = $member['foto_path'];
                        $default_foto = base_url('assets/img/90x90.jpg');
                        $user_foto = ($foto && file_exists(FCPATH . $foto)) ? base_url($foto) : $default_foto;
                        ?>
                        <img src="<?= $user_foto ?>" alt="avatar" width="90" height="90">
                        <p class=""><?= esc($member['nama_lengkap']) ?></p>
                        <span class="badge badge-success"><?= esc(ucfirst($member['status_keanggotaan'])) ?></span>
                    </div>
                    <div class="user-info-list">
                        <div class="text-center">
                            <ul class="contacts-block list-unstyled">
                                <li class="contacts-block__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <?= esc($member['user_email']) ?>
                                </li>
                                <li class="contacts-block__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    <?= esc($member['nomor_whatsapp']) ?>
                                </li>
                                <li class="contacts-block__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <?= esc($member['nama_kota']) ?>, <?= esc($member['nama_provinsi']) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="widget-content widget-content-area mt-4">
                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="academic-tab" data-toggle="tab" href="#academic" role="tab" aria-controls="academic" aria-selected="true">Data Akademik & Kepegawaian</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payment-tab" data-toggle="tab" href="#payment" role="tab" aria-controls="payment" aria-selected="false">Riwayat Iuran</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active p-3" id="academic" role="tabpanel" aria-labelledby="academic-tab">
                            <p><strong>Kampus:</strong> <?= esc($member['nama_kampus']) ?></p>
                            <p><strong>Program Studi:</strong> <?= esc($member['nama_prodi']) ?></p>
                            <p><strong>Status Kepegawaian:</strong> <?= esc($member['status_kepegawaian']) ?></p>
                            <p><strong>Pemberi Gaji:</strong> <?= esc($member['pemberi_gaji']) ?></p>
                            <p><strong>Bidang Keahlian:</strong> <?= esc($member['bidang_keahlian']) ?></p>
                        </div>
                        <div class="tab-pane fade p-3" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tgl. Bayar</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($payment_history)): ?>
                                            <?php foreach ($payment_history as $payment): ?>
                                                <tr>
                                                    <td><?= date('d M Y', strtotime($payment['tanggal_pembayaran'])) ?></td>
                                                    <td><?= esc(ucfirst(str_replace('_', ' ', $payment['jenis_pembayaran']))) ?></td>
                                                    <td>Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                                    <td><span class="badge badge-<?= $payment['status_pembayaran'] == 'verified' ? 'success' : 'warning' ?>"><?= esc($payment['status_pembayaran']) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada riwayat pembayaran.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>