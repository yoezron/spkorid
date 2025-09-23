<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($thread['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/editors/quill/quill.snow.css') ?>" rel="stylesheet" type="text/css">
<style>
    .thread-post,
    .reply-post {
        display: flex;
        margin-bottom: 25px;
    }

    .post-author-info {
        flex-shrink: 0;
        width: 150px;
        text-align: center;
        padding-right: 20px;
        border-right: 1px solid #e0e6ed;
    }

    .post-author-info img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }

    .post-author-info .author-name {
        font-weight: 600;
    }

    .post-author-info .author-role {
        font-size: 0.9em;
        color: #888ea8;
    }

    .post-content {
        padding-left: 20px;
        width: 100%;
    }

    .post-meta {
        color: #888ea8;
        font-size: 0.9em;
        margin-bottom: 15px;
    }

    .post-body {
        line-height: 1.8;
    }

    .reply-form {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid #e0e6ed;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12">

            <div class="widget-content widget-content-area br-6 p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="<?= base_url('member/forum/category/' . $thread['category_slug']) ?>" class="btn btn-secondary mb-3"><i data-feather="arrow-left"></i> Kembali ke Daftar Thread</a>
                        <h2><?= esc($thread['title']) ?></h2>
                    </div>
                </div>
                <hr>

                <div class="thread-post">
                    <div class="post-author-info">
                        <img src="<?= base_url($thread['author_photo'] ?? 'assets/img/90x90.jpg') ?>" alt="avatar">
                        <p class="author-name"><?= esc($thread['author_name']) ?></p>
                        <p class="author-role"><?= esc(ucwords(str_replace('_', ' ', $thread['author_role']))) ?></p>
                    </div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span>Diposting pada: <?= date('d M Y, H:i', strtotime($thread['created_at'])) ?></span>
                        </div>
                        <div class="post-body">
                            <?= $thread['content'] // Tampilkan HTML asli 
                            ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($replies)): ?>
                    <?php foreach ($replies as $reply): ?>
                        <hr>
                        <div class="reply-post">
                            <div class="post-author-info">
                                <img src="<?= base_url($reply['author_photo'] ?? 'assets/img/90x90.jpg') ?>" alt="avatar">
                                <p class="author-name"><?= esc($reply['author_name']) ?></p>
                                <p class="author-role"><?= esc(ucwords(str_replace('_', ' ', $reply['author_role']))) ?></p>
                            </div>
                            <div class="post-content">
                                <div class="post-meta">
                                    <span>Membalas pada: <?= date('d M Y, H:i', strtotime($reply['created_at'])) ?></span>
                                </div>
                                <div class="post-body">
                                    <?= $reply['content'] // Tampilkan HTML asli 
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="reply-form">
                    <h4>Tulis Balasan Anda</h4>
                    <?php if ($thread['is_locked']): ?>
                        <div class="alert alert-warning">Diskusi ini telah dikunci oleh moderator. Anda tidak dapat mengirim balasan baru.</div>
                    <?php else: ?>
                        <form action="<?= base_url('member/forum/reply/' . $thread['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <div id="editor-container" style="min-height: 150px;"></div>
                                <input type="hidden" name="content" id="content-input">
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Kirim Balasan</button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/editors/quill/quill.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Hanya inisialisasi editor jika thread tidak dikunci
        <?php if (!$thread['is_locked']): ?>
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        ['link', 'blockquote'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }]
                    ]
                }
            });

            var form = document.querySelector('form');
            form.onsubmit = function() {
                var contentInput = document.querySelector('#content-input');
                contentInput.value = quill.root.innerHTML;
            };
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>