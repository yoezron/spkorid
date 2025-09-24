<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Artikel
<?= $this->endSection() ?>

<?= $this->section('pageStyles') ?>
<link href="<?= base_url('neptune-assets/plugins/summernote/summernote-lite.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Edit Artikel</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?= form_open_multipart('member/posts/update/' . $post['id']) ?>
        <input type="hidden" name="_method" value="PUT">
        <div class="card">
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Artikel</label>
                            <input type="text" class="form-control form-control-lg <?= (validation_show_error('title')) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= old('title', $post['title'] ?? '') ?>" required>
                            <?php if (validation_show_error('title')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('title') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Konten Artikel</label>
                            <textarea id="summernote" name="content" class="<?= (validation_show_error('content')) ? 'is-invalid' : '' ?>"><?= old('content', $post['content'] ?? '') ?></textarea>
                            <?php if (validation_show_error('content')): ?>
                                <div class="invalid-feedback d-block"><?= validation_show_error('content') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select <?= (validation_show_error('category_id')) ? 'is-invalid' : '' ?>" id="category_id" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= (old('category_id', $post['category_id']) == $category['id']) ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (validation_show_error('category_id')): ?>
                                <div class="invalid-feedback"><?= validation_show_error('category_id') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="tags" name="tags" value="<?= old('tags', $post['tags'] ?? '') ?>">
                            <div class="form-text">Pisahkan dengan koma.</div>
                        </div>
                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Ganti Gambar Utama (Opsional)</label>
                            <input class="form-control <?= (session()->getFlashdata('error_image')) ? 'is-invalid' : '' ?>" type="file" id="featured_image" name="featured_image">
                            <?php if (session()->getFlashdata('error_image')): ?>
                                <div class="invalid-feedback d-block"><?= session()->getFlashdata('error_image') ?></div>
                            <?php endif; ?>
                            <?php if (!empty($post['featured_image'])): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url('uploads/featured_images/' . $post['featured_image']) ?>" alt="Current Image" class="img-thumbnail" width="200">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer text-end">
                <a href="<?= base_url('member/posts') ?>" class="btn btn-light">Batal</a>
                <button type="submit" name="status" value="<?= $post['status'] ?>" class="btn btn-secondary">Simpan Perubahan</button>
                <?php if ($post['status'] !== 'published'): ?>
                    <button type="submit" name="status" value="pending" class="btn btn-primary">Simpan & Kirim untuk Review</button>
                <?php endif; ?>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('neptune-assets/plugins/summernote/summernote-lite.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            height: 350,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
<?= $this->endSection() ?>