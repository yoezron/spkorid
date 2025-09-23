<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Forum: <?= esc($category['name']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<style>
    #thread-table td {
        vertical-align: middle;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class.php="p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="<?= base_url('member/forum') ?>" class="btn btn-secondary mb-2"><i data-feather="arrow-left"></i> Kembali ke Kategori</a>
                        <h2><?= esc($category['name']) ?></h2>
                        <p><?= esc($category['description']) ?></p>
                    </div>
                    <div>
                        <a href="<?= base_url('member/forum/create-thread') ?>" class="btn btn-success">
                            <i data-feather="plus"></i> Buat Thread Baru
                        </a>
                    </div>
                </div>
                <hr>

                <div class="table-responsive">
                    <table id="thread-table" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Topik Diskusi</th>
                                <th>Penulis</th>
                                <th class="text-center">Balasan</th>
                                <th class="text-center">Dilihat</th>
                                <th>Aktivitas Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($threads)): ?>
                                <?php foreach ($threads as $thread): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>">
                                                <strong><?= esc($thread['title']) ?></strong>
                                            </a>
                                            <?php if ($thread['is_pinned']): ?>
                                                <span class="badge badge-warning">Pinned</span>
                                            <?php endif; ?>
                                            <?php if ($thread['is_locked']): ?>
                                                <span class="badge badge-danger">Terkunci</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($thread['author_name']) ?></td>
                                        <td class="text-center"><?= number_format($thread['reply_count']) ?></td>
                                        <td class="text-center"><?= number_format($thread['view_count']) ?></td>
                                        <td>
                                            <?php if ($thread['last_reply_at']): ?>
                                                <?= date('d M Y, H:i', strtotime($thread['last_reply_at'])) ?><br>
                                                <small>oleh <?= esc($thread['last_reply_author']) ?></small>
                                            <?php else: ?>
                                                <?= date('d M Y, H:i', strtotime($thread['created_at'])) ?>
                                            <?php endif; ?>
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
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#thread-table').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                /* Terjemahan... */
            },
            "order": [
                [4, "desc"]
            ], // Urutkan berdasarkan kolom Aktivitas Terakhir
            "stripeClasses": [],
            "lengthMenu": [10, 25, 50],
            "pageLength": 25
        });
    });
</script>
<?= $this->endSection() ?>