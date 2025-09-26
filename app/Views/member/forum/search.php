<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
            <li class="breadcrumb-item active">Hasil Pencarian</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-search"></i> Hasil Pencarian
        </h1>
        <p class="text-muted">
            Menampilkan hasil untuk: <strong>"<?= esc($keyword) ?>"</strong>
        </p>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('member/forum/search') ?>" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="q"
                            placeholder="Cari diskusi atau balasan..."
                            value="<?= esc($keyword) ?>" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="all" <?= ($type == 'all') ? 'selected' : '' ?>>Semua</option>
                        <option value="threads" <?= ($type == 'threads') ? 'selected' : '' ?>>Diskusi Saja</option>
                        <option value="replies" <?= ($type == 'replies') ? 'selected' : '' ?>>Balasan Saja</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php
                        $request = \Config\Services::request();
                        $selectedCategory = $request->getGet('category');
                        ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($selectedCategory == $cat['id']) ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    <div class="row">
        <div class="col-lg-12">
            <!-- Thread Results -->
            <?php if (isset($results['threads']) && !empty($results['threads'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-comments"></i> Diskusi yang Cocok
                            <span class="badge bg-light text-primary ms-2"><?= count($results['threads']) ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($results['threads'] as $thread): ?>
                                <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div>
                                            <h6 class="mb-1">
                                                <?= highlightKeyword(esc($thread['title']), $keyword) ?>
                                                <?php if ($thread['is_pinned']): ?>
                                                    <i class="fas fa-thumbtack text-danger ms-2" title="Pinned"></i>
                                                <?php endif; ?>
                                                <?php if ($thread['is_locked']): ?>
                                                    <i class="fas fa-lock text-warning ms-2" title="Locked"></i>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                <?= character_limiter(strip_tags(highlightKeyword(esc($thread['content']), $keyword)), 150) ?>
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?= esc($thread['author_name']) ?> •
                                                <i class="fas fa-folder"></i> <?= esc($thread['category_name']) ?> •
                                                <i class="fas fa-clock"></i> <?= date('d M Y', strtotime($thread['created_at'])) ?> •
                                                <i class="fas fa-eye"></i> <?= number_format($thread['views']) ?> views
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reply Results -->
            <?php if (isset($results['replies']) && !empty($results['replies'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-reply"></i> Balasan yang Cocok
                            <span class="badge bg-light text-success ms-2"><?= count($results['replies']) ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($results['replies'] as $reply): ?>
                                <a href="<?= base_url('member/forum/thread/' . $reply['thread_id'] . '#reply-' . $reply['id']) ?>"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div>
                                            <h6 class="mb-1">
                                                Re: <?= esc($reply['thread_title']) ?>
                                                <?php if ($reply['is_solution']): ?>
                                                    <span class="badge bg-success ms-2">Solusi</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                <?= character_limiter(strip_tags(highlightKeyword(esc($reply['content']), $keyword)), 150) ?>
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?= esc($reply['author_name']) ?> •
                                                <i class="fas fa-clock"></i> <?= date('d M Y H:i', strtotime($reply['created_at'])) ?>
                                                <?php if ($reply['is_edited']): ?>
                                                    • <i class="fas fa-edit"></i> Diedit
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- No Results -->
            <?php if ((empty($results['threads']) && empty($results['replies'])) ||
                (isset($results['threads']) && empty($results['threads']) && $type == 'threads') ||
                (isset($results['replies']) && empty($results['replies']) && $type == 'replies')
            ): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada hasil ditemukan</h5>
                        <p class="text-muted">
                            Coba gunakan kata kunci yang berbeda atau perluas kriteria pencarian Anda.
                        </p>
                        <a href="<?= base_url('member/forum') ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left"></i> Kembali ke Forum
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Helper function to highlight search keyword
    function highlightText(text, keyword) {
        if (!keyword) return text;

        var regex = new RegExp('(' + keyword + ')', 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    $(document).ready(function() {
        // Auto-submit on type change
        $('select[name="type"]').change(function() {
            $(this).closest('form').submit();
        });
    });
</script>
<?= $this->endSection() ?>

<?php
// Helper function for highlighting keyword in PHP
function highlightKeyword($text, $keyword)
{
    if (empty($keyword)) return $text;

    $highlighted = preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<mark>$1</mark>', $text);
    return $highlighted;
}
?>