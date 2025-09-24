<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard Admin
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Dashboard</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">groups</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Anggota Aktif</span>
                        <span class="widget-stats-amount"><?= number_format($statistics['active'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-warning">
                        <i class="material-icons-outlined">pending</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Pending Verifikasi</span>
                        <span class="widget-stats-amount"><?= number_format($statistics['pending'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-success">
                        <i class="material-icons-outlined">paid</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Iuran Terverifikasi</span>
                        <span class="widget-stats-amount">Rp <?= number_format($payment_summary['verified'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-danger">
                        <i class="material-icons-outlined">person</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Anggota</span>
                        <span class="widget-stats-amount"><?= number_format($statistics['total'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card widget widget-list">
            <div class="card-header">
                <h5 class="card-title">Anggota Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_members)): ?>
                                <?php foreach (array_slice($recent_members, 0, 5) as $member): ?>
                                    <tr>
                                        <td><?= esc($member['nama_lengkap']) ?></td>
                                        <td>
                                            <span class="badge <?= $member['status_keanggotaan'] == 'active' ? 'badge-success' : 'badge-warning' ?>">
                                                <?= esc(ucfirst($member['status_keanggotaan'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-sm btn-light">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada anggota baru.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card widget widget-list">
            <div class="card-header">
                <h5 class="card-title">Pembayaran Menunggu Verifikasi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_payments)): ?>
                                <?php foreach (array_slice($pending_payments, 0, 5) as $payment): ?>
                                    <tr>
                                        <td><?= esc($payment['nama_lengkap']) ?></td>
                                        <td>Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/payments/pending') ?>" class="btn btn-sm btn-warning">Verifikasi</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada pembayaran pending.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>