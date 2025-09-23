<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard Anggota
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/css/dashboard/dash_2.css') ?>" rel="stylesheet" type="text/css" />
<style>
    .widget-card-four .w-action {
        margin-top: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h5 class="">Selamat Datang, <?= esc(session()->get('nama_lengkap')) ?>!</h5>
            </div>
            <div class="widget-content">
                <p>Ini adalah pusat informasi Anda. Di sini Anda dapat melihat pengumuman terbaru, mengelola profil, dan berpartisipasi dalam kegiatan serikat.</p>
                <p>Nomor Anggota Anda: <strong><?= esc($member['nomor_anggota']) ?></strong></p>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value">Profil Saya</h6>
                        <p class="">Kelola data pribadi Anda.</p>
                    </div>
                </div>
                <div class="w-action">
                    <a href="<?= base_url('member/profile') ?>" class="btn btn-primary">Lihat Profil</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value">Kartu Anggota</h6>
                        <p class="">Lihat & unduh kartu digital.</p>
                    </div>
                </div>
                <div class="w-action">
                    <a href="<?= base_url('member/card') ?>" class="btn btn-info">Lihat Kartu</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value">Riwayat Iuran</h6>
                        <p class="">Lihat histori pembayaran.</p>
                    </div>
                </div>
                <div class="w-action">
                    <a href="<?= base_url('member/payment/history') ?>" class="btn btn-warning">Lihat Riwayat</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-table-three">
            <div class="widget-heading">
                <h5 class="">Informasi & Pengumuman Terbaru</h5>
            </div>
            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-scroll">
                        <thead>
                            <tr>
                                <th>
                                    <div class="th-content">Judul</div>
                                </th>
                                <th>
                                    <div class="th-content">Kategori</div>
                                </th>
                                <th>
                                    <div class="th-content">Tanggal</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_info)): ?>
                                <?php foreach ($recent_info as $info): ?>
                                    <tr>
                                        <td>
                                            <div class="td-content customer-name"><?= esc($info['judul']) ?></div>
                                        </td>
                                        <td>
                                            <div class="td-content"><span class="badge badge-primary"><?= esc($info['kategori']) ?></span></div>
                                        </td>
                                        <td>
                                            <div class="td-content"><?= date('d M Y', strtotime($info['published_at'])) ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="td-content text-center">Belum ada informasi terbaru.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-activity-four">
            <div class="widget-heading">
                <h5 class="">Artikel Terpublikasi Saya</h5>
            </div>
            <div class="widget-content">
                <div class="mt-container mx-auto">
                    <div class="timeline-line">
                        <?php if (!empty($my_posts)): ?>
                            <?php foreach ($my_posts as $post): ?>
                                <div class="item-timeline timeline-primary">
                                    <div class="t-dot" data-original-title="" title=""></div>
                                    <div class="t-text">
                                        <a href="<?= base_url('blog/view/' . $post['slug']) ?>" target="_blank">
                                            <p><?= esc($post['title']) ?></p>
                                        </a>
                                        <span class="badge badge-success">Dilihat <?= esc($post['view_count']) ?> kali</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center">Anda belum memiliki artikel yang dipublikasikan.</p>
                            <div class="text-center mt-3">
                                <a href="<?= base_url('member/posts/create') ?>" class="btn btn-success">Tulis Artikel Sekarang</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>