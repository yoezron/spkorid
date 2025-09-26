<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Survei
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/forms/switches.css') ?>">
<style>
    .survey-card {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .survey-card:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .survey-stats {
        display: flex;
        justify-content: space-around;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e0e6ed;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #5c1ac3;
    }

    .stat-label {
        font-size: 12px;
        color: #888ea8;
        text-transform: uppercase;
    }

    .survey-status {
        padding: 4px 12px;
        border-radius: 4px;
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <img src="<?= base_url('assets/img/survey-icon.svg') ?>" alt="survey">
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
                                <img src="<?= base_url('assets/img/active-icon.svg') ?>" alt="active">
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
                                <img src="<?= base_url('assets/img/upcoming-icon.svg') ?>" alt="upcoming">
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
                                <img src="<?= base_url('assets/img/response-icon.svg') ?>" alt="responses">
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

        <div class="widget-content widget-content-area br-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Daftar Survei</h4>
                <div>
                    <button type="button" class="btn btn-info btn-sm mr-2" onclick="window.location.reload()">
                        <i data-feather="refresh-cw"></i> Refresh
                    </button>
                    <a href="<?= base_url('admin/surveys/create') ?>" class="btn btn-primary">
                        <i data-feather="plus"></i> Buat Survei Baru
                    </a>
                </div>
            </div>

            <ul class="nav nav-tabs mb-3" id="surveyTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                        Semua <span class="badge badge-primary ml-2"><?= count($surveys) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="active-tab" data-toggle="tab" href="#active" role="tab">
                        Aktif <span class="badge badge-success ml-2">
                            <?= count(array_filter($surveys, function ($s) {
                                $now = date('Y-m-d H:i:s');
                                return $s['is_active'] && $s['start_date'] <= $now && $s['end_date'] >= $now;
                            })) ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="upcoming-tab" data-toggle="tab" href="#upcoming" role="tab">
                        Akan Datang <span class="badge badge-warning ml-2">
                            <?= count(array_filter($surveys, function ($s) {
                                return $s['is_active'] && $s['start_date'] > date('Y-m-d H:i:s');
                            })) ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="expired-tab" data-toggle="tab" href="#expired" role="tab">
                        Selesai <span class="badge badge-secondary ml-2">
                            <?= count(array_filter($surveys, function ($s) {
                                return $s['end_date'] < date('Y-m-d H:i:s');
                            })) ?>
                        </span>
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="surveyTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <div class="table-responsive">
                        <table id="survey-table" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Responden</th>
                                    <th>Dibuat Oleh</th>
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
                                            <div class="d-flex flex-column">
                                                <strong><?= esc($survey['title']) ?></strong>
                                                <small class="text-muted"><?= character_limiter(esc($survey['description']), 50) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <small><?= date('d M Y', strtotime($survey['start_date'])) ?></small>
                                                <small><?= date('d M Y', strtotime($survey['end_date'])) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="survey-status status-<?= $status ?>"><?= $statusText ?></span>
                                        </td>
                                        <td>
                                            <strong><?= $survey['response_count'] ?? 0 ?></strong> responden
                                        </td>
                                        <td>
                                            <?= esc($survey['creator_name'] ?? 'Unknown') ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown custom-dropdown">
                                                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                                    <i data-feather="more-horizontal"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="<?= base_url('admin/surveys/results/' . $survey['id']) ?>">
                                                        <i data-feather="bar-chart"></i> Lihat Hasil
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url('admin/surveys/edit/' . $survey['id']) ?>">
                                                        <i data-feather="edit"></i> Edit
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:void(0);" onclick="toggleStatus(<?= $survey['id'] ?>)">
                                                        <i data-feather="<?= $survey['is_active'] ? 'toggle-right' : 'toggle-left' ?>"></i>
                                                        <?= $survey['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url('admin/surveys/clone/' . $survey['id']) ?>">
                                                        <i data-feather="copy"></i> Duplikat
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url('admin/surveys/export/' . $survey['id']) ?>">
                                                        <i data-feather="download"></i> Export Excel
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteSurvey(<?= $survey['id'] ?>)">
                                                        <i data-feather="trash-2"></i> Hapus
                                                    </a>
                                                </div>
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
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script src="<?= base_url('plugins/sweetalerts/sweetalert2.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#survey-table').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<i data-feather="arrow-left"></i>',
                    "sNext": '<i data-feather="arrow-right"></i>'
                },
                "sInfo": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "sSearch": '<i data-feather="search"></i>',
                "sSearchPlaceholder": "Cari...",
                "sLengthMenu": "Hasil :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [10, 20, 50],
            "pageLength": 10,
            drawCallback: function() {
                feather.replace();
            }
        });
    });

    function toggleStatus(surveyId) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengubah status survei ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('admin/surveys/toggle-status/') ?>' + surveyId, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    });
            }
        });
    }
</script>
<?= $this->endSection() ?>