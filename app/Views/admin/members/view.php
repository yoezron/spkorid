<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Anggota: <?= esc($member['nama_lengkap']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/css/users/user-profile.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('assets/css/components/tabs-accordian/custom-tabs.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-spacing">

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 layout-top-spacing">

                <div class="user-profile layout-spacing">
                    <div class="widget-content widget-content-area">
                        <div class="d-flex justify-content-between">
                            <h3 class="">Detail Profil Anggota</h3>
                            <a href="<?= base_url('admin/members/edit/' . $member['id']) ?>" class="mt-2 edit-profile">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
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
                            <span class="badge badge-<?= $member['status_keanggotaan'] == 'active' ? 'success' : 'warning' ?>">
                                <?= esc(ucfirst($member['status_keanggotaan'])) ?>
                            </span>
                        </div>
                        <div class="user-info-list">
                            <div class="text-center">
                                <ul class="contacts-block list-unstyled">
                                    <li class="contacts-block__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <?= esc($member['email']) ?>
                                    </li>
                                    <li class="contacts-block__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                        </svg>
                                        <?= esc($member['nomor_whatsapp']) ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($member['status_keanggotaan'] === 'pending'): ?>
                    <div class="widget-content widget-content-area text-center layout-spacing">
                        <h4 class="mb-4">Tindakan Verifikasi</h4>
                        <p>Tinjau data pendaftar dengan seksama. Setelah yakin, pilih tindakan di bawah ini.</p>
                        <div class="d-flex justify-content-center">
                            <form action="<?= base_url('admin/members/verify/' . $member['id']) ?>" method="post" class="mr-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="notes" value="Disetujui oleh admin.">
                                <button type="submit" class="btn btn-success">
                                    <i data-feather="check-circle" class="mr-1"></i> Verifikasi & Setujui
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectionModal">
                                <i data-feather="x-circle" class="mr-1"></i> Tolak Pendaftaran
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="widget-content widget-content-area">
                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">Data Pribadi & Akademik</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="employment-tab" data-toggle="tab" href="#employment" role="tab" aria-controls="employment" aria-selected="false">Data Kepegawaian</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payment-tab" data-toggle="tab" href="#payment" role="tab" aria-controls="payment" aria-selected="false">Dokumen & Pembayaran</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <p><strong>Nama Lengkap:</strong> <?= esc($member['nama_lengkap']) ?></p>
                            <p><strong>Jenis Kelamin:</strong> <?= esc($member['jenis_kelamin']) ?></p>
                            <p><strong>Alamat:</strong> <?= esc($member['alamat_lengkap']) ?></p>
                            <hr>
                            <p><strong>Kampus:</strong> <?= esc($member['nama_kampus']) ?></p>
                            <p><strong>Program Studi:</strong> <?= esc($member['nama_prodi']) ?></p>
                            <p><strong>NIDN/NIP:</strong> <?= esc($member['nidn_nip'] ?: 'Tidak ada') ?></p>
                            <p><strong>Bidang Keahlian:</strong> <?= esc($member['bidang_keahlian']) ?></p>
                            <p><strong>Motivasi:</strong> <?= esc($member['motivasi_berserikat']) ?></p>
                        </div>

                        <div class="tab-pane fade" id="employment" role="tabpanel" aria-labelledby="employment-tab">
                            <p><strong>Status Kepegawaian:</strong> <?= esc($member['status_kepegawaian']) ?></p>
                            <p><strong>Pemberi Gaji:</strong> <?= esc($member['pemberi_gaji']) ?></p>
                            <p><strong>Range Gaji:</strong> <?= esc($member['range_gaji']) ?></p>
                            <p><strong>Gaji Pokok:</strong> Rp <?= number_format($member['gaji_pokok'], 0, ',', '.') ?></p>
                        </div>

                        <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                            <h5>Bukti Pembayaran Pendaftaran</h5>
                            <?php if ($member['bukti_pembayaran_path'] && file_exists(FCPATH . $member['bukti_pembayaran_path'])): ?>
                                <a href="<?= base_url($member['bukti_pembayaran_path']) ?>" target="_blank">
                                    <img src="<?= base_url($member['bukti_pembayaran_path']) ?>" alt="bukti bayar" class="img-fluid" style="max-width: 400px;">
                                </a>
                            <?php else: ?>
                                <p class="text-danger">Bukti pembayaran tidak ditemukan.</p>
                            <?php endif; ?>
                            <hr>
                            <h5>Riwayat Pembayaran Lainnya</h5>
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

<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectionModalLabel">Tolak Pendaftaran Anggota</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/members/reject/' . $member['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Anda akan menolak pendaftaran dari <strong><?= esc($member['nama_lengkap']) ?></strong>.</p>
                    <div class="form-group">
                        <label for="rejection_reason">Alasan Penolakan (Wajib diisi)</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pendaftaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Inisialisasi Feather Icons
    feather.replace();
</script>
<?= $this->endSection() ?>