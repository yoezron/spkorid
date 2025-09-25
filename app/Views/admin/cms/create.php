<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Halaman Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Tambah Halaman Baru</h1>
            <span>Buat halaman konten baru untuk website</span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Form Halaman</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('admin/content/store') ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="page_title" class="form-label">Judul Halaman <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= validation_show_error('page_title') ? 'is-invalid' : '' ?>"
                                id="page_title" name="page_title" value="<?= old('page_title') ?>" required>
                            <?php if (validation_show_error('page_title')): ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('page_title') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="page_slug" class="form-label">URL Slug (opsional)</label>
                            <input type="text" class="form-control" id="page_slug" name="page_slug"
                                value="<?= old('page_slug') ?>" placeholder="otomatis-dari-judul">
                            <small class="text-muted">Kosongkan untuk generate otomatis dari judul</small>
                        </div>

                        <div class="mb-3">
                            <label for="page_content" class="form-label">Konten Halaman <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= validation_show_error('page_content') ? 'is-invalid' : '' ?>"
                                id="page_content" name="page_content" rows="12" required><?= old('page_content') ?></textarea>
                            <?php if (validation_show_error('page_content')): ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('page_content') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">SEO Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title"
                                        value="<?= old('meta_title') ?>" maxlength="255">
                                    <small class="text-muted">Untuk SEO, kosongkan jika sama dengan judul</small>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description"
                                        rows="3" maxlength="500"><?= old('meta_description') ?></textarea>
                                    <small class="text-muted">Deskripsi untuk mesin pencari (maks 160 karakter ideal)</small>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                        value="<?= old('meta_keywords') ?>" placeholder="kata kunci, dipisah koma">
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title">Status Publikasi</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_published"
                                        name="is_published" value="1" <?= old('is_published') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_published">
                                        Publish langsung
                                    </label>
                                </div>
                                <small class="text-muted">Jika tidak dicentang, akan disimpan sebagai draft</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">save</i> Simpan Halaman
                    </button>
                    <a href="<?= base_url('admin/content') ?>" class="btn btn-secondary">
                        <i class="material-icons">arrow_back</i> Batal
                    </a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<!-- Optional: Add text editor like TinyMCE or CKEditor -->
<script>
    // Auto generate slug from title
    document.getElementById('page_title').addEventListener('keyup', function() {
        if (!document.getElementById('page_slug').value) {
            var slug = this.value.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            document.getElementById('page_slug').value = slug;
        }
    });
</script>

<?= $this->endSection() ?>