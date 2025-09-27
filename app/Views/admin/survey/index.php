<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Survei
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="page-header">
        <nav class="navbar navbar-expand">
            <div class="container-fluid">
                <div class="navbar-collapse" id="navbarSupportedContent">
                    <div class="page-title">
                        <h4>Manajemen Survei</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Survei</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div class="main-wrapper">

        <!-- =================================================================
        PENYEMPURNAAN 1: KARTU STATISTIK SESUAI TEMPLATE NEPTUNE
        - Menggunakan komponen .widget.widget-stats dari template.
        - Tampilan lebih bersih, ikon lebih besar dan konsisten.
        - Menghapus style inline dan custom gradient.
        ================================================================== -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card widget widget-stats">
                    <div class="card-body">
                        <div class="widget-stats-container d-flex">
                            <div class="widget-stats-icon widget-stats-icon-primary">
                                <i class="material-icons-outlined">poll</i>
                            </div>
                            <div class="widget-stats-content flex-fill">
                                <span class="widget-stats-title">Total Survei</span>
                                <span class="widget-stats-amount"><?= number_format($summary['total_surveys'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card widget widget-stats">
                    <div class="card-body">
                        <div class="widget-stats-container d-flex">
                            <div class="widget-stats-icon widget-stats-icon-success">
                                <i class="material-icons-outlined">task_alt</i>
                            </div>
                            <div class="widget-stats-content flex-fill">
                                <span class="widget-stats-title">Survei Aktif</span>
                                <span class="widget-stats-amount"><?= number_format($summary['active_surveys'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card widget widget-stats">
                    <div class="card-body">
                        <div class="widget-stats-container d-flex">
                            <div class="widget-stats-icon widget-stats-icon-warning">
                                <i class="material-icons-outlined">pending_actions</i>
                            </div>
                            <div class="widget-stats-content flex-fill">
                                <span class="widget-stats-title">Akan Datang</span>
                                <span class="widget-stats-amount"><?= number_format($summary['upcoming_surveys'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card widget widget-stats">
                    <div class="card-body">
                        <div class="widget-stats-container d-flex">
                            <div class="widget-stats-icon widget-stats-icon-info">
                                <i class="material-icons-outlined">group</i>
                            </div>
                            <div class="widget-stats-content flex-fill">
                                <span class="widget-stats-title">Total Responden</span>
                                <span class="widget-stats-amount"><?= number_format($summary['total_responses'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                    <h5 class="card-title m-0">Daftar Semua Survei</h5>
                    <a href="<?= base_url('admin/surveys/create') ?>" class="btn btn-primary"><i class="material-icons-outlined me-1">add</i>Buat Survei Baru</a>
                </div>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="survey-table" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Judul Survei</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Responden</th>
                                <th>Periode</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($surveys as $survey) : ?>
                                <tr>
                                    <td>
                                        <p class="mb-0 fw-bold"><?= esc($survey['title']) ?></p>
                                        <small class="text-muted">Dibuat oleh: <?= esc($survey['creator_name'] ?? 'N/A') ?></small>
                                    </td>
                                    <td class="text-center">
                                        <!-- =================================================================
                                        PENYEMPURNAAN 2: LOGIKA STATUS YANG LEBIH AKURAT
                                        - Menghitung status (Aktif, Selesai, dll.) berdasarkan tanggal dan is_active.
                                        - Menggunakan warna badge yang sesuai dari template Neptune.
                                        ================================================================== -->
                                        <?php
                                        $now = time();
                                        $startDate = strtotime($survey['start_date']);
                                        $endDate = strtotime($survey['end_date']);
                                        $status = '';
                                        $badgeClass = '';

                                        if ($survey['is_active'] == 0) {
                                            $status = 'Nonaktif';
                                            $badgeClass = 'badge-danger';
                                        } elseif ($startDate > $now) {
                                            $status = 'Akan Datang';
                                            $badgeClass = 'badge-info';
                                        } elseif ($endDate < $now) {
                                            $status = 'Selesai';
                                            $badgeClass = 'badge-secondary';
                                        } else {
                                            $status = 'Aktif';
                                            $badgeClass = 'badge-success';
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                    </td>
                                    <td class="text-center"><?= number_format($survey['response_count']) ?></td>
                                    <td>
                                        <p class="mb-0"><span class="text-success"><?= date('d M Y', $startDate) ?></span></p>
                                        <p class="mb-0"><span class="text-danger"><?= date('d M Y', $endDate) ?></span></p>
                                    </td>
                                    <td class="text-center">
                                        <!-- =================================================================
                                        PENYEMPURNAAN 3: TOMBOL AKSI YANG LEBIH RAPI
                                        - Mengelompokkan aksi utama (Hasil, Edit, Hapus) agar mudah diakses.
                                        - Memasukkan aksi sekunder (Pratinjau, Duplikat) ke dalam dropdown.
                                        - Menggunakan ikon Material yang konsisten dengan template.
                                        ================================================================== -->
                                        <div class="d-inline-flex">
                                            <a href="<?= base_url('admin/surveys/results/' . $survey['id']) ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Lihat Hasil"><i class="material-icons-outlined">bar_chart</i></a>
                                            <a href="<?= base_url('admin/surveys/edit/' . $survey['id']) ?>" class="btn btn-sm btn-light mx-1" data-bs-toggle="tooltip" title="Edit"><i class="material-icons-outlined">edit</i></a>
                                            <a href="#" onclick="confirmDelete(<?= $survey['id'] ?>, '<?= esc($survey['title'], 'js') ?>')" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Hapus"><i class="material-icons-outlined">delete_outline</i></a>

                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light ms-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="material-icons-outlined">more_vert</i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="<?= base_url('surveys/take/' . $survey['id']) ?>" target="_blank"><i class="material-icons-outlined me-2">visibility</i>Pratinjau</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="cloneSurvey(<?= $survey['id'] ?>, '<?= esc($survey['title'], 'js') ?>')"><i class="material-icons-outlined me-2">content_copy</i>Duplikat</a></li>
                                                </ul>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#survey-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            },
            "columnDefs": [{
                "orderable": false,
                "targets": 4
            }],
            "order": [
                [3, "desc"]
            ]
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });

    function confirmDelete(surveyId, surveyTitle) {
        Swal.fire({
            title: 'Anda yakin?',
            html: `Survei "<b>${surveyTitle}</b>" akan dihapus secara permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Menggunakan Fetch API untuk request DELETE
                fetch(`<?= base_url('admin/surveys/delete/') ?>${surveyId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghubungi server.', 'error');
                    });
            }
        });
    }

    function cloneSurvey(surveyId, surveyTitle) {
        Swal.fire({
            title: 'Duplikat Survei?',
            html: `Ini akan membuat salinan dari survei "<b>${surveyTitle}</b>".`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, duplikat!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('admin/surveys/clone/') ?>${surveyId}`;
            }
        });
    }
</script>
<?= $this->endSection() ?>