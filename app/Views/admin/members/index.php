<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Daftar Anggota
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

                <div class="d-flex justify-content-between mb-4">
                    <h4>Daftar Semua Anggota Aktif</h4>
                    <a href="<?= base_url('admin/members/create') ?>" class="btn btn-success">
                        <i data-feather="user-plus"></i> Tambah Anggota
                    </a>
                </div>

                <table id="member-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>No. Anggota</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Kampus</th>
                            <th>Status</th>
                            <th class="no-content">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($members)): ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $foto = $member['foto_path'];
                                        $default_foto = base_url('assets/img/90x90.jpg');
                                        $user_foto = ($foto && file_exists(FCPATH . $foto)) ? base_url($foto) : $default_foto;
                                        ?>
                                        <img src="<?= $user_foto ?>" alt="<?= esc($member['nama_lengkap']) ?>" class="rounded-circle" width="40" height="40">
                                    </td>
                                    <td><?= esc($member['nomor_anggota']) ?></td>
                                    <td><?= esc($member['nama_lengkap']) ?></td>
                                    <td><?= esc($member['email']) ?></td>
                                    <td><?= esc($member['nama_kampus'] ?? 'N/A') ?></td>
                                    <td><span class="badge badge-success"><?= esc(ucfirst($member['status_keanggotaan'])) ?></span></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-sm btn-info mr-2" title="Lihat Detail">
                                                <i data-feather="eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/members/edit/' . $member['id']) ?>" class="btn btn-sm btn-warning mr-2" title="Edit Anggota">
                                                <i data-feather="edit-2"></i>
                                            </a>
                                            <form action="<?= base_url('admin/members/delete/' . $member['id']) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Anggota">
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#member-table').DataTable({
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
</script>
<?= $this->endSection() ?>