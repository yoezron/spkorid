<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tulis Artikel Baru
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/editors/quill/quill.snow.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('plugins/file-upload/file-upload-with-preview.min.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Formulir Artikel Baru</h4>
                <p>Bagikan pemikiran dan wawasan Anda dengan anggota lainnya.</p>
                <hr>

                <form action="<?= base_url('member/posts/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="title">Judul Artikel</label>
                        <input type="text" class="form-control" name="title" id="title" value="<?= old('title') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="content">Isi Artikel</label>
                        <div id="editor-container" style="min-height: 250px;"></div>
                        <input type="hidden" name="content" id="content-input">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Kategori</label>
                                <select class="form-control" name="category" id="category">
                                    <option value="Opini">Opini</option>
                                    <option value="Berita Kampus">Berita Kampus</option>
                                    <option value="Advokasi">Advokasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="excerpt">Kutipan Singkat (Excerpt)</label>
                                <textarea class="form-control" name="excerpt" id="excerpt" rows="3"><?= old('excerpt') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gambar Utama (Featured Image)</label>
                                <div class="custom-file-container" data-upload-id="featuredImage">
                                    <label>Pilih Gambar <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                                    <label class="custom-file-container__custom-file">
                                        <input type="file" name="featured_image" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                    </label>
                                    <div class="custom-file-container__image-preview"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="status" value="draft" class="btn btn-secondary">Simpan sebagai Draft</button>
                        <button type="submit" name="status" value="pending_review" class="btn btn-primary">Kirim untuk Direview</button>
                        <a href="<?= base_url('member/posts') ?>" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/editors/quill/quill.js') ?>"></script>
<script src="<?= base_url('plugins/file-upload/file-upload-with-preview.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Quill Editor
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'blockquote', 'code-block'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['clean']
                ]
            }
        });

        // Inisialisasi Upload Gambar
        new FileUploadWithPreview('featuredImage');

        // Sinkronkan konten Quill ke input tersembunyi sebelum form disubmit
        var form = document.querySelector('form');
        form.onsubmit = function() {
            var contentInput = document.querySelector('#content-input');
            contentInput.value = quill.root.innerHTML;
        };
    });
</script>
<?= $this->endSection() ?>