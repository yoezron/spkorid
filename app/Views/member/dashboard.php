<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard Anggota
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
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Selamat Datang, <?= esc(session()->get('nama_lengkap')) ?>!</h5>
                <p>Ini adalah pusat informasi Anda. Di sini Anda dapat melihat pengumuman terbaru, mengelola profil, dan berpartisipasi dalam kegiatan serikat.</p>
                <p>Nomor Anggota Anda: <strong><?= esc($member['nomor_anggota'] ?? 'N/A') ?></strong></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex align-items-center">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">person</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Profil Saya</span>
                        <a href="<?= base_url('member/profile') ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex align-items-center">
                    <div class="widget-stats-icon widget-stats-icon-info">
                        <i class="material-icons-outlined">badge</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Kartu Anggota</span>
                        <a href="<?= base_url('member/card') ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex align-items-center">
                    <div class="widget-stats-icon widget-stats-icon-warning">
                        <i class="material-icons-outlined">history</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Riwayat Iuran</span>
                        <a href="<?= base_url('member/payment/history') ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-7">
        <div class="card widget widget-list">
            <div class="card-header">
                <h5 class="card-title">Informasi & Pengumuman Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_info)): ?>
                                <?php foreach ($recent_info as $info): ?>
                                    <tr>
                                        <td><?= esc($info['judul']) ?></td>
                                        <td><span class="badge badge-primary"><?= esc($info['kategori']) ?></span></td>
                                        <td><?= date('d M Y', strtotime($info['published_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada informasi terbaru.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card widget widget-list">
            <div class="card-header">
                <h5 class="card-title">Artikel Terpublikasi Saya</h5>
            </div>
            <div class="card-body">
                <ul class="widget-list-content list-unstyled">
                    <?php if (!empty($my_posts)): ?>
                        <?php foreach ($my_posts as $post): ?>
                            <li class="widget-list-item">
                                <span class="widget-list-item-icon"><i class="material-icons-outlined">article</i></span>
                                <span class="widget-list-item-description">
                                    <a href="<?= base_url('blog/view/' . $post['slug']) ?>" target="_blank" class="widget-list-item-description-title">
                                        <?= esc($post['title']) ?>
                                    </a>
                                    <span class="widget-list-item-description-subtitle">
                                        Dilihat <?= esc($post['view_count']) ?> kali
                                    </span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-center p-3">
                            <p>Anda belum memiliki artikel yang dipublikasikan.</p>
                            <a href="<?= base_url('member/posts/create') ?>" class="btn btn-success mt-2">Tulis Artikel Sekarang</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>