<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Verifikasi Anggota
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4">

                <h4 class="mb-4">Anggota Menunggu Verifikasi</h4>

                <table id="pending-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Kampus</th>
                            <th>Tanggal Daftar</th>
                            <th class="no-content">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pending_members)): ?>
                            <?php foreach ($pending_members as $member): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <?php
                                            $foto = $member['foto_path'];
                                            $default_foto = base_url('assets/img/90x90.jpg');
                                            $user_foto = ($foto && file_exists(FCPATH . $foto)) ? base_url($foto) : $default_foto;
                                            ?>
                                            <div class="usr-img-frame mr-2 rounded-circle">
                                                <img alt="avatar" class="img-fluid rounded-circle" src="<?= $user_foto ?>">
                                            </div>
                                            <p class="align-self-center mb-0"><?= esc($member['nama_lengkap']) ?></p>
                                        </div>
                                    </td>
                                    <td><?= esc($member['email']) ?></td>
                                    <td><?= esc($member['nama_kampus'] ?? 'N/A') ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($member['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-sm btn-primary">
                                            <i data-feather="eye" class="mr-1"></i> Review & Verifikasi
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada anggota yang menunggu verifikasi saat ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#pending-table').DataTable({
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
            "lengthMenu": [10, 20, 50],
            "pageLength": 10
        });
    });
</script>
<?= $this->endSection() ?>