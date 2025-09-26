<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('member/forum/category/' . $thread['category_slug']) ?>">
                    <?= esc($thread['category_name']) ?>
                </a>
            </li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 300px;">
                <?= esc($thread['title']) ?>
            </li>
        </ol>
    </nav>

    <!-- Thread Header -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <?php if ($thread['is_pinned']): ?>
                        <i class="fas fa-thumbtack me-2" title="Pinned"></i>
                    <?php endif; ?>
                    <?php if ($thread['is_locked']): ?>
                        <i class="fas fa-lock me-2" title="Locked"></i>
                    <?php endif; ?>
                    <?= esc($thread['title']) ?>
                </h4>

                <!-- Thread Actions -->
                <?php if ($isAdmin || $isAuthor): ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($isAuthor || $isAdmin): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('member/forum/edit-thread/' . $thread['id']) ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($isAdmin): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('member/forum/toggle-pin/' . $thread['id']) ?>"
                                        onclick="return confirm('Toggle pin status?')">
                                        <i class="fas fa-thumbtack"></i>
                                        <?= $thread['is_pinned'] ? 'Unpin' : 'Pin' ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('member/forum/toggle-lock/' . $thread['id']) ?>"
                                        onclick="return confirm('Toggle lock status?')">
                                        <i class="fas fa-lock"></i>
                                        <?= $thread['is_locked'] ? 'Unlock' : 'Lock' ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($isAuthor || $isAdmin): ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger"
                                        href="<?= base_url('member/forum/delete-thread/' . $thread['id']) ?>"
                                        onclick="return confirm('Hapus diskusi ini? Tindakan ini tidak dapat dibatalkan.')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body">
            <!-- Author Info -->
            <div class="row">
                <div class="col-md-2 text-center border-end">
                    <img src="<?= base_url(!empty($thread['author_photo']) ? $thread['author_photo'] : 'assets/images/avatars/avatar.png') ?>"
                        class="rounded-circle mb-2" width="80" height="80" alt="<?= esc($thread['author_name']) ?>">
                    <h6 class="mb-1"><?= esc($thread['author_name']) ?></h6>
                    <small class="text-muted d-block"><?= esc($thread['author_email']) ?></small>
                    <a href="<?= base_url('member/forum/user/' . $thread['user_id']) ?>" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-user"></i> Profil
                    </a>
                </div>

                <!-- Thread Content -->
                <div class="col-md-10">
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> <?= date('d M Y H:i', strtotime($thread['created_at'])) ?> •
                            <i class="fas fa-eye"></i> <?= number_format($thread['views']) ?> views
                        </small>
                    </div>

                    <div class="thread-content">
                        <?= nl2br(esc($thread['content'])) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Replies Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-comments"></i> Balasan
                <span class="badge bg-info"><?= count($replies) ?></span>
            </h5>
        </div>

        <div class="card-body p-0">
            <?php if (empty($replies)): ?>
                <div class="text-center p-5">
                    <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada balasan. Jadilah yang pertama memberikan tanggapan!</p>
                </div>
            <?php else: ?>
                <?php foreach ($replies as $index => $reply): ?>
                    <div class="border-bottom p-3" id="reply-<?= $reply['id'] ?>">
                        <div class="row">
                            <div class="col-md-2 text-center border-end">
                                <img src="<?= base_url(!empty($reply['author_photo']) ? $reply['author_photo'] : 'assets/images/avatars/avatar.png') ?>"
                                    class="rounded-circle mb-2" width="60" height="60" alt="<?= esc($reply['author_name']) ?>">
                                <h6 class="small mb-0"><?= esc($reply['author_name']) ?></h6>
                                <small class="text-muted d-block"><?= esc($reply['status_kepegawaian'] ?? 'Anggota') ?></small>
                            </div>

                            <div class="col-md-10">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> <?= date('d M Y H:i', strtotime($reply['created_at'])) ?>
                                        <?php if ($reply['is_edited']): ?>
                                            • <i class="fas fa-edit"></i> Diedit <?= date('d M Y H:i', strtotime($reply['edited_at'])) ?>
                                            oleh <?= esc($reply['editor_name']) ?>
                                        <?php endif; ?>
                                    </small>

                                    <!-- Reply Actions -->
                                    <?php if ($reply['user_id'] == session()->get('user_id') || $isAdmin): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item edit-reply" href="#"
                                                        data-reply-id="<?= $reply['id'] ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </li>
                                                <?php if ($isAuthor && !$reply['is_solution']): ?>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="<?= base_url('member/forum/mark-solution/' . $reply['id']) ?>"
                                                            onclick="return confirm('Tandai sebagai solusi?')">
                                                            <i class="fas fa-check"></i> Tandai Solusi
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        href="<?= base_url('member/forum/delete-reply/' . $reply['id']) ?>"
                                                        onclick="return confirm('Hapus balasan?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($reply['is_solution']): ?>
                                    <div class="alert alert-success py-1 px-2 mb-2">
                                        <small><i class="fas fa-check-circle"></i> Ditandai sebagai Solusi</small>
                                    </div>
                                <?php endif; ?>

                                <div class="reply-content" id="reply-content-<?= $reply['id'] ?>">
                                    <?= nl2br(esc($reply['content'])) ?>
                                </div>

                                <!-- Edit Form (Hidden by default) -->
                                <div class="edit-reply-form d-none" id="edit-form-<?= $reply['id'] ?>">
                                    <form action="<?= base_url('member/forum/update-reply/' . $reply['id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <textarea name="content" class="form-control mb-2" rows="3" required><?= $reply['content'] ?></textarea>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                        <button type="button" class="btn btn-sm btn-secondary cancel-edit"
                                            data-reply-id="<?= $reply['id'] ?>">Batal</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($pager): ?>
            <div class="card-footer">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Reply Form -->
    <?php if ($canReply): ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-reply"></i> Tulis Balasan</h5>
            </div>
            <div class="card-body">
                <?php if (session()->get('logged_in')): ?>
                    <form action="<?= base_url('member/forum/reply/' . $thread['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="5"
                                placeholder="Tulis balasan Anda..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Balasan
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-center mb-0">
                        <a href="<?= base_url('login') ?>" class="btn btn-primary">Login untuk memberikan balasan</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> Diskusi ini telah dikunci. Tidak dapat menambahkan balasan.
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Edit reply functionality
        $('.edit-reply').click(function(e) {
            e.preventDefault();
            var replyId = $(this).data('reply-id');
            $('#reply-content-' + replyId).addClass('d-none');
            $('#edit-form-' + replyId).removeClass('d-none');
        });

        $('.cancel-edit').click(function() {
            var replyId = $(this).data('reply-id');
            $('#reply-content-' + replyId).removeClass('d-none');
            $('#edit-form-' + replyId).addClass('d-none');
        });

        // Initialize tooltips
        $('[title]').tooltip();
    });
</script>
<?= $this->endSection() ?>