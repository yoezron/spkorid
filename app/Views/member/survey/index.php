<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Survei Anggota
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .survey-card {
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        background: #fff;
        position: relative;
        overflow: hidden;
    }

    .survey-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .survey-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #5c1ac3;
    }

    .survey-card.completed::before {
        background: #1abc9c;
    }

    .survey-card.upcoming::before {
        background: #e2a03f;
    }

    .survey-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-completed {
        background: #1abc9c20;
        color: #1abc9c;
    }

    .badge-available {
        background: #5c1ac320;
        color: #5c1ac3;
    }

    .badge-upcoming {
        background: #e2a03f20;
        color: #e2a03f;
    }

    .survey-meta {
        display: flex;
        gap: 20px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e0e6ed;
        font-size: 14px;
        color: #888ea8;
    }

    .survey-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .survey-meta-item i {
        width: 16px;
        height: 16px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state svg {
        width: 100px;
        height: 100px;
        margin-bottom: 20px;
        color: #e0e6ed;
    }

    .progress-bar-container {
        margin-top: 15px;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 12px;
        color: #888ea8;
    }

    .countdown {
        background: #fff3cd;
        color: #856404;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        margin-top: 10px;
        display: inline-block;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <h5>Survei Tersedia</h5>
                        <div class="task-action">
                            <div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    <i data-feather="more-horizontal"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="location.reload()">
                                        Refresh
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content">
                        <div class="w-chart">
                            <div class="w-chart-section">
                                <div class="w-detail">
                                    <p class="w-title">Total Survei Aktif</p>
                                    <p class="w-stats"><?= count($available_surveys) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <h5>Sudah Diisi</h5>
                    </div>
                    <div class="widget-content">
                        <div class="w-chart">
                            <div class="w-chart-section">
                                <div class="w-detail">
                                    <p class="w-title">Survei Selesai</p>
                                    <p class="w-stats text-success"><?= count($completed_surveys) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <h5>Akan Datang</h5>
                    </div>
                    <div class="widget-content">
                        <div class="w-chart">
                            <div class="w-chart-section">
                                <div class="w-detail">
                                    <p class="w-title">Belum Dibuka</p>
                                    <p class="w-stats text-warning"><?= count($upcoming_surveys) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Survey Tabs -->
        <div class="widget-content widget-content-area br-6">
            <div class="widget-header mb-4">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Daftar Survei</h4>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs mb-3" id="surveyTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#available" role="tab">
                        <i data-feather="clipboard"></i> Tersedia
                        <?php if (count($available_surveys) > 0): ?>
                            <span class="badge badge-primary"><?= count($available_surveys) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#completed" role="tab">
                        <i data-feather="check-circle"></i> Sudah Diisi
                        <?php if (count($completed_surveys) > 0): ?>
                            <span class="badge badge-success"><?= count($completed_surveys) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#upcoming" role="tab">
                        <i data-feather="clock"></i> Akan Datang
                        <?php if (count($upcoming_surveys) > 0): ?>
                            <span class="badge badge-warning"><?= count($upcoming_surveys) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="surveyTabsContent">
                <!-- Available Surveys -->
                <div class="tab-pane fade show active" id="available" role="tabpanel">
                    <?php if (empty($available_surveys)): ?>
                        <div class="empty-state">
                            <i data-feather="inbox"></i>
                            <h5>Tidak Ada Survei Tersedia</h5>
                            <p class="text-muted">Saat ini tidak ada survei yang perlu Anda isi.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($available_surveys as $survey): ?>
                                <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                                    <div class="survey-card">
                                        <span class="survey-badge badge-available">Tersedia</span>

                                        <h5 class="mb-2"><?= esc($survey['title']) ?></h5>
                                        <p class="text-muted mb-3"><?= esc($survey['description']) ?></p>

                                        <?php
                                        $endDate = new DateTime($survey['end_date']);
                                        $now = new DateTime();
                                        $diff = $now->diff($endDate);
                                        $daysLeft = $diff->days;
                                        ?>

                                        <?php if ($daysLeft <= 3): ?>
                                            <div class="countdown">
                                                <i data-feather="alert-triangle" style="width: 14px; height: 14px;"></i>
                                                Tersisa <?= $daysLeft ?> hari lagi
                                            </div>
                                        <?php endif; ?>

                                        <div class="survey-meta">
                                            <div class="survey-meta-item">
                                                <i data-feather="calendar"></i>
                                                <span>Berakhir: <?= date('d M Y', strtotime($survey['end_date'])) ?></span>
                                            </div>
                                            <div class="survey-meta-item">
                                                <i data-feather="users"></i>
                                                <span><?= $survey['total_responses'] ?? 0 ?> responden</span>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <a href="<?= base_url('member/surveys/take/' . $survey['id']) ?>"
                                                class="btn btn-primary btn-block">
                                                <i data-feather="edit-3"></i> Isi Survei
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Completed Surveys -->
                <div class="tab-pane fade" id="completed" role="tabpanel">
                    <?php if (empty($completed_surveys)): ?>
                        <div class="empty-state">
                            <i data-feather="check-circle"></i>
                            <h5>Belum Ada Survei yang Diselesaikan</h5>
                            <p class="text-muted">Anda belum menyelesaikan survei apapun.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($completed_surveys as $survey): ?>
                                <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                                    <div class="survey-card completed">
                                        <span class="survey-badge badge-completed">Selesai</span>

                                        <h5 class="mb-2"><?= esc($survey['title']) ?></h5>
                                        <p class="text-muted mb-3"><?= esc($survey['description']) ?></p>

                                        <div class="progress-bar-container">
                                            <div class="progress-label">
                                                <span>Partisipasi</span>
                                                <span><?= $survey['total_responses'] ?? 0 ?> responden</span>
                                            </div>
                                            <div class="progress br-30">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: 100%" aria-valuenow="100"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="survey-meta">
                                            <div class="survey-meta-item">
                                                <i data-feather="check"></i>
                                                <span>Sudah diisi</span>
                                            </div>
                                            <div class="survey-meta-item">
                                                <i data-feather="calendar"></i>
                                                <span>Periode: <?= date('d M', strtotime($survey['start_date'])) ?> - <?= date('d M Y', strtotime($survey['end_date'])) ?></span>
                                            </div>
                                        </div>

                                        <div class="mt-3 d-flex gap-2">
                                            <a href="<?= base_url('member/surveys/my-response/' . $survey['id']) ?>"
                                                class="btn btn-info flex-fill">
                                                <i data-feather="eye"></i> Lihat Jawaban
                                            </a>
                                            <?php if ($survey['show_results_to_participants']): ?>
                                                <a href="<?= base_url('member/surveys/result/' . $survey['id']) ?>"
                                                    class="btn btn-secondary flex-fill">
                                                    <i data-feather="bar-chart-2"></i> Lihat Hasil
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Upcoming Surveys -->
                <div class="tab-pane fade" id="upcoming" role="tabpanel">
                    <?php if (empty($upcoming_surveys)): ?>
                        <div class="empty-state">
                            <i data-feather="clock"></i>
                            <h5>Tidak Ada Survei Mendatang</h5>
                            <p class="text-muted">Saat ini tidak ada survei yang akan datang.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($upcoming_surveys as $survey): ?>
                                <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                                    <div class="survey-card upcoming">
                                        <span class="survey-badge badge-upcoming">Akan Datang</span>

                                        <h5 class="mb-2"><?= esc($survey['title']) ?></h5>
                                        <p class="text-muted mb-3"><?= esc($survey['description']) ?></p>

                                        <?php
                                        $startDate = new DateTime($survey['start_date']);
                                        $now = new DateTime();
                                        $diff = $startDate->diff($now);
                                        ?>

                                        <div class="alert alert-warning mb-3" role="alert">
                                            <i data-feather="info" style="width: 18px; height: 18px;"></i>
                                            Survei akan dibuka dalam <strong><?= $diff->days ?> hari</strong>
                                        </div>

                                        <div class="survey-meta">
                                            <div class="survey-meta-item">
                                                <i data-feather="calendar"></i>
                                                <span>Mulai: <?= date('d M Y H:i', strtotime($survey['start_date'])) ?></span>
                                            </div>
                                            <div class="survey-meta-item">
                                                <i data-feather="clock"></i>
                                                <span>Durasi: <?= $diff->days ?> hari</span>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button class="btn btn-secondary btn-block" disabled>
                                                <i data-feather="lock"></i> Belum Dibuka
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    feather.replace();

    // Auto refresh for countdown
    setInterval(function() {
        document.querySelectorAll('.countdown').forEach(function(el) {
            // Refresh countdown display
            // Implementation depends on your needs
        });
    }, 60000); // Update every minute
</script>
<?= $this->endSection() ?>