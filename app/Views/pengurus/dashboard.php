<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard Pengurus
<?= $this->endSection() ?>

<?= $this->section('pageStyles') ?>
<!-- CDN untuk Ikon Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .widget-card .widget-content {
        padding: 1.5rem;
    }

    .widget-card .avatar {
        width: 60px;
        height: 60px;
        font-size: 1.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .widget-card .info-text h5 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .widget-card .info-text p {
        color: #888ea8;
        margin: 0;
    }

    .card-title h5 {
        font-size: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 15px;
        color: #888ea8;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Sambutan -->
<div class="row">
    <div class="col-12">
        <div class="page-description">
            <h1 class="mb-1">Selamat Datang, <?= esc(session()->get('nama_lengkap') ?? 'Pengurus') ?>!</h1>
            <p>Berikut adalah ringkasan aktivitas terbaru di serikat.</p>
        </div>
    </div>
</div>

<div class="row">

    <!-- 1. KARTU STATISTIK: CALON ANGGOTA -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card widget-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-light-primary text-primary rounded-circle me-3">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="info-text">
                            <h5><?= number_format($member_stats['pending'] ?? 0) ?></h5>
                            <p>Calon Anggota</p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url('pengurus/members/pending') ?>" class="btn btn-sm btn-primary">Verifikasi</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. KARTU STATISTIK: ARTIKEL PENDING -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card widget-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-light-warning text-warning rounded-circle me-3">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="info-text">
                            <h5><?= number_format(count($pending_posts ?? [])) ?></h5>
                            <p>Artikel Pending</p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url('pengurus/blog/pending') ?>" class="btn btn-sm btn-warning">Review</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. KARTU STATISTIK: PENGADUAN BARU -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card widget-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-light-danger text-danger rounded-circle me-3">
                            <i class="bi bi-chat-left-dots"></i>
                        </div>
                        <div class="info-text">
                            <h5><?= number_format($open_pengaduan ?? 0) ?></h5>
                            <p>Pengaduan Baru</p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url('pengurus/pengaduan') ?>" class="btn btn-sm btn-danger">Tangani</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <!-- WIDGET: ANGGOTA MENUNGGU VERIFIKASI -->
    <div class="col-xl-7 col-lg-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Anggota Menunggu Verifikasi</h5>
                <a href="<?= base_url('pengurus/members/pending') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($pending_members)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <?php foreach (array_slice($pending_members, 0, 5) as $member): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= base_url($member['foto_path'] ?? 'neptune-assets/images/avatars/avatar.png') ?>" alt="avatar" class="rounded-circle" width="40" height="40">
                                                <div class="ms-3">
                                                    <h6 class="mb-0"><?= esc($member['nama_lengkap']) ?></h6>
                                                    <small class="text-muted"><?= esc($member['nama_kampus'] ?? 'N/A') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-sm btn-light">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-check2-circle text-success"></i>
                        <p>Hebat! Tidak ada anggota yang menunggu verifikasi.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- WIDGET: AKTIVITAS TERBARU -->
    <div class="col-xl-5 col-lg-12 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Aktivitas Terbaru Serikat</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_activities)): ?>
                    <div class="timeline-simple">
                        <?php foreach (array_slice($recent_activities, 0, 4) as $activity): ?>
                            <div class="timeline-item">
                                <h6 class="mb-0"><?= esc($activity['message']) ?></h6>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= \CodeIgniter\I18n\Time::parse($activity['date'])->humanize() ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-bell-slash"></i>
                        <p>Belum ada aktivitas terbaru yang tercatat.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>