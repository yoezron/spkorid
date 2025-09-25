<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Profil Saya
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Profil Saya</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <img src="<?= base_url($member['foto_path'] ?? 'assets/images/avatars/avatar.png') ?>" class="img-fluid rounded-circle" alt="avatar" style="width: 150px; height: 150px; object-fit: cover;">
                    <h5 class="card-title mt-3"><?= esc($member['nama_lengkap']) ?></h5>
                    <p class="card-text text-muted"><?= esc($user['email']) ?></p>
                </div>
                <div class="d-grid gap-2 mt-4">
                    <a href="<?= base_url('member/profile/edit') ?>" class="btn btn-primary">Edit Profil</a>
                    <a href="<?= base_url('member/change-password') ?>" class="btn btn-light">Ubah Password</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Detail Keanggotaan</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Nomor Anggota
                        <strong><?= esc($member['nomor_anggota'] ?? 'N/A') ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Status Keanggotaan
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
                        <span class="badge <?= $statusClass ?>"><?= esc(ucfirst($member['status_keanggotaan'])) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Tanggal Bergabung
                        <strong><?= date('d F Y', strtotime($member['tanggal_bergabung'] ?? time())) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Terakhir Diperbarui
                        <strong><?= date('d F Y, H:i', strtotime($member['updated_at'] ?? time())) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Asal Kampus
                        <strong><?= esc($member['nama_kampus'] ?? 'Belum diisi') ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Program Studi
                        <strong><?= esc($member['nama_prodi'] ?? 'Belum diisi') ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>