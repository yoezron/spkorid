<?= $this->extend('layouts/member_layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-comments"></i> Forum Diskusi Anggota
        </h1>
        <a href="<?= base_url('member/forum/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Buat Diskusi Baru
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= base_url('member/forum/search') ?>" method="get" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="q" placeholder="Cari diskusi..." value="<?= esc($keyword ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="all">Semua</option>
                        <option value="threads">Diskusi</option>
                        <option value="replies">Balasan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Categories Section -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder"></i> Kategori Forum</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($categories)): ?>
                        <div class="text-center p-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada kategori forum tersedia</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="50"></th>
                                        <th>Kategori</th>
                                        <th class="text-center" width="100">Diskusi</th>
                                        <th class="text-center" width="100">Balasan</th>
                                        <th width="200">Aktivitas Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td class="text-center">
                                                <i class="fas fa-<?= $category['icon'] ?? 'comments' ?> fa-2x text-<?= $category['color'] ?? 'primary' ?>"></i>
                                            </td>
                                            <td>
                                                <h6 class="mb-1">
                                                    <a href="<?= base_url('member/forum/category/' . $category['slug']) ?>"
                                                        class="text-decoration-none">
                                                        <?= esc($category['name']) ?>
                                                    </a>
                                                </h6>
                                                <?php if ($category['description']): ?>
                                                    <small class="text-muted"><?= esc($category['description']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info"><?= number_format($category['thread_count'] ?? 0) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary"><?= number_format($category['reply_count'] ?? 0) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($category['last_activity']): ?>
                                                    <small class="text-muted">
                                                        <?= date('d M Y H:i', strtotime($category['last_activity'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">Belum ada aktivitas</small>
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

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- User Statistics -->
            <?php if ($user_stats): ?>
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik Anda</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h4 class="mb-0"><?= $user_stats['threads'] ?></h4>
                                <small class="text-muted">Diskusi</small>
                            </div>
                            <div class="col-4">
                                <h4 class="mb-0"><?= $user_stats['replies'] ?></h4>
                                <small class="text-muted">Balasan</small>
                            </div>
                            <div class="col-4">
                                <h4 class="mb-0"><?= $user_stats['solutions'] ?></h4>
                                <small class="text-muted">Solusi</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent Threads -->
            <?php if (!empty($recent_threads)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-clock"></i> Diskusi Terbaru</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_threads as $thread): ?>
                            <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>"
                                class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-truncate" style="max-width: 250px;">
                                        <?= esc($thread['title']) ?>
                                    </h6>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> <?= esc($thread['author_name']) ?> •
                                    <i class="fas fa-folder"></i> <?= esc($thread['category_name']) ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Popular Threads -->
            <?php if (!empty($popular_threads)): ?>
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-fire"></i> Diskusi Populer</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($popular_threads as $thread): ?>
                            <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>"
                                class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-truncate" style="max-width: 250px;">
                                        <?= esc($thread['title']) ?>
                                    </h6>
                                    <small>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-eye"></i> <?= number_format($thread['views']) ?>
                                        </span>
                                    </small>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> <?= esc($thread['author_name']) ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Forum Rules -->
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-exclamation-circle"></i> Aturan Forum</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Hormati sesama anggota</li>
                        <li>Tidak diperbolehkan spam atau promosi</li>
                        <li>Gunakan bahasa yang sopan</li>
                        <li>Topik harus relevan dengan serikat</li>
                        <li>Dilarang menyebarkan informasi palsu</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Tooltip initialization
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
<?= $this->endSection() ?>