<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Forum Diskusi
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description d-flex align-items-center">
            <h1 class="flex-grow-1">Forum Diskusi</h1>
            <a href="<?= base_url('member/forum/create-thread') ?>" class="btn btn-primary">
                <i class="material-icons-outlined">add</i>Buat Thread Baru
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Diskusi Terbaru</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($threads)): ?>
                        <?php foreach ($threads as $thread): ?>
                            <li class="list-group-item d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <img src="<?= base_url('uploads/avatars/' . ($thread['avatar'] ?? 'default.png')) ?>" alt="avatar">
                                </div>
                                <div class="flex-grow-1">
                                    <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>" class="text-decoration-none text-dark fw-bold fs-6"><?= esc($thread['title']) ?></a>
                                    <div class="text-muted small">
                                        <span>Oleh <?= esc($thread['author_name']) ?></span> |
                                        <span><?= count($thread['replies']) ?> balasan</span> |
                                        <span>Dilihat <?= esc($thread['view_count']) ?> kali</span>
                                    </div>
                                </div>
                                <div class="text-end text-muted small ms-3" style="min-width: 120px;">
                                    <div><?= date('d M Y', strtotime($thread['created_at'])) ?></div>
                                    <div><?= date('H:i', strtotime($thread['created_at'])) ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center">
                            <p class="mt-4">Belum ada diskusi. Jadilah yang pertama memulai!</p>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="mt-4">
                    <?= $pager->links('default', 'bootstrap_5') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Kategori</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <a href="<?= base_url('member/forum/category/' . $category['slug']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <?= esc($category['name']) ?>
                                <span class="badge bg-primary rounded-pill"><?= esc($category['thread_count']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">Tidak ada kategori ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>