<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Artikel Saya
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
                    <h4>Daftar Artikel Tulisan Saya</h4>
                    <a href="<?= base_url('member/posts/create') ?>" class="btn btn-success">
                        <i data-feather="plus"></i> Tulis Artikel Baru
                    </a>
                </div>

                <table id="posts-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Judul Artikel</th>
                            <th>Kategori</th>
                            <th>Tanggal Dibuat</th>
                            <th>Status</th>
                            <th class="no-content">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><strong><?= esc($post['title']) ?></strong></td>
                                    <td><?= esc($post['category']) ?></td>
                                    <td><?= date('d M Y', strtotime($post['created_at'])) ?></td>
                                    <td>
                                        <?php
                                        $status_map = [
                                            'draft' => ['class' => 'secondary', 'text' => 'Draft'],
                                            'pending_review' => ['class' => 'warning', 'text' => 'Pending Review'],
                                            'published' => ['class' => 'success', 'text' => 'Published'],
                                            'rejected' => ['class' => 'danger', 'text' => 'Rejected'],
                                        ];
                                        $status = $status_map[$post['status']] ?? ['class' => 'dark', 'text' => 'Unknown'];
                                        ?>
                                        <span class="badge badge-<?= $status['class'] ?>"><?= $status['text'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <?php if ($post['status'] === 'published'): ?>
                                                <a href="<?= base_url('blog/view/' . $post['slug']) ?>" target="_blank" class="btn btn-sm btn-info mr-2" title="Lihat Artikel">
                                                    <i data-feather="eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('member/posts/edit/' . $post['id']) ?>" class="btn btn-sm btn-warning mr-2" title="Edit">
                                                <i data-feather="edit-2"></i>
                                            </a>
                                            <form action="<?= base_url('member/posts/delete/' . $post['id']) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
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
        $('#posts-table').DataTable({
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
                "sSearchPlaceholder": "Cari artikel...",
                "sLengthMenu": "Hasil :  _MENU_",
            },
            "order": [
                [2, "desc"]
            ],
            "stripeClasses": [],
            "lengthMenu": [10, 20, 50],
            "pageLength": 10
        });
    });
</script>
<?= $this->endSection() ?>