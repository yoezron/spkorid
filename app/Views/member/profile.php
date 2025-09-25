<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Profil Saya
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description d-flex justify-content-between align-items-center">
            <div>
                <h1>Profil Saya</h1>
                <span>Informasi lengkap keanggotaan Anda di Serikat Pekerja Kampus</span>
            </div>
            <div>
                <a href="<?= base_url('member/card') ?>" class="btn btn-outline-primary">
                    <i class="material-icons">badge</i> Kartu Anggota
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="material-icons">check_circle</i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="material-icons">error</i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Left Column - Profile Card -->
    <div class="col-xl-4">
        <!-- Profile Picture Card -->
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="avatar-upload-container position-relative d-inline-block">
                        <img src="<?= base_url($member['foto_path'] ?? 'assets/images/avatars/avatar.png') ?>"
                            class="img-fluid rounded-circle border border-4 border-light shadow"
                            alt="avatar"
                            style="width: 180px; height: 180px; object-fit: cover;">
                        <button class="btn btn-sm btn-primary rounded-circle position-absolute"
                            style="bottom: 10px; right: 10px; width: 40px; height: 40px;"
                            onclick="document.getElementById('photoUpload').click()">
                            <i class="material-icons" style="font-size: 18px;">camera_alt</i>
                        </button>
                        <input type="file" id="photoUpload" style="display: none;" accept="image/*">
                    </div>

                    <h4 class="card-title mt-3 mb-1"><?= esc($member['nama_lengkap']) ?></h4>
                    <p class="text-muted mb-1">
                        <i class="material-icons" style="font-size: 16px; vertical-align: middle;">badge</i>
                        <?= esc($member['nomor_anggota'] ?? 'N/A') ?>
                    </p>

                    <?php
                    $statusClass = 'badge-secondary';
                    $statusIcon = 'pending';
                    if ($member['status_keanggotaan'] == 'active') {
                        $statusClass = 'badge-success';
                        $statusIcon = 'verified';
                    } elseif ($member['status_keanggotaan'] == 'pending') {
                        $statusClass = 'badge-warning';
                        $statusIcon = 'schedule';
                    } elseif ($member['status_keanggotaan'] == 'suspended') {
                        $statusClass = 'badge-danger';
                        $statusIcon = 'block';
                    }
                    ?>
                    <span class="badge <?= $statusClass ?> fs-6 px-3 py-2">
                        <i class="material-icons" style="font-size: 14px; vertical-align: middle;"><?= $statusIcon ?></i>
                        <?= esc(ucfirst($member['status_keanggotaan'])) ?>
                    </span>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="<?= base_url('member/profile/edit') ?>" class="btn btn-primary">
                        <i class="material-icons">edit</i> Edit Profil
                    </a>
                    <a href="<?= base_url('member/change-password') ?>" class="btn btn-light">
                        <i class="material-icons">lock</i> Ubah Password
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats Card -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title mb-3">Statistik Keanggotaan</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="mb-0 text-primary">
                                <?php
                                $joinDate = strtotime($member['tanggal_bergabung'] ?? 'now');
                                $months = floor((time() - $joinDate) / (30 * 24 * 60 * 60));
                                echo $months;
                                ?>
                            </h4>
                            <small class="text-muted">Bulan Bergabung</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="mb-0 text-success">
                                <?= count($payment_history ?? []) ?>
                            </h4>
                            <small class="text-muted">Pembayaran Iuran</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Detailed Information -->
    <div class="col-xl-8">
        <!-- Navigation Tabs -->
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
                            <i class="material-icons">school</i> Akademik
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#payment" type="button">
                            <i class="material-icons">payment</i> Riwayat Iuran
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- Personal Data Tab -->
                    <div class="tab-pane fade show active" id="personal">
                        <h5 class="mb-4">Informasi Pribadi</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Nama Lengkap</label>
                                <p class="fw-semibold"><?= esc($member['nama_lengkap']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Email</label>
                                <p class="fw-semibold">
                                    <i class="material-icons" style="font-size: 16px;">email</i>
                                    <?= esc($member['email']) ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Jenis Kelamin</label>
                                <p class="fw-semibold"><?= esc($member['jenis_kelamin'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Nomor WhatsApp</label>
                                <p class="fw-semibold">
                                    <i class="material-icons" style="font-size: 16px;">phone</i>
                                    <?= esc($member['nomor_whatsapp']) ?>
                                    <a href="https://wa.me/<?= str_replace(['+', ' ', '-'], '', $member['nomor_whatsapp']) ?>"
                                        target="_blank" class="btn btn-sm btn-outline-success ms-2">
                                        <i class="material-icons" style="font-size: 14px;">chat</i>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Tempat, Tanggal Lahir</label>
                                <p class="fw-semibold">
                                    <?= esc($member['tempat_lahir'] ?? '-') ?>,
                                    <?= $member['tanggal_lahir'] ? date('d F Y', strtotime($member['tanggal_lahir'])) : '-' ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Tanggal Bergabung</label>
                                <p class="fw-semibold">
                                    <i class="material-icons" style="font-size: 16px;">event</i>
                                    <?= date('d F Y', strtotime($member['tanggal_bergabung'] ?? 'now')) ?>
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small">Alamat Lengkap</label>
                                <p class="fw-semibold">
                                    <i class="material-icons" style="font-size: 16px;">location_on</i>
                                    <?= esc($member['alamat_lengkap']) ?>
                                </p>
                            </div>
                            <?php if ($member['media_sosial']): ?>
                                <div class="col-12">
                                    <label class="text-muted small">Media Sosial</label>
                                    <p class="fw-semibold"><?= esc($member['media_sosial']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Employment Tab -->
                    <div class="tab-pane fade" id="employment">
                        <h5 class="mb-4">Informasi Kepegawaian</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">NIDN/NIP</label>
                                <p class="fw-semibold"><?= esc($member['nidn_nip'] ?? 'Belum diisi') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Status Kepegawaian</label>
                                <p class="fw-semibold"><?= esc($member['status_kepegawaian'] ?? 'Belum diisi') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Pemberi Gaji</label>
                                <p class="fw-semibold"><?= esc($member['pemberi_gaji'] ?? 'Belum diisi') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Range Gaji</label>
                                <p class="fw-semibold"><?= esc($member['range_gaji'] ?? 'Belum diisi') ?></p>
                            </div>
                            <?php if ($member['gaji_pokok']): ?>
                                <div class="col-md-6">
                                    <label class="text-muted small">Gaji Pokok</label>
                                    <p class="fw-semibold">Rp <?= number_format($member['gaji_pokok'], 0, ',', '.') ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <label class="text-muted small">Jabatan Fungsional</label>
                                <p class="fw-semibold"><?= esc($member['jabatan_fungsional'] ?? 'Belum diisi') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Golongan/Pangkat</label>
                                <p class="fw-semibold"><?= esc($member['golongan_pangkat'] ?? 'Belum diisi') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Tab -->
                    <div class="tab-pane fade" id="academic">
                        <h5 class="mb-4">Informasi Akademik</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Pendidikan Terakhir</label>
                                <p class="fw-semibold"><?= esc($member['pendidikan_terakhir'] ?? 'Belum diisi') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Jenis Perguruan Tinggi</label>
                                <p class="fw-semibold"><?= esc($member['jenis_pt'] ?? 'Belum diisi') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Asal Kampus</label>
                                <p class="fw-semibold">
                                    <i class="material-icons" style="font-size: 16px;">account_balance</i>
                                    <?= esc($member['nama_kampus'] ?? 'Belum diisi') ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Program Studi</label>
                                <p class="fw-semibold">
                                    <i class="material-icons" style="font-size: 16px;">menu_book</i>
                                    <?= esc($member['nama_prodi'] ?? 'Belum diisi') ?>
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small">Bidang Keahlian/Expertise</label>
                                <p class="fw-semibold"><?= esc($member['bidang_keahlian'] ?? 'Belum diisi') ?></p>
                            </div>
                            <?php if ($member['motivasi_berserikat']): ?>
                                <div class="col-12">
                                    <label class="text-muted small">Motivasi Bergabung dengan Serikat</label>
                                    <div class="alert alert-light">
                                        <p class="mb-0"><?= nl2br(esc($member['motivasi_berserikat'])) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment History Tab -->
                    <div class="tab-pane fade" id="payment">
                        <h5 class="mb-4">Riwayat Pembayaran Iuran</h5>
                        <?php if (!empty($payment_history)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No. Transaksi</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payment_history as $index => $payment): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <code><?= esc($payment['nomor_transaksi']) ?></code>
                                                </td>
                                                <td><?= date('d M Y', strtotime($payment['tanggal_pembayaran'])) ?></td>
                                                <td>
                                                    <strong>Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></strong>
                                                </td>
                                                <td>
                                                    <?php if ($payment['status_pembayaran'] == 'verified'): ?>
                                                        <span class="badge badge-success">
                                                            <i class="material-icons" style="font-size: 14px;">check</i> Terverifikasi
                                                        </span>
                                                    <?php elseif ($payment['status_pembayaran'] == 'pending'): ?>
                                                        <span class="badge badge-warning">
                                                            <i class="material-icons" style="font-size: 14px;">schedule</i> Menunggu
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">
                                                            <i class="material-icons" style="font-size: 14px;">close</i> Ditolak
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#paymentDetail<?= $payment['id'] ?>">
                                                        <i class="material-icons" style="font-size: 16px;">visibility</i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="material-icons">info</i>
                                <span class="ms-2">Belum ada riwayat pembayaran iuran.</span>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPaymentModal">
                                <i class="material-icons">upload</i> Upload Bukti Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Card -->
        <?php if ($member['status_keanggotaan'] == 'pending'): ?>
            <div class="card mt-3">
                <div class="card-body bg-warning bg-opacity-10">
                    <h6 class="card-title text-warning">
                        <i class="material-icons">info</i> Status Keanggotaan Pending
                    </h6>
                    <p class="card-text">
                        Keanggotaan Anda sedang dalam proses verifikasi oleh pengurus.
                        Pastikan Anda telah melengkapi semua data dan mengunggah bukti pembayaran iuran pertama.
                    </p>
                    <?php if ($member['catatan_verifikasi']): ?>
                        <div class="alert alert-warning mt-3">
                            <strong>Catatan dari Pengurus:</strong><br>
                            <?= esc($member['catatan_verifikasi']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Payment Modal -->
<div class="modal fade" id="uploadPaymentModal" tabindex="-1" aria-labelledby="uploadPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPaymentModalLabel">Unggah Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- PERUBAHAN DI SINI: form action dan penambahan field -->
            <form action="<?= base_url('member/payment/uploadProof') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="payment_type" value="Iuran Rutin">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah Pembayaran</label>
                        <input type="number" class="form-control" id="amount" name="amount" required placeholder="Contoh: 50000">
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Tanggal Pembayaran</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <input type="text" class="form-control" id="metode_pembayaran" name="metode_pembayaran" required placeholder="Contoh: Transfer Bank BNI">
                    </div>
                    <div class="mb-3">
                        <label for="payment_proof" class="form-label">Bukti Pembayaran</label>
                        <input class="form-control" type="file" id="payment_proof" name="payment_proof" required accept="image/*,.pdf">
                        <div class="form-text">File: PNG, JPG, JPEG, PDF. Maksimal 5MB.</div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for photo upload -->
<script>
    document.getElementById('photoUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('photo', file);

            fetch('<?= base_url('member/profile/upload-photo') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal upload foto: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat upload foto');
                });
        }
    });
</script>

<?= $this->endSection() ?>