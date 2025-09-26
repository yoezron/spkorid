<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
            <li class="breadcrumb-item active"><?= esc($category['name']) ?></li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-<?= $category['icon'] ?? 'folder' ?> text-<?= $category['color'] ?? 'primary' ?>"></i>
                <?= esc($category['name']) ?>
            </h1>
            <?php if ($category['description']): ?>
                <p class="text-muted mb-0"><?= esc($category['description']) ?></p>
            <?php endif; ?>
        </div>
        <a href="<?= base_url('member/forum/create?category=' . $category['id']) ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Buat Diskusi
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search"
                            placeholder="Cari diskusi dalam kategori ini..."
                            value="<?= esc($keyword ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="latest" <?= ($sort == 'latest') ? 'selected' : '' ?>>Terbaru</option>
                        <option value="oldest" <?= ($sort == 'oldest') ? 'selected' : '' ?>>Terlama</option>
                        <option value="popular" <?= ($sort == 'popular') ? 'selected' : '' ?>>Terpopuler</option>
                        <option value="unanswered" <?= ($sort == 'unanswered') ? 'selected' : '' ?>>Belum Terjawab</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Threads List -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($threads)): ?>
                <div class="text-center p-5">
                    <i class="fas fa-comments-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada diskusi dalam kategori ini</h5>
                    <p class="text-muted">Jadilah yang pertama memulai diskusi!</p>
                    <a href="<?= base_url('member/forum/create?category=' . $category['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Mulai Diskusi
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50"></th>
                                <th>Judul Diskusi</th>
                                <th class="text-center" width="100">Balasan</th>
                                <th class="text-center" width="100">Dilihat</th>
                                <th width="200">Aktivitas Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($threads as $thread): ?>
                                <tr>
                                    <td class="text-center align-middle">
                                        <?php if ($thread['is_pinned']): ?>
                                            <i class="fas fa-thumbtack text-danger" title="Pinned"></i>
                                        <?php elseif ($thread['is_locked']): ?>
                                            <i class="fas fa-lock text-warning" title="Locked"></i>
                                        <?php elseif ($thread['is_featured']): ?>
                                            <i class="fas fa-star text-warning" title="Featured"></i>
                                        <?php else: ?>
                                            <i class="fas fa-comment text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>"
                                                    class="text-decoration-none">
                                                    <?= esc($thread['title']) ?>
                                                </a>
                                                <?php if ($thread['reply_count'] == 0): ?>
                                                    <span class="badge bg-secondary ms-2">Belum Terjawab</span>
                                                <?php endif; ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?= esc($thread['author_name']) ?> •
                                                <i class="fas fa-clock"></i> <?= date('d M Y', strtotime($thread['created_at'])) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-info"><?= number_format($thread['reply_count'] ?? 0) ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-secondary"><?= number_format($thread['views'] ?? 0) ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($thread['last_reply_time']): ?>
                                            <small class="text-muted">
                                                <?= date('d M Y H:i', strtotime($thread['last_reply_time'])) ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">
                                                <?= date('d M Y H:i', strtotime($thread['created_at'])) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($pager): ?>
            <div class="card-footer">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Auto-submit form on sort change
        $('select[name="sort"]').change(function() {
            $(this).closest('form').submit();
        });

        // Tooltips
        $('[title]').tooltip();
    });
</script>
<?= $this->endSection() ?>