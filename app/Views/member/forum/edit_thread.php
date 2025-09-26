<?= $this->extend('layouts/member_layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>">
                    <?= character_limiter(esc($thread['title']), 30) ?>
                </a>
            </li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-edit"></i> Edit Diskusi
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <!-- Alert untuk error -->
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Terjadi kesalahan!</strong>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Form Edit Thread -->
                    <?= form_open('member/forum/update-thread/' . $thread['id']) ?>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">
                            <i class="fas fa-folder"></i> Kategori <span class="text-danger">*</span>
                        </label>
                        <select class="form-select <?= validation_show_error('category_id') ? 'is-invalid' : '' ?>"
                            id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= ($thread['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= esc($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= validation_show_error('category_id') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">
                            <i class="fas fa-heading"></i> Judul Diskusi <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            class="form-control <?= validation_show_error('title') ? 'is-invalid' : '' ?>"
                            id="title"
                            name="title"
                            value="<?= old('title', $thread['title']) ?>"
                            maxlength="255"
                            required>
                        <div class="invalid-feedback">
                            <?= validation_show_error('title') ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-bold">
                            <i class="fas fa-comment-dots"></i> Isi Diskusi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control <?= validation_show_error('content') ? 'is-invalid' : '' ?>"
                            id="content"
                            name="content"
                            rows="10"
                            required><?= old('content', $thread['content']) ?></textarea>
                        <div class="invalid-feedback">
                            <?= validation_show_error('content') ?>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Catatan:</strong> Edit terakhir akan tercatat dan ditampilkan di thread.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Thread</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Dibuat:</dt>
                        <dd class="col-sm-7"><?= date('d M Y H:i', strtotime($thread['created_at'])) ?></dd>

                        <dt class="col-sm-5">Terakhir Update:</dt>
                        <dd class="col-sm-7"><?= date('d M Y H:i', strtotime($thread['updated_at'])) ?></dd>

                        <dt class="col-sm-5">Views:</dt>
                        <dd class="col-sm-7"><?= number_format($thread['views']) ?></dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            <?php if ($thread['is_pinned']): ?>
                                <span class="badge bg-danger">Pinned</span>
                            <?php endif; ?>
                            <?php if ($thread['is_locked']): ?>
                                <span class="badge bg-warning">Locked</span>
                            <?php endif; ?>
                            <?php if (!$thread['is_pinned'] && !$thread['is_locked']): ?>
                                <span class="badge bg-success">Normal</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Auto-save draft
        var autoSaveTimer;
        $('#content, #title').on('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                console.log('Auto-saving draft...');
                // You can implement auto-save to localStorage here
            }, 2000);
        });
    });
</script>
<?= $this->endSection() ?>