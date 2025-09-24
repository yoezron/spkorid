<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($thread['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('member/forum/category/' . $thread['category_slug']) ?>"><?= esc($thread['category_name']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc(character_limiter($thread['title'], 50)) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1 class="fs-2"><?= esc($thread['title']) ?></h1>
            <p class="text-muted">
                Diskusi ini dimulai oleh <?= esc($thread['author_name']) ?> pada <?= date('d F Y, H:i', strtotime($thread['created_at'])) ?>
            </p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body d-flex">
                <div class="text-center me-4" style="flex: 0 0 120px;">
                    <img src="<?= base_url('uploads/avatars/' . ($thread['author_avatar'] ?? 'default.png')) ?>" class="img-fluid rounded-circle mb-2" alt="avatar" style="width: 80px; height: 80px; object-fit: cover;">
                    <h6 class="card-title mb-0"><?= esc($thread['author_name']) ?></h6>
                    <small class="text-muted"><?= esc($thread['author_role']) ?></small>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small mb-2">
                        Diposting pada: <?= date('d F Y, H:i', strtotime($thread['created_at'])) ?>
                    </div>
                    <div class="forum-content">
                        <?= nl2br(esc($thread['content'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mt-5 mb-3">Balasan (<?= count($replies) ?>)</h4>

        <?php if (!empty($replies)): ?>
            <?php foreach ($replies as $reply): ?>
                <div class="card mb-3">
                    <div class="card-body d-flex">
                        <div class="text-center me-4" style="flex: 0 0 120px;">
                            <img src="<?= base_url('uploads/avatars/' . ($reply['author_avatar'] ?? 'default.png')) ?>" class="img-fluid rounded-circle mb-2" alt="avatar" style="width: 80px; height: 80px; object-fit: cover;">
                            <h6 class="card-title mb-0"><?= esc($reply['author_name']) ?></h6>
                            <small class="text-muted"><?= esc($reply['author_role']) ?></small>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-2">
                                Dibalas pada: <?= date('d F Y, H:i', strtotime($reply['created_at'])) ?>
                            </div>
                            <div class="forum-content">
                                <?= nl2br(esc($reply['content'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Belum ada balasan di diskusi ini.</p>
        <?php endif; ?>

        <div class="mt-4">
            <?= $pager->links('default', 'bootstrap_5') ?>
        </div>


        <div class="card mt-5">
            <div class="card-header">
                <h5 class="card-title">Tinggalkan Balasan</h5>
            </div>
            <div class="card-body">
                <?= form_open('member/forum/reply/' . $thread['id']) ?>
                <div class="mb-3">
                    <textarea class="form-control" name="content" rows="5" placeholder="Tulis balasan Anda di sini..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Balasan</button>
                <?= form_close() ?>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>