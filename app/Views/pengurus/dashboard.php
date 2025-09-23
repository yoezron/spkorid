<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard Pengurus
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/css/dashboard/dash_2.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= number_format($member_stats['pending'] ?? 0) ?></h6>
                        <p class="">Calon Anggota</p>
                    </div>
                </div>
                <div class="w-action">
                    <a href="<?= base_url('pengurus/members/pending') ?>" class="btn btn-primary">Verifikasi</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= count($pending_posts ?? []) ?></h6>
                        <p class="">Artikel Pending</p>
                    </div>
                </div>
                <div class="w-action">
                    <a href="<?= base_url('pengurus/blog/pending') ?>" class="btn btn-warning">Review</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= count($open_pengaduan ?? []) ?></h6>
                        <p class="">Pengaduan Baru</p>
                    </div>
                </div>
                <div class="w-action">
                    <a href="<?= base_url('pengurus/pengaduan') ?>" class="btn btn-danger">Tangani</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-table-three">
            <div class="widget-heading">
                <h5 class="">Anggota Menunggu Verifikasi</h5>
                <div class="task-action">
                    <a href="<?= base_url('pengurus/members/pending') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
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
                                    <div class="th-content">Kampus</div>
                                </th>
                                <th>
                                    <div class="th-content">Aksi</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_members)): ?>
                                <?php foreach (array_slice($pending_members, 0, 5) as $member): ?>
                                    <tr>
                                        <td>
                                            <div class="td-content customer-name">
                                                <img src="<?= base_url($member['foto_path'] ?? 'assets/img/90x90.jpg') ?>" alt="avatar">
                                                <span><?= esc($member['nama_lengkap']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="td-content"><?= esc($member['nama_kampus'] ?? 'N/A') ?></div>
                                        </td>
                                        <td>
                                            <div class="td-content"><a href="<?= base_url('pengurus/members/view/' . $member['id']) ?>" class="btn btn-sm btn-primary">Review</a></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="td-content text-center">Tidak ada anggota menunggu verifikasi.</div>
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
                <h5 class="">Aktivitas Terbaru Serikat</h5>
            </div>
            <div class="widget-content">
                <div class="mt-container mx-auto">
                    <div class="timeline-line">
                        <?php if (!empty($recent_activities)): ?>
                            <?php foreach (array_slice($recent_activities, 0, 5) as $activity): ?>
                                <div class="item-timeline timeline-<?= $activity['type'] == 'new_member' ? 'success' : 'primary' ?>">
                                    <div class="t-dot" data-original-title="" title=""></div>
                                    <div class="t-text">
                                        <p><?= esc($activity['message']) ?></p>
                                        <span class="badge badge-info"><?= \CodeIgniter\I18n\Time::parse($activity['date'])->humanize() ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center">Belum ada aktivitas terbaru.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>