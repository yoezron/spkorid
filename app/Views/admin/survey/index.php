<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Survei
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/forms/switches.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4">

                <div class="d-flex justify-content-between mb-4">
                    <h4>Daftar Semua Survei</h4>
                    <a href="<?= base_url('admin/surveys/create') ?>" class="btn btn-success">
                        <i data-feather="plus"></i> Buat Survei Baru
                    </a>
                </div>

                <table id="survey-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Judul Survei</th>
                            <th>Deskripsi Singkat</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Responden</th>
                            <th class="no-content">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($surveys)): ?>
                            <?php foreach ($surveys as $survey): ?>
                                <tr>
                                    <td><strong><?= esc($survey['title']) ?></strong></td>
                                    <td><?= esc(character_limiter($survey['description'], 50)) ?></td>
                                    <td><?= date('d M Y', strtotime($survey['start_date'])) ?> - <?= date('d M Y', strtotime($survey['end_date'])) ?></td>
                                    <td>
                                        <label class="switch s-icons s-outline s-outline-success mr-2">
                                            <input type="checkbox" <?= $survey['is_active'] ? 'checked' : '' ?> onchange="toggleStatus(<?= $survey['id'] ?>)">
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <?php // Logika untuk menampilkan jumlah responden (membutuhkan join di model) 
                                        ?>
                                        <span class="badge badge-info">0 Responden</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="<?= base_url('admin/surveys/results/' . $survey['id']) ?>" class="btn btn-sm btn-primary mr-2" title="Lihat Hasil">
                                                <i data-feather="pie-chart"></i>
                                            </a>
                                            <a href="<?= base_url('admin/surveys/edit/' . $survey['id']) ?>" class="btn btn-sm btn-warning mr-2" title="Edit Survei">
                                                <i data-feather="edit-2"></i>
                                            </a>
                                            <form action="<?= base_url('admin/surveys/delete/' . $survey['id']) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus survei ini? Semua data jawaban terkait akan ikut terhapus.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Survei">
                                                    <i data-feather="trash-2"></i>
                                                </button>
                                            </form>
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

<?php // Hidden form untuk toggle status 
?>
<form id="toggle-status-form" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#survey-table').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                // Terjemahan Bahasa Indonesia...
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Cari...",
                "sLengthMenu": "Hasil :  _MENU_",
            },
            "order": [
                [2, "desc"]
            ], // Urutkan berdasarkan Periode
            "stripeClasses": [],
            "lengthMenu": [10, 20, 50],
            "pageLength": 10
        });
    });

    // Fungsi untuk toggle status survei
    function toggleStatus(surveyId) {
        const form = document.getElementById('toggle-status-form');
        form.action = `<?= base_url('admin/surveys/toggle-status/') ?>${surveyId}`;
        form.submit();
    }
</script>
<?= $this->endSection() ?>