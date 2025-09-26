<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
            <li class="breadcrumb-item active">Buat Diskusi Baru</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-plus-circle"></i> Buat Diskusi Baru
        </h1>
        <p class="text-muted">Mulai diskusi baru dengan anggota serikat lainnya</p>
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

                    <!-- Form Create Thread -->
                    <?= form_open('member/forum/store') ?>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">
                            <i class="fas fa-folder"></i> Kategori <span class="text-danger">*</span>
                        </label>
                        <select class="form-select <?= validation_show_error('category_id') ? 'is-invalid' : '' ?>"
                            id="category_id" name="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            $request = \Config\Services::request();
                            $selectedCategory = old('category_id') ?? $request->getGet('category');
                            ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= ($selectedCategory == $cat['id']) ? 'selected' : '' ?>>
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
                            value="<?= old('title') ?>"
                            placeholder="Tulis judul yang jelas dan deskriptif..."
                            maxlength="255"
                            required>
                        <div class="invalid-feedback">
                            <?= validation_show_error('title') ?>
                        </div>
                        <small class="form-text text-muted">
                            Minimal 5 karakter, maksimal 255 karakter
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-bold">
                            <i class="fas fa-comment-dots"></i> Isi Diskusi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control <?= validation_show_error('content') ? 'is-invalid' : '' ?>"
                            id="content"
                            name="content"
                            rows="10"
                            placeholder="Jelaskan pertanyaan atau topik diskusi Anda secara rinci..."
                            required><?= old('content') ?></textarea>
                        <div class="invalid-feedback">
                            <?= validation_show_error('content') ?>
                        </div>
                        <small class="form-text text-muted">
                            Minimal 10 karakter. Gunakan bahasa yang sopan dan jelas.
                        </small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('member/forum') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Publikasikan Diskusi
                        </button>
                    </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>

        <!-- Sidebar with Tips -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Tips Membuat Diskusi</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li class="mb-2">Gunakan judul yang jelas dan spesifik</li>
                        <li class="mb-2">Pilih kategori yang sesuai dengan topik</li>
                        <li class="mb-2">Jelaskan pertanyaan atau masalah dengan detail</li>
                        <li class="mb-2">Sertakan konteks yang relevan</li>
                        <li class="mb-2">Gunakan bahasa yang sopan dan profesional</li>
                        <li>Hindari posting duplikat, cek dulu apakah sudah ada diskusi serupa</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Aturan Forum</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li class="mb-2">Dilarang spam atau promosi</li>
                        <li class="mb-2">Tidak boleh menyebarkan informasi palsu</li>
                        <li class="mb-2">Hormati pendapat anggota lain</li>
                        <li class="mb-2">Topik harus relevan dengan serikat</li>
                        <li>Pelanggaran dapat berakibat pembatasan akses</li>
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
        // Character counter for title
        $('#title').on('input', function() {
            var length = $(this).val().length;
            var maxLength = 255;
            if (length > 200) {
                $(this).next('.invalid-feedback').after('<small class="text-warning">Karakter: ' + length + '/' + maxLength + '</small>');
            }
        });

        // Auto-save draft (optional)
        var autoSaveTimer;
        $('#content, #title').on('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // Save to localStorage
                localStorage.setItem('forum_draft_title', $('#title').val());
                localStorage.setItem('forum_draft_content', $('#content').val());
                localStorage.setItem('forum_draft_category', $('#category_id').val());
            }, 1000);
        });

        // Load draft if exists
        if (localStorage.getItem('forum_draft_title') && !$('#title').val()) {
            if (confirm('Anda memiliki draft yang tersimpan. Muat draft?')) {
                $('#title').val(localStorage.getItem('forum_draft_title'));
                $('#content').val(localStorage.getItem('forum_draft_content'));
                $('#category_id').val(localStorage.getItem('forum_draft_category'));
            }
        }

        // Clear draft on submit
        $('form').on('submit', function() {
            localStorage.removeItem('forum_draft_title');
            localStorage.removeItem('forum_draft_content');
            localStorage.removeItem('forum_draft_category');
        });
    });
</script>
<?= $this->endSection() ?>