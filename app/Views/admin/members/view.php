<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Anggota: <?= esc($member['nama_lengkap']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Detail Anggota</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="<?= base_url($member['foto_path'] ?? 'neptune-assets/images/avatars/avatar.png') ?>" class="img-fluid rounded-circle mb-3" alt="avatar" style="width: 120px; height: 120px; object-fit: cover;">
                <h5 class="card-title"><?= esc($member['nama_lengkap']) ?></h5>
                <p class="card-text text-muted"><?= esc($member['email']) ?></p>
                <?php
                $statusClass = 'badge-secondary';
                if ($member['status_keanggotaan'] == 'active') {
                    $statusClass = 'badge-success';
                } elseif ($member['status_keanggotaan'] == 'pending') {
                    $statusClass = 'badge-warning';
                } elseif ($member['status_keanggotaan'] == 'suspended') {
                    $statusClass = 'badge-danger';
                }
                ?>
                <span class="badge <?= $statusClass ?> fs-6 mb-3"><?= esc(ucfirst($member['status_keanggotaan'])) ?></span>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/members/edit/' . $member['id']) ?>" class="btn btn-primary">Edit Anggota</a>
                    <?php if ($member['status_keanggotaan'] == 'pending'): ?>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal">Verifikasi</button>
                    <?php elseif ($member['status_keanggotaan'] == 'active'): ?>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#suspendModal">Suspend</button>
                    <?php elseif ($member['status_keanggotaan'] == 'suspended'): ?>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#reactivateModal">Aktifkan Kembali</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="memberTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Detail Profil</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false">Riwayat Iuran</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Nomor Anggota: <strong><?= esc($member['nomor_anggota'] ?? 'N/A') ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Nomor WhatsApp: <strong><?= esc($member['nomor_whatsapp'] ?? 'N/A') ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Alamat: <strong><?= esc($member['alamat_lengkap'] ?? 'N/A') ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Tanggal Bergabung: <strong><?= date('d F Y', strtotime($member['tanggal_bergabung'] ?? time())) ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Asal Kampus: <strong><?= esc($member['nama_kampus'] ?? 'N/A') ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Program Studi: <strong><?= esc($member['nama_prodi'] ?? 'N/A') ?></strong></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($payment_history)): ?>
                                        <?php foreach ($payment_history as $p): ?>
                                            <tr>
                                                <td><?= esc($p['nomor_transaksi']) ?></td>
                                                <td><?= date('d M Y', strtotime($p['tanggal_pembayaran'])) ?></td>
                                                <td>Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $p['status_pembayaran'] == 'verified' ? 'success' : 'warning' ?>"><?= esc(ucfirst($p['status_pembayaran'])) ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada riwayat pembayaran.</td>
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

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyModalLabel">Verifikasi Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memverifikasi anggota <strong><?= esc($member['nama_lengkap']) ?></strong>?</p>
                <p class="text-muted">Anggota akan diaktifkan dan dapat mengakses semua fitur.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= base_url('admin/members/verify/' . $member['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success">Ya, Verifikasi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div class="modal fade" id="suspendModal" tabindex="-1" aria-labelledby="suspendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suspendModalLabel">Suspend Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin men-suspend anggota <strong><?= esc($member['nama_lengkap']) ?></strong>?</p>
                <div class="mb-3">
                    <label for="suspend_reason" class="form-label">Alasan Suspend:</label>
                    <textarea class="form-control" id="suspend_reason" name="suspend_reason" rows="3" placeholder="Masukkan alasan suspend..."></textarea>
                </div>
                <p class="text-warning"><small>Anggota tidak akan bisa mengakses sistem sampai diaktifkan kembali.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= base_url('admin/members/suspend/' . $member['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="reason" id="suspend_reason_hidden">
                    <button type="submit" class="btn btn-warning" onclick="document.getElementById('suspend_reason_hidden').value = document.getElementById('suspend_reason').value;">Ya, Suspend</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reactivate Modal -->
<div class="modal fade" id="reactivateModal" tabindex="-1" aria-labelledby="reactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reactivateModalLabel">Aktifkan Kembali Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengaktifkan kembali anggota <strong><?= esc($member['nama_lengkap']) ?></strong>?</p>
                <p class="text-info">Anggota akan dapat mengakses sistem kembali.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= base_url('admin/members/activate/' . $member['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-info">Ya, Aktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>