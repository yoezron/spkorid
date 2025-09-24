<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Forum: <?= esc($category['name']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($category['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="page-description d-flex align-items-center">
            <h1 class="flex-grow-1">Kategori: <?= esc($category['name']) ?></h1>
            <a href="<?= base_url('member/forum/create-thread?category=' . $category['id']) ?>" class="btn btn-primary">
                <i class="material-icons-outlined">add</i>Buat Thread di Kategori Ini
            </a>
        </div>
        <p class="mb-4"><?= esc($category['description']) ?></p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
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
                                        <span><?= esc($thread['reply_count']) ?> balasan</span> |
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
                            <p class="mt-4">Belum ada diskusi di kategori ini. Jadilah yang pertama memulai!</p>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="mt-4">
                    <?= $pager->links('default', 'bootstrap_5') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>