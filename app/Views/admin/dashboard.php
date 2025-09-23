<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="dashboard-container">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?= number_format($statistics['total_members']) ?></h3>
                <p>Total Anggota Aktif</p>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 12% bulan ini
                </span>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?= number_format($statistics['pending_members']) ?></h3>
                <p>Pending Verifikasi</p>
                <a href="<?= base_url('admin/members/pending') ?>" class="stat-link">
                    Lihat Detail <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <h3>Rp <?= number_format($payment_summary['total_verified'] ?? 0) ?></h3>
                <p>Total Iuran Bulan Ini</p>
                <span class="stat-change positive">
                    <i class="fas fa-check"></i> <?= $payment_summary['verified_count'] ?? 0 ?> pembayaran
                </span>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3><?= $pengaduan_stats['new'] ?? 0 ?></h3>
                <p>Pengaduan Baru</p>
                <a href="<?= base_url('admin/pengaduan') ?>" class="stat-link">
                    Tangani Sekarang <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="dashboard-row">
        <div class="dashboard-col-8">
            <div class="card">
                <div class="card-header">
                    <h4>Statistik Keanggotaan</h4>
                    <div class="card-tools">
                        <select class="form-control-sm" id="chart-period">
                            <option value="7">7 Hari</option>
                            <option value="30" selected>30 Hari</option>
                            <option value="90">3 Bulan</option>
                            <option value="365">1 Tahun</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="membershipChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="dashboard-col-4">
            <div class="card">
                <div class="card-header">
                    <h4>Distribusi Anggota</h4>
                </div>
                <div class="card-body">
                    <canvas id="distributionChart" height="200"></canvas>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #4CAF50"></span>
                            <span>Laki-laki (<?= $statistics['male_members'] ?>)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #FF9800"></span>
                            <span>Perempuan (<?= $statistics['female_members'] ?>)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="dashboard-row">
        <div class="dashboard-col-6">
            <div class="card">
                <div class="card-header">
                    <h4>Anggota Baru</h4>
                    <a href="<?= base_url('admin/members') ?>" class="btn btn-sm btn-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="member-list">
                        <?php foreach ($recent_members as $member): ?>
                            <div class="member-item">
                                <img src="<?= $member['foto'] ?? base_url('images/default-avatar.png') ?>"
                                    alt="<?= $member['nama_lengkap'] ?>"
                                    class="member-avatar">
                                <div class="member-info">
                                    <h5><?= $member['nama_lengkap'] ?></h5>
                                    <p><?= $member['nama_kampus'] ?? 'Kampus tidak diketahui' ?></p>
                                </div>
                                <div class="member-meta">
                                    <span class="badge badge-success">Baru</span>
                                    <small><?= date('d/m/Y', strtotime($member['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-col-6">
            <div class="card">
                <div class="card-header">
                    <h4>Pembayaran Pending</h4>
                    <a href="<?= base_url('admin/payments/pending') ?>" class="btn btn-sm btn-warning">
                        Verifikasi
                    </a>
                </div>
                <div class="card-body">
                    <div class="payment-list">
                        <?php foreach ($pending_payments as $payment): ?>
                            <div class="payment-item">
                                <div class="payment-info">
                                    <h5><?= $payment['nama_lengkap'] ?></h5>
                                    <p>Rp <?= number_format($payment['amount']) ?></p>
                                </div>
                                <div class="payment-actions">
                                    <button onclick="verifyPayment(<?= $payment['id'] ?>)"
                                        class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectPayment(<?= $payment['id'] ?>)"
                                        class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($pending_payments)): ?>
                            <p class="text-muted text-center">Tidak ada pembayaran pending</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h4>Aksi Cepat</h4>
        <div class="action-buttons">
            <a href="<?= base_url('admin/members/create') ?>" class="action-btn">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Anggota</span>
            </a>
            <a href="<?= base_url('admin/blog/create') ?>" class="action-btn">
                <i class="fas fa-pen"></i>
                <span>Buat Artikel</span>
            </a>
            <a href="<?= base_url('admin/surveys/create') ?>" class="action-btn">
                <i class="fas fa-poll"></i>
                <span>Buat Survei</span>
            </a>
            <a href="<?= base_url('admin/informasi/create') ?>" class="action-btn">
                <i class="fas fa-bullhorn"></i>
                <span>Kirim Informasi</span>
            </a>
            <a href="<?= base_url('admin/reports') ?>" class="action-btn">
                <i class="fas fa-chart-bar"></i>
                <span>Lihat Laporan</span>
            </a>
            <a href="<?= base_url('admin/system/backup') ?>" class="action-btn">
                <i class="fas fa-database"></i>
                <span>Backup Data</span>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Membership Chart
    const membershipCtx = document.getElementById('membershipChart').getContext('2d');
    new Chart(membershipCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Anggota Aktif',
                data: [120, 135, 140, 155, 168, 180],
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                tension: 0.4
            }, {
                label: 'Anggota Baru',
                data: [10, 15, 5, 20, 13, 12],
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Distribution Chart
    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{
                data: [<?= $statistics['male_members'] ?>, <?= $statistics['female_members'] ?>],
                backgroundColor: ['#4CAF50', '#FF9800']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Quick action functions
    function verifyPayment(id) {
        if (confirm('Verifikasi pembayaran ini?')) {
            window.location.href = `<?= base_url('admin/payments/verify') ?>/${id}`;
        }
    }

    function rejectPayment(id) {
        if (confirm('Tolak pembayaran ini?')) {
            window.location.href = `<?= base_url('admin/payments/reject') ?>/${id}`;
        }
    }
</script>
<?= $this->endSection() ?>