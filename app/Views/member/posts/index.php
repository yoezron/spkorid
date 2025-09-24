<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Artikel Saya
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description d-flex align-items-center">
            <h1 class="flex-grow-1">Artikel Saya</h1>
            <a href="<?= base_url('member/posts/create') ?>" class="btn btn-primary">
                <i class="material-icons-outlined">add</i>Tulis Artikel Baru
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Dilihat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($posts)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <th scope="row"><?= $i++ ?></th>
                                        <td><?= esc(character_limiter($post['title'], 40)) ?></td>
                                        <td><?= esc($post['category_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'badge-secondary';
                                            if ($post['status'] == 'published') {
                                                $statusClass = 'badge-success';
                                            } elseif ($post['status'] == 'pending') {
                                                $statusClass = 'badge-warning';
                                            } elseif ($post['status'] == 'draft') {
                                                $statusClass = 'badge-info';
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= esc(ucfirst($post['status'])) ?></span>
                                        </td>
                                        <td><?= number_format($post['view_count']) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('member/posts/edit/' . $post['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="<?= base_url('blog/view/' . $post['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $post['id'] ?>">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="deleteModal<?= $post['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus artikel berjudul "<?= esc($post['title']) ?>"? Tindakan ini tidak dapat dibatalkan.
                                                </div>
                                                <div class="modal-footer">
                                                    <?= form_open('member/posts/delete/' . $post['id'], 'class="d-inline"') ?>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                                    <?= form_close() ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <p class="mt-4">Anda belum menulis artikel apapun.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <?= $pager->links('default', 'bootstrap_5') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>