<?= $this->extend('layouts/member_layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
            <li class="breadcrumb-item active">Profil Pengguna</li>
        </ol>
    </nav>

    <!-- User Profile Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    <img src="<?= base_url('uploads/photos/' . ($user['foto'] ?? 'default.png')) ?>"
                        class="rounded-circle mb-3"
                        width="120"
                        height="120"
                        alt="<?= esc($user['nama_lengkap']) ?>">
                </div>
                <div class="col-md-7">
                    <h3><?= esc($user['nama_lengkap']) ?></h3>
                    <p class="text-muted mb-2">
                        <i class="fas fa-briefcase"></i> <?= esc($user['status_kepegawaian'] ?? 'Anggota') ?>
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-university"></i> <?= esc($user['asal_kampus'] ?? '-') ?>
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar-alt"></i> Bergabung sejak <?= date('d M Y', strtotime($user['created_at'])) ?>
                    </p>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Statistik Forum</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-comments"></i> Diskusi:</span>
                                <strong><?= number_format($stats['total_threads']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-reply"></i> Balasan:</span>
                                <strong><?= number_format($stats['total_replies']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-check-circle"></i> Solusi:</span>
                                <strong><?= number_format($stats['solutions']) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Threads -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i> Diskusi Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($threads)): ?>
                        <div class="text-center p-4">
                            <p class="text-muted mb-0">Belum ada diskusi</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($threads, 0, 5) as $thread): ?>
                                <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 text-truncate" style="max-width: 350px;">
                                            <?= esc($thread['title']) ?>
                                            <?php if ($thread['is_pinned']): ?>
                                                <i class="fas fa-thumbtack text-danger ms-1" title="Pinned"></i>
                                            <?php endif; ?>
                                            <?php if ($thread['is_locked']): ?>
                                                <i class="fas fa-lock text-warning ms-1" title="Locked"></i>
                                            <?php endif; ?>
                                        </h6>
                                        <small>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-comment"></i> <?= $thread['reply_count'] ?>
                                            </span>
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-folder"></i> <?= esc($thread['category_name']) ?> •
                                        <i class="fas fa-clock"></i> <?= date('d M Y', strtotime($thread['created_at'])) ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($threads) > 5): ?>
                            <div class="card-footer text-center">
                                <small class="text-muted">
                                    Menampilkan 5 dari <?= count($threads) ?> diskusi
                                </small>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Replies -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-reply"></i> Balasan Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($replies)): ?>
                        <div class="text-center p-4">
                            <p class="text-muted mb-0">Belum ada balasan</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($replies as $reply): ?>
                                <a href="<?= base_url('member/forum/thread/' . $reply['thread_id'] . '#reply-' . $reply['id']) ?>"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div>
                                            <h6 class="mb-1 text-truncate" style="max-width: 350px;">
                                                Re: <?= esc($reply['thread_title']) ?>
                                                <?php if ($reply['is_solution']): ?>
                                                    <span class="badge bg-success ms-1">Solusi</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1 small text-muted">
                                                <?= character_limiter(strip_tags($reply['content']), 100) ?>
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> <?= date('d M Y H:i', strtotime($reply['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- All Threads Table -->
    <?php if (!empty($threads)): ?>
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Semua Diskusi (<?= count($threads) ?>)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th class="text-center">Balasan</th>
                                <th class="text-center">Views</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($threads as $thread): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>"
                                            class="text-decoration-none">
                                            <?= esc($thread['title']) ?>
                                            <?php if ($thread['is_pinned']): ?>
                                                <i class="fas fa-thumbtack text-danger ms-1" title="Pinned"></i>
                                            <?php endif; ?>
                                            <?php if ($thread['is_locked']): ?>
                                                <i class="fas fa-lock text-warning ms-1" title="Locked"></i>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= esc($thread['category_name']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?= number_format($thread['reply_count']) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= number_format($thread['views']) ?>
                                    </td>
                                    <td>
                                        <small><?= date('d M Y', strtotime($thread['created_at'])) ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[title]').tooltip();
    });
</script>
<?= $this->endSection() ?>