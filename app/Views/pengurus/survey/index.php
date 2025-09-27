<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Survei
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<style>
    .widget-card-one {
        border-radius: 12px;
        border: 1px solid #e0e6ed;
        transition: all 0.3s ease;
    }

    .widget-card-one:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .survey-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background: #1abc9c20;
        color: #1abc9c;
    }

    .status-inactive {
        background: #e7515a20;
        color: #e7515a;
    }

    .status-upcoming {
        background: #e2a03f20;
        color: #e2a03f;
    }

    .status-expired {
        background: #888ea820;
        color: #888ea8;
    }

    .table-actions {
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .btn-view {
        background: #3b82f6;
        color: white;
    }

    .btn-edit {
        background: #f59e0b;
        color: white;
    }

    .btn-toggle {
        background: #8b5cf6;
        color: white;
    }

    .btn-clone {
        background: #10b981;
        color: white;
    }

    .btn-export {
        background: #6366f1;
        color: white;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .survey-meta {
        font-size: 13px;
        color: #888ea8;
        margin-top: 8px;
    }

    .survey-meta i {
        width: 12px;
        height: 12px;
        margin-right: 4px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <i data-feather="bar-chart" class="text-primary" style="width: 50px; height: 50px;"></i>
                            </div>
                            <div class="media-body">
                                <h6>Total Survei</h6>
                                <p class="meta-date-time"><?= number_format($summary['total_surveys'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <i data-feather="play-circle" class="text-success" style="width: 50px; height: 50px;"></i>
                            </div>
                            <div class="media-body">
                                <h6>Survei Aktif</h6>
                                <p class="meta-date-time"><?= number_format($summary['active_surveys'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <i data-feather="clock" class="text-warning" style="width: 50px; height: 50px;"></i>
                            </div>
                            <div class="media-body">
                                <h6>Akan Datang</h6>
                                <p class="meta-date-time"><?= number_format($summary['upcoming_surveys'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <i data-feather="users" class="text-info" style="width: 50px; height: 50px;"></i>
                            </div>
                            <div class="media-body">
                                <h6>Total Responden</h6>
                                <p class="meta-date-time"><?= number_format($summary['total_responses'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="widget-content widget-content-area br-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Daftar Survei</h4>
                    <p class="text-muted mt-1">Kelola semua survei dalam sistem</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info btn-sm" onclick="window.location.reload()">
                        <i data-feather="refresh-cw"></i> Refresh
                    </button>
                    <a href="<?= base_url('admin/surveys/create') ?>" class="btn btn-primary">
                        <i data-feather="plus"></i> Buat Survei Baru
                    </a>
                </div>
            </div>

            <!-- Survey Table -->
            <div class="table-responsive">
                <table id="survey-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Survei</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Responden</th>
                            <th>Pembuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($surveys as $survey): ?>
                            <?php
                            $now = date('Y-m-d H:i:s');
                            $status = 'inactive';
                            $statusText = 'Nonaktif';

                            if ($survey['is_active']) {
                                if ($survey['end_date'] < $now) {
                                    $status = 'expired';
                                    $statusText = 'Selesai';
                                } elseif ($survey['start_date'] > $now) {
                                    $status = 'upcoming';
                                    $statusText = 'Akan Datang';
                                } else {
                                    $status = 'active';
                                    $statusText = 'Aktif';
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong class="d-block"><?= esc($survey['title']) ?></strong>
                                        <small class="text-muted"><?= character_limiter(esc($survey['description']), 60) ?></small>
                                        <div class="survey-meta">
                                            <i data-feather="help-circle"></i> <?= $survey['question_count'] ?? 0 ?> pertanyaan
                                            <?php if ($survey['is_anonymous']): ?>
                                                <span class="ml-3"><i data-feather="user-x"></i> Anonim</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <small><strong>Mulai:</strong> <?= date('d M Y', strtotime($survey['start_date'])) ?></small><br>
                                        <small><strong>Selesai:</strong> <?= date('d M Y', strtotime($survey['end_date'])) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="survey-status status-<?= $status ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <strong class="text-primary"><?= $survey['response_count'] ?? 0 ?></strong>
                                    <small class="text-muted d-block">responden</small>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= esc($survey['creator_name'] ?? 'Unknown') ?></strong>
                                        <small class="text-muted d-block"><?= date('d M Y', strtotime($survey['created_at'])) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button class="action-btn btn-view" onclick="window.open('<?= base_url('admin/surveys/results/' . $survey['id']) ?>', '_blank')" title="Lihat Hasil">
                                            <i data-feather="bar-chart" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <button class="action-btn btn-edit" onclick="window.location.href='<?= base_url('admin/surveys/edit/' . $survey['id']) ?>'" title="Edit">
                                            <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <button class="action-btn btn-toggle" onclick="toggleStatus(<?= $survey['id'] ?>)" title="<?= $survey['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i data-feather="<?= $survey['is_active'] ? 'toggle-right' : 'toggle-left' ?>" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <button class="action-btn btn-clone" onclick="cloneSurvey(<?= $survey['id'] ?>)" title="Duplikat">
                                            <i data-feather="copy" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <button class="action-btn btn-export" onclick="window.open('<?= base_url('admin/surveys/export/' . $survey['id']) ?>', '_blank')" title="Export Excel">
                                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <button class="action-btn btn-delete" onclick="deleteSurvey(<?= $survey['id'] ?>)" title="Hapus">
                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script src="<?= base_url('plugins/sweetalerts/sweetalert2.min.js') ?>"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#survey-table').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<i data-feather="arrow-left"></i>',
                    "sNext": '<i data-feather="arrow-right"></i>'
                },
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ survei",
                "sSearch": '<i data-feather="search"></i>',
                "sSearchPlaceholder": "Cari survei...",
                "sLengthMenu": "Tampilkan: _MENU_",
                "sEmptyTable": "Belum ada survei",
                "sZeroRecords": "Tidak ada survei yang cocok"
            },
            "stripeClasses": [],
            "lengthMenu": [10, 25, 50],
            "pageLength": 10,
            "order": [
                [1, "desc"]
            ],
            drawCallback: function() {
                feather.replace();
            }
        });
    });

    // Toggle Status Function
    function toggleStatus(surveyId) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengubah status survei ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#5c1ac3',
            cancelButtonColor: '#e7515a'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('admin/surveys/toggle-status/') ?>' + surveyId, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Terjadi kesalahan saat mengubah status', 'error');
                    });
            }
        });
    }

    // Delete Survey Function
    function deleteSurvey(surveyId) {
        Swal.fire({
            title: 'Anda Yakin?',
            text: "Survei ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e7515a',
            cancelButtonColor: '#888ea8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('admin/surveys/delete/') ?>' + surveyId, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus survei', 'error');
                    });
            }
        });
    }

    // Clone Survey Function
    function cloneSurvey(surveyId) {
        Swal.fire({
            title: 'Duplikat Survei',
            text: 'Apakah Anda yakin ingin menduplikat survei ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Duplikat',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#5c1ac3'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menduplikat survei',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                window.location.href = '<?= base_url('admin/surveys/clone/') ?>' + surveyId;
            }
        });
    }

    // Initialize feather icons
    feather.replace();
</script>
<?= $this->endSection() ?>