<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen User
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
                    <h4>Daftar User Sistem</h4>
                    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-success">
                        <i data-feather="user-plus"></i> Tambah User Baru
                    </a>
                </div>

                <table id="user-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="no-content">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= esc($user['username']) ?></td>
                                    <td><?= esc($user['nama_lengkap'] ?? 'N/A') ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <span class="badge badge-primary"><?= esc(ucwords(str_replace('_', ' ', $user['role_name']))) ?></span>
                                    </td>
                                    <td>
                                        <label class="switch s-icons s-outline s-outline-success mr-2">
                                            <input type="checkbox" <?= $user['is_active'] ? 'checked' : '' ?> onchange="toggleStatus(<?= $user['id'] ?>)">
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="<?= base_url('admin/users/activity/' . $user['id']) ?>" class="btn btn-sm btn-dark mr-2" title="Lihat Aktivitas">
                                                <i data-feather="list"></i>
                                            </a>
                                            <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning mr-2" title="Edit User">
                                                <i data-feather="edit-2"></i>
                                            </a>
                                            <form action="<?= base_url('admin/users/delete/' . $user['id']) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Ini tidak akan menghapus data anggota terkait.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus User">
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
        $('#user-table').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Cari...",
                "sLengthMenu": "Hasil :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [10, 20, 50, 100],
            "pageLength": 10
        });
    });

    function toggleStatus(userId) {
        const form = document.getElementById('toggle-status-form');
        form.action = `<?= base_url('admin/users/toggle-status/') ?>${userId}`;
        form.submit();
    }
</script>
<?= $this->endSection() ?>