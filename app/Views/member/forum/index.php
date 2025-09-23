<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Forum Diskusi
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .category-item {
        transition: transform .2s;
    }

    .category-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px -10px #888ea8;
    }

    .category-item .widget-content {
        padding: 20px;
    }

    .category-item .category-title a {
        color: #3b3f5c;
        font-weight: 600;
        font-size: 1.2rem;
    }

    .category-item .category-description {
        color: #888ea8;
    }

    .category-item .category-stats {
        margin-top: 15px;
        display: flex;
        justify-content: space-between;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row">
        <div class="col-12">
            <div class="widget-content widget-content-area">
                <div class="d-flex justify-content-between align-items-center p-4">
                    <div>
                        <h2>Selamat Datang di Forum</h2>
                        <p>Pilih kategori untuk memulai diskusi atau membaca thread yang ada.</p>
                    </div>
                    <div>
                        <a href="<?= base_url('member/forum/create-thread') ?>" class="btn btn-lg btn-success">
                            <i data-feather="plus"></i> Buat Thread Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row layout-top-spacing">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-content-area br-6 category-item">
                        <div class="widget-content">
                            <div class="category-title">
                                <a href="<?= base_url('member/forum/category/' . $category['slug']) ?>">
                                    <?= esc($category['name']) ?>
                                </a>
                            </div>
                            <div class="category-description">
                                <p><?= esc($category['description']) ?></p>
                            </div>
                            <div class="category-stats">
                                <span><i data-feather="message-square"></i> <?= number_format($category['thread_count'] ?? 0) ?> Thread</span>
                                <span><i data-feather="message-circle"></i> <?= number_format($category['reply_count'] ?? 0) ?> Balasan</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">Belum ada kategori forum yang dibuat oleh admin.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>