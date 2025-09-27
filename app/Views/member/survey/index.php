<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Survei Anggota
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .dashboard-header h3 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .dashboard-meta {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .meta-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-widget {
        background: white;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #e0e6ed;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-widget:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .stat-widget::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .stat-widget.available::before {
        background: #3498db;
    }

    .stat-widget.completed::before {
        background: #1abc9c;
    }

    .stat-widget.upcoming::before {
        background: #f39c12;
    }

    .widget-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .widget-info h6 {
        color: #888ea8;
        font-size: 14px;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .widget-value {
        font-size: 32px;
        font-weight: bold;
        color: #2c3e50;
    }

    .widget-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .widget-icon.available {
        background: #3498db20;
        color: #3498db;
    }

    .widget-icon.completed {
        background: #1abc9c20;
        color: #1abc9c;
    }

    .widget-icon.upcoming {
        background: #f39c1220;
        color: #f39c12;
    }

    .surveys-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        border: 1px solid #e0e6ed;
    }

    .surveys-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f8f9fa;
    }

    .surveys-title h4 {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .surveys-subtitle {
        color: #888ea8;
        font-size: 14px;
    }

    .survey-tabs {
        display: flex;
        gap: 5px;
        margin-bottom: 25px;
        border-bottom: 1px solid #e0e6ed;
    }

    .survey-tab {
        padding: 12px 20px;
        border: none;
        background: transparent;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .survey-tab:hover {
        background: #f8f9fa;
    }

    .survey-tab.active {
        border-bottom-color: #5c1ac3;
        background: #5c1ac320;
        color: #5c1ac3;
    }

    .tab-badge {
        background: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .survey-tab.active .tab-badge {
        background: #5c1ac3;
        color: white;
    }

    .survey-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
    }

    .survey-card {
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 25px;
        background: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .survey-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .survey-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .survey-card.available::before {
        background: #3498db;
    }

    .survey-card.completed::before {
        background: #1abc9c;
    }

    .survey-card.upcoming::before {
        background: #f39c12;
    }

    .survey-status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-available {
        background: #3498db20;
        color: #3498db;
        border: 1px solid #3498db40;
    }

    .badge-completed {
        background: #1abc9c20;
        color: #1abc9c;
        border: 1px solid #1abc9c40;
    }

    .badge-upcoming {
        background: #f39c1220;
        color: #f39c12;
        border: 1px solid #f39c1240;
    }

    .survey-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        line-height: 1.4;
        color: #2c3e50;
    }

    .survey-description {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
        font-size: 14px;
    }

    .survey-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #888ea8;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .meta-item i {
        width: 14px;
        height: 14px;
    }

    .survey-progress {
        margin-bottom: 20px;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .progress-bar-custom {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 1s ease;
    }

    .progress-fill.available {
        background: linear-gradient(90deg, #3498db, #5dade2);
    }

    .progress-fill.completed {
        background: linear-gradient(90deg, #1abc9c, #58d68d);
    }

    .progress-fill.upcoming {
        background: linear-gradient(90deg, #f39c12, #f8c471);
    }

    .countdown-alert {
        background: #fff3cd;
        color: #856404;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-left: 4px solid #ffc107;
    }

    .survey-actions {
        display: flex;
        gap: 10px;
    }

    .action-btn {
        flex: 1;
        padding: 12px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        text-decoration: none;
        color: inherit;
    }

    .btn-primary-action {
        background: #5c1ac3;
        color: white;
    }

    .btn-secondary-action {
        background: #6c757d;
        color: white;
    }

    .btn-info-action {
        background: #17a2b8;
        color: white;
    }

    .btn-disabled {
        background: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #888ea8;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h5 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .empty-state p {
        margin-bottom: 0;
        line-height: 1.6;
    }

    .tab-content {
        min-height: 400px;
    }

    .tab-pane {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .tab-pane.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .survey-card {
        animation: slideInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    .survey-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .survey-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .survey-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .survey-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    @keyframes slideInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notification-dot {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 12px;
        height: 12px;
        background: #e74c3c;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.7;
            transform: scale(1.1);
        }
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 30px 20px;
        }

        .dashboard-meta {
            flex-direction: column;
            gap: 10px;
        }

        .stats-overview {
            grid-template-columns: 1fr;
        }

        .survey-grid {
            grid-template-columns: 1fr;
        }

        .survey-actions {
            flex-direction: column;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">

        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h3>Survei Anggota</h3>
                    <p class="mb-0 opacity-90">Partisipasi Anda sangat berarti untuk kemajuan organisasi</p>

                    <div class="dashboard-meta">
                        <div class="meta-badge">
                            <i data-feather="calendar"></i>
                            <span><?= date('d M Y') ?></span>
                        </div>
                        <div class="meta-badge">
                            <i data-feather="user"></i>
                            <span><?= session()->get('nama_lengkap') ?></span>
                        </div>
                        <div class="meta-badge">
                            <i data-feather="award"></i>
                            <span>Anggota Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-light btn-sm" onclick="refreshSurveys()">
                        <i data-feather="refresh-cw"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-overview">
            <div class="stat-widget available">
                <div class="widget-content">
                    <div class="widget-info">
                        <h6>Survei Tersedia</h6>
                        <div class="widget-value"><?= count($available_surveys) ?></div>
                        <small class="text-muted">Menunggu partisipasi Anda</small>
                    </div>
                    <div class="widget-icon available">
                        <i data-feather="clipboard"></i>
                    </div>
                </div>
            </div>

            <div class="stat-widget completed">
                <div class="widget-content">
                    <div class="widget-info">
                        <h6>Sudah Diselesaikan</h6>
                        <div class="widget-value"><?= count($completed_surveys) ?></div>
                        <small class="text-muted">Terima kasih atas partisipasinya</small>
                    </div>
                    <div class="widget-icon completed">
                        <i data-feather="check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="stat-widget upcoming">
                <div class="widget-content">
                    <div class="widget-info">
                        <h6>Akan Datang</h6>
                        <div class="widget-value"><?= count($upcoming_surveys) ?></div>
                        <small class="text-muted">Segera tersedia</small>
                    </div>
                    <div class="widget-icon upcoming">
                        <i data-feather="clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surveys Container -->
        <div class="surveys-container">
            <div class="surveys-header">
                <div class="surveys-title">
                    <h4>Daftar Survei</h4>
                    <p class="surveys-subtitle">Pilih survei yang ingin Anda ikuti</p>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="form-group mb-0">
                        <select class="form-control form-control-sm" id="sortSurveys">
                            <option value="newest">Terbaru</option>
                            <option value="deadline">Deadline Terdekat</option>
                            <option value="alphabetical">Alfabetis</option>
                        </select>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" onclick="toggleGridView()">
                        <i data-feather="grid" id="viewToggleIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Survey Tabs -->
            <div class="survey-tabs">
                <button class="survey-tab active" data-tab="available">
                    <i data-feather="clipboard"></i>
                    <span>Tersedia</span>
                    <?php if (count($available_surveys) > 0): ?>
                        <span class="tab-badge"><?= count($available_surveys) ?></span>
                    <?php endif; ?>
                </button>
                <button class="survey-tab" data-tab="completed">
                    <i data-feather="check-circle"></i>
                    <span>Sudah Diisi</span>
                    <?php if (count($completed_surveys) > 0): ?>
                        <span class="tab-badge"><?= count($completed_surveys) ?></span>
                    <?php endif; ?>
                </button>
                <button class="survey-tab" data-tab="upcoming">
                    <i data-feather="clock"></i>
                    <span>Akan Datang</span>
                    <?php if (count($upcoming_surveys) > 0): ?>
                        <span class="tab-badge"><?= count($upcoming_surveys) ?></span>
                    <?php endif; ?>
                </button>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Available Surveys -->
                <div class="tab-pane active" id="available-tab">
                    <?php if (empty($available_surveys)): ?>
                        <div class="empty-state">
                            <i data-feather="inbox"></i>
                            <h5>Tidak Ada Survei Tersedia</h5>
                            <p>Saat ini tidak ada survei yang perlu Anda isi.<br>Silakan periksa kembali nanti.</p>
                        </div>
                    <?php else: ?>
                        <div class="survey-grid" id="availableSurveys">
                            <?php foreach ($available_surveys as $survey): ?>
                                <div class="survey-card available" data-survey-id="<?= $survey['id'] ?>">
                                    <span class="survey-status-badge badge-available">Tersedia</span>

                                    <?php
                                    $endDate = new DateTime($survey['end_date']);
                                    $now = new DateTime();
                                    $diff = $now->diff($endDate);
                                    $daysLeft = $diff->days;
                                    $isUrgent = $daysLeft <= 3;
                                    ?>

                                    <?php if ($isUrgent): ?>
                                        <div class="notification-dot"></div>
                                    <?php endif; ?>

                                    <h5 class="survey-title"><?= esc($survey['title']) ?></h5>
                                    <p class="survey-description"><?= esc($survey['description']) ?></p>

                                    <?php if ($isUrgent): ?>
                                        <div class="countdown-alert">
                                            <i data-feather="alert-triangle"></i>
                                            <span><strong>Segera berakhir!</strong> Tersisa <?= $daysLeft ?> hari lagi</span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="survey-meta">
                                        <div class="meta-item">
                                            <i data-feather="calendar"></i>
                                            <span>Berakhir: <?= date('d M Y', strtotime($survey['end_date'])) ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <i data-feather="users"></i>
                                            <span><?= $survey['total_responses'] ?? 0 ?> responden</span>
                                        </div>
                                        <div class="meta-item">
                                            <i data-feather="clock"></i>
                                            <span>~<?= $survey['estimated_time'] ?? 5 ?> menit</span>
                                        </div>
                                    </div>

                                    <div class="survey-progress">
                                        <div class="progress-label">
                                            <span>Partisipasi</span>
                                            <span><?= $survey['participation_rate'] ?? 0 ?>%</span>
                                        </div>
                                        <div class="progress-bar-custom">
                                            <div class="progress-fill available" style="width: <?= $survey['participation_rate'] ?? 0 ?>%"></div>
                                        </div>
                                    </div>

                                    <div class="survey-actions">
                                        <a href="<?= base_url('member/surveys/take/' . $survey['id']) ?>"
                                            class="action-btn btn-primary-action">
                                            <i data-feather="edit-3"></i>
                                            Isi Survei
                                        </a>
                                        <button class="action-btn btn-secondary-action" onclick="previewSurvey(<?= $survey['id'] ?>)">
                                            <i data-feather="eye"></i>
                                            Preview
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Completed Surveys -->
                <div class="tab-pane" id="completed-tab">
                    <?php if (empty($completed_surveys)): ?>
                        <div class="empty-state">
                            <i data-feather="check-circle"></i>
                            <h5>Belum Ada Survei yang Diselesaikan</h5>
                            <p>Anda belum menyelesaikan survei apapun.<br>Mulai dengan mengisi survei yang tersedia.</p>
                        </div>
                    <?php else: ?>
                        <div class="survey-grid" id="completedSurveys">
                            <?php foreach ($completed_surveys as $survey): ?>
                                <div class="survey-card completed" data-survey-id="<?= $survey['id'] ?>">
                                    <span class="survey-status-badge badge-completed">Selesai</span>

                                    <h5 class="survey-title"><?= esc($survey['title']) ?></h5>
                                    <p class="survey-description"><?= esc($survey['description']) ?></p>

                                    <div class="survey-meta">
                                        <div class="meta-item">
                                            <i data-feather="check"></i>
                                            <span>Diselesaikan</span>
                                        </div>
                                        <div class="meta-item">
                                            <i data-feather="calendar"></i>
                                            <span>Periode: <?= date('d M', strtotime($survey['start_date'])) ?> - <?= date('d M Y', strtotime($survey['end_date'])) ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <i data-feather="users"></i>
                                            <span><?= $survey['total_responses'] ?? 0 ?> total responden</span>
                                        </div>
                                    </div>

                                    <div class="survey-progress">
                                        <div class="progress-label">
                                            <span>Kontribusi Anda</span>
                                            <span><i data-feather="star"></i> Terima kasih!</span>
                                        </div>
                                        <div class="progress-bar-custom">
                                            <div class="progress-fill completed" style="width: 100%"></div>
                                        </div>
                                    </div>

                                    <div class="survey-actions">
                                        <a href="<?= base_url('member/surveys/my-response/' . $survey['id']) ?>"
                                            class="action-btn btn-info-action">
                                            <i data-feather="eye"></i>
                                            Lihat Jawaban
                                        </a>
                                        <?php if ($survey['show_results_to_participants'] ?? false): ?>
                                            <a href="<?= base_url('member/surveys/result/' . $survey['id']) ?>"
                                                class="action-btn btn-secondary-action">
                                                <i data-feather="bar-chart-2"></i>
                                                Lihat Hasil
                                            </a>
                                        <?php else: ?>
                                            <button class="action-btn btn-disabled" disabled>
                                                <i data-feather="lock"></i>
                                                Hasil Ditutup
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Upcoming Surveys -->
                <div class="tab-pane" id="upcoming-tab">
                    <?php if (empty($upcoming_surveys)): ?>
                        <div class="empty-state">
                            <i data-feather="clock"></i>
                            <h5>Tidak Ada Survei Mendatang</h5>
                            <p>Saat ini tidak ada survei yang akan datang.<br>Pantau terus untuk survei terbaru.</p>
                        </div>
                    <?php else: ?>
                        <div class="survey-grid" id="upcomingSurveys">
                            <?php foreach ($upcoming_surveys as $survey): ?>
                                <div class="survey-card upcoming" data-survey-id="<?= $survey['id'] ?>">
                                    <span class="survey-status-badge badge-upcoming">Akan Datang</span>

                                    <h5 class="survey-title"><?= esc($survey['title']) ?></h5>
                                    <p class="survey-description"><?= esc($survey['description']) ?></p>

                                    <?php
                                    $startDate = new DateTime($survey['start_date']);
                                    $now = new DateTime();
                                    $diff = $startDate->diff($now);
                                    $daysUntil = $diff->days;
                                    ?>

                                    <div class="countdown-alert">
                                        <i data-feather="info"></i>
                                        <span>Survei akan dibuka dalam <strong><?= $daysUntil ?> hari</strong></span>
                                    </div>

                                    <div class="survey-meta">
                                        <div class="meta-item">
                                            <i data-feather="calendar"></i>
                                            <span>Mulai: <?= date('d M Y H:i', strtotime($survey['start_date'])) ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <i data-feather="clock"></i>
                                            <span>Durasi: <?= $diff->days ?> hari</span>
                                        </div>
                                        <div class="meta-item">
                                            <i data-feather="help-circle"></i>
                                            <span><?= $survey['question_count'] ?? 0 ?> pertanyaan</span>
                                        </div>
                                    </div>

                                    <div class="survey-progress">
                                        <div class="progress-label">
                                            <span>Status</span>
                                            <span>Menunggu dibuka</span>
                                        </div>
                                        <div class="progress-bar-custom">
                                            <div class="progress-fill upcoming" style="width: 0%"></div>
                                        </div>
                                    </div>

                                    <div class="survey-actions">
                                        <button class="action-btn btn-disabled" disabled>
                                            <i data-feather="lock"></i>
                                            Belum Dibuka
                                        </button>
                                        <button class="action-btn btn-secondary-action" onclick="setReminder(<?= $survey['id'] ?>)">
                                            <i data-feather="bell"></i>
                                            Ingatkan Saya
                                        </button>
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

<!-- Survey Preview Modal -->
<div class="modal fade" id="surveyPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i data-feather="eye"></i> Preview Survei
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="surveyPreviewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="startSurveyFromPreview">
                    <i data-feather="edit-3"></i> Mulai Isi Survei
                </button>
            </div>
        </div>
    </div>
</div>