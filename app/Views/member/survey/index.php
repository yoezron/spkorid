<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Survei Anggota
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Kustomisasi tambahan untuk tampilan survei */
    .survey-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease-in-out;
    }

    .survey-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .card-footer {
        background-color: #f7f7f7;
    }

    .stat-widget .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-widget i {
        font-size: 3rem;
        opacity: 0.3;
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #888ea8;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Survei Anggota</h1>
            <span>Partisipasi Anda sangat berarti untuk kemajuan organisasi.</span>
        </div>
    </div>
</div>

<!-- Statistik Overview -->
<div class="row">
    <div class="col-xl-4">
        <div class="card widget stat-widget">
            <div class="card-body">
                <div class="widget-info">
                    <h5 class="widget-title">Survei Tersedia</h5>
                    <span class="widget-data"><?= count($available_surveys) ?></span>
                </div>
                <div class="widget-icon">
                    <i class="material-icons-outlined">quiz</i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card widget stat-widget">
            <div class="card-body">
                <div class="widget-info">
                    <h5 class="widget-title">Sudah Diselesaikan</h5>
                    <span class="widget-data"><?= count($completed_surveys) ?></span>
                </div>
                <div class="widget-icon">
                    <i class="material-icons-outlined">done_all</i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card widget stat-widget">
            <div class="card-body">
                <div class="widget-info">
                    <h5 class="widget-title">Akan Datang</h5>
                    <span class="widget-data"><?= count($upcoming_surveys) ?></span>
                </div>
                <div class="widget-icon">
                    <i class="material-icons-outlined">pending</i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Survei dengan Tabs -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="survey-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="available-tab" data-bs-toggle="tab" href="#available" role="tab" aria-controls="available" aria-selected="true">
                    Tersedia
                    <?php if (count($available_surveys) > 0): ?>
                        <span class="badge badge-primary ms-1"><?= count($available_surveys) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">
                    Selesai
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="upcoming-tab" data-bs-toggle="tab" href="#upcoming" role="tab" aria-controls="upcoming" aria-selected="false">
                    Akan Datang
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="survey-tabs-content">
            <!-- Tab Survei Tersedia -->
            <div class="tab-pane fade show active" id="available" role="tabpanel" aria-labelledby="available-tab">
                <?php if (empty($available_surveys)): ?>
                    <div class="empty-state">
                        <i class="material-icons-outlined">inbox</i>
                        <h5>Tidak Ada Survei Tersedia</h5>
                        <p>Saat ini tidak ada survei yang perlu Anda isi. Silakan periksa kembali nanti.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($available_surveys as $survey): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card survey-card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($survey['title']) ?></h5>
                                        <span class="badge badge-primary mb-3">Tersedia</span>
                                        <p class="card-text text-muted small"><?= esc($survey['description']) ?></p>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><i class="material-icons-outlined me-2 small">event_busy</i>Berakhir: <?= date('d M Y', strtotime($survey['end_date'])) ?></li>
                                        <li class="list-group-item"><i class="material-icons-outlined me-2 small">group</i><?= $survey['total_responses'] ?? 0 ?> Responden</li>
                                    </ul>
                                    <div class="card-footer">
                                        <a href="<?= base_url('member/survey/' . $survey['id']) ?>" class="btn btn-primary w-100">
                                            <i class="material-icons-outlined me-1">edit</i> Isi Survei
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab Survei Selesai -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <?php if (empty($completed_surveys)): ?>
                    <div class="empty-state">
                        <i class="material-icons-outlined">check_circle_outline</i>
                        <h5>Belum Ada Survei Selesai</h5>
                        <p>Riwayat survei yang telah Anda selesaikan akan muncul di sini.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($completed_surveys as $survey): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card survey-card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($survey['title']) ?></h5>
                                        <span class="badge badge-success mb-3">Selesai</span>
                                        <p class="card-text text-muted small"><?= esc($survey['description']) ?></p>
                                    </div>
                                    <div class="card-footer d-flex gap-2">
                                        <a href="<?= base_url('member/survey/my-response/' . $survey['id']) ?>" class="btn btn-secondary w-100">Lihat Jawaban</a>
                                        <?php if ($survey['show_results_to_participants'] ?? false): ?>
                                            <a href="<?= base_url('member/survey/results/' . $survey['id']) ?>" class="btn btn-info w-100">Lihat Hasil</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab Survei Akan Datang -->
            <div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                <?php if (empty($upcoming_surveys)): ?>
                    <div class="empty-state">
                        <i class="material-icons-outlined">watch_later</i>
                        <h5>Tidak Ada Survei Mendatang</h5>
                        <p>Nantikan informasi survei berikutnya di sini.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($upcoming_surveys as $survey): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card survey-card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($survey['title']) ?></h5>
                                        <span class="badge badge-warning mb-3">Akan Datang</span>
                                        <p class="card-text text-muted small"><?= esc($survey['description']) ?></p>
                                        <div class="alert alert-warning mt-3">
                                            <i class="material-icons-outlined me-2 small">info</i>
                                            Mulai pada: <?= date('d M Y, H:i', strtotime($survey['start_date'])) ?>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-light w-100" disabled>
                                            <i class="material-icons-outlined me-1">lock</i> Belum Dibuka
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Script tambahan jika diperlukan.
    // Fungsi tab sudah ditangani oleh Bootstrap 5 via atribut data-bs-toggle.
</script>
<?= $this->endSection() ?>