<?= $this->extend('layouts/main') ?>

<?php // Sesuaikan judul halaman 
?>
<?= $this->section('title') ?>
Dashboard Admin
<?= $this->endSection() ?>

<?php // Sisipkan CSS khusus untuk halaman ini 
?>
<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/apex/apexcharts.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('assets/css/dashboard/dash_1.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?php // Konten utama halaman 
?>
<?= $this->section('content') ?>

<div class="row layout-top-spacing">

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= number_format($statistics['active'] ?? 0) ?></h6>
                        <p class="">Anggota Aktif</p>
                    </div>
                    <div class="w-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-gradient-secondary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= number_format($statistics['pending'] ?? 0) ?></h6>
                        <p class="">Pending Verifikasi</p>
                    </div>
                    <div class="w-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <polyline points="17 11 19 13 23 9"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-gradient-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value">Rp <?= number_format($payment_summary['total_verified'] ?? 0, 0, ',', '.') ?></h6>
                        <p class="">Iuran Bulan Ini</p>
                    </div>
                    <div class="w-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-gradient-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h5 class="">Statistik Pendaftaran</h5>
            </div>
            <div class="widget-content">
                <div id="registrationStats"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-chart-two">
            <div class="widget-heading">
                <h5 class="">Ringkasan Aktivitas</h5>
            </div>
            <div class="widget-content">
                <div id="summary-chart" class=""></div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-table-three">
            <div class="widget-heading">
                <h5 class="">Anggota Terbaru</h5>
            </div>
            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-scroll">
                        <thead>
                            <tr>
                                <th>
                                    <div class="th-content">Nama</div>
                                </th>
                                <th>
                                    <div class="th-content">Status</div>
                                </th>
                                <th>
                                    <div class="th-content">Aksi</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_members)): ?>
                                <?php foreach (array_slice($recent_members, 0, 5) as $member): ?>
                                    <tr>
                                        <td>
                                            <div class="td-content customer-name"><?= esc($member['nama_lengkap']) ?></div>
                                        </td>
                                        <td>
                                            <div class="td-content">
                                                <span class="badge <?= $member['status_keanggotaan'] == 'active' ? 'badge-success' : 'badge-warning' ?>">
                                                    <?= esc(ucfirst($member['status_keanggotaan'])) ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="td-content">
                                                <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-sm btn-primary">Detail</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="td-content text-center">Belum ada anggota baru.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-table-three">
            <div class="widget-heading">
                <h5 class="">Pembayaran Menunggu Verifikasi</h5>
            </div>
            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-scroll">
                        <thead>
                            <tr>
                                <th>
                                    <div class="th-content">Nama</div>
                                </th>
                                <th>
                                    <div class="th-content">Jumlah</div>
                                </th>
                                <th>
                                    <div class="th-content">Aksi</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_payments)): ?>
                                <?php foreach (array_slice($pending_payments, 0, 5) as $payment): ?>
                                    <tr>
                                        <td>
                                            <div class="td-content customer-name"><?= esc($payment['nama_lengkap']) ?></div>
                                        </td>
                                        <td>
                                            <div class="td-content">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></div>
                                        </td>
                                        <td>
                                            <div class="td-content">
                                                <a href="<?= base_url('admin/payments/pending') ?>" class="btn btn-sm btn-warning">Verifikasi</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="td-content text-center">Tidak ada pembayaran pending.</div>
                                    </td>
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

<?php // Sisipkan JS khusus untuk halaman ini 
?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/apex/apexcharts.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Grafik Pendaftaran Anggota (Area Chart)
        var registrationOptions = {
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false,
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            series: [{
                name: 'Pendaftar Baru',
                // Data ini sebaiknya diambil dari controller, contoh: [10, 15, 7, 22, 18, 25, 20]
                data: [31, 40, 28, 51, 42, 109, 100]
            }],
            xaxis: {
                // Label ini juga sebaiknya dari controller, contoh: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
                categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy'
                },
            }
        };
        var registrationChart = new ApexCharts(document.querySelector("#registrationStats"), registrationOptions);
        registrationChart.render();

        // Grafik Ringkasan (Donut Chart)
        var summaryOptions = {
            chart: {
                type: 'donut',
                width: 380
            },
            colors: ['#2196f3', '#e2a03f', '#e7515a'],
            series: [
                <?= (int)($statistics['total'] ?? 0) ?>,
                <?= (int)($pengaduan_stats['total'] ?? 0) ?>,
                <?= (int)($total_posts ?? 0) ?>
            ],
            labels: ['Total Anggota', 'Total Pengaduan', 'Total Artikel'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
        var summaryChart = new ApexCharts(document.querySelector("#summary-chart"), summaryOptions);
        summaryChart.render();
    });
</script>
<?= $this->endSection() ?>