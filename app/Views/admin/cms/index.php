<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Content Management
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Content Management</h1>
            <span>Kelola halaman dan konten website</span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Daftar Halaman</h5>
                <a href="<?= base_url('admin/content/create') ?>" class="btn btn-primary btn-sm float-end">
                    <i class="material-icons">add</i> Tambah Halaman
                </a>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <div class="table-responsive">
                    <table class="table table-striped" id="datatable1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Halaman</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pages)): ?>
                                <?php foreach ($pages as $index => $page): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($page['page_title']) ?></td>
                                        <td><?= esc($page['page_slug'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($page['is_published']): ?>
                                                <span class="badge badge-success">Published</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y', strtotime($page['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/content/edit/' . $page['id']) ?>" class="btn btn-info" title="Edit">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                <?php if ($page['is_published']): ?>
                                                    <form action="<?= base_url('admin/content/unpublish/' . $page['id']) ?>" method="post" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-warning" title="Unpublish">
                                                            <i class="material-icons">visibility_off</i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="<?= base_url('admin/content/publish/' . $page['id']) ?>" method="post" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-success" title="Publish">
                                                            <i class="material-icons">visibility</i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form action="<?= base_url('admin/content/delete/' . $page['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin hapus halaman ini?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-danger" title="Hapus">
                                                        <i class="material-icons">delete</i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada halaman</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>