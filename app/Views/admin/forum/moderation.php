<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Moderasi Forum
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/css/dashboard/dash_2.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= number_format($statistics['total_threads'] ?? 0) ?></h6>
                        <p class="">Total Thread</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= number_format($statistics['total_replies'] ?? 0) ?></h6>
                        <p class="">Total Balasan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= number_format($statistics['today_threads'] ?? 0) ?></h6>
                        <p class="">Thread Hari Ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-content">
                    <div class="w-info">
                        <h6 class="value"><?= count($reported_content ?? []) ?></h6>
                        <p class="">Konten Dilaporkan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-table-two">
            <div class="widget-heading">
                <h5 class="">Thread Terbaru</h5>
            </div>
            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <div class="th-content">Judul</div>
                                </th>
                                <th>
                                    <div class="th-content">Kategori</div>
                                </th>
                                <th>
                                    <div class="th-content">Balasan</div>
                                </th>
                                <th>
                                    <div class="th-content th-heading">Status</div>
                                </th>
                                <th>
                                    <div class="th-content">Aksi</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_threads)): ?>
                                <?php foreach (array_slice($recent_threads, 0, 7) as $thread): ?>
                                    <tr>
                                        <td>
                                            <div class="td-content"><span class="pricing"><?= esc(character_limiter($thread['title'], 40)) ?></span></div>
                                        </td>
                                        <td>
                                            <div class="td-content"><span class="badge badge-primary"><?= esc($thread['category_name']) ?></span></div>
                                        </td>
                                        <td>
                                            <div class="td-content"><?= esc($thread['reply_count']) ?></div>
                                        </td>
                                        <td>
                                            <div class="td-content">
                                                <?php if ($thread['is_locked']): ?>
                                                    <span class="badge badge-danger">Terkunci</span>
                                                <?php elseif ($thread['is_pinned']): ?>
                                                    <span class="badge badge-warning">Pinned</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="td-content">
                                                <a href="<?= base_url('forum/thread/' . $thread['id']) ?>" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-activity-four">
            <div class="widget-heading">
                <h5 class="">Konten yang Dilaporkan</h5>
            </div>
            <div class="widget-content">
                <div class="mt-container mx-auto">
                    <div class="timeline-line">
                        <?php if (!empty($reported_content)): ?>
                            <?php foreach ($reported_content as $report): ?>
                                <div class="item-timeline timeline-primary">
                                    <div class="t-dot" data-original-title="" title=""></div>
                                    <div class="t-text">
                                        <p><span>Laporan baru</span> dari <?= esc($report['reporter_name']) ?></p>
                                        <span class="badge badge-danger"><?= esc($report['reason']) ?></span>
                                        <p class="t-time"><?= date('d M Y', strtotime($report['created_at'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center">Tidak ada laporan konten saat ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>