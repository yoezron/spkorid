<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Buat Thread Baru
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/editors/quill/quill.snow.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('plugins/select2/select2.min.css') ?>" rel="stylesheet" type="text/css" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Mulai Diskusi Baru</h4>
                <p>Pilih kategori yang sesuai, tulis judul yang jelas, dan mulailah diskusi Anda.</p>
                <hr>

                <form action="<?= base_url('member/forum/store-thread') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="title">Judul Thread</label>
                        <input type="text" class="form-control" name="title" id="title" value="<?= old('title') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Kategori</label>
                        <select class="form-control select2" name="category_id" id="category_id" required>
                            <option value="">Pilih Kategori</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="content">Isi Diskusi</label>
                        <div id="editor-container" style="min-height: 200px;"></div>
                        <input type="hidden" name="content" id="content-input">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Publikasikan Thread</button>
                        <a href="<?= base_url('member/forum') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/editors/quill/quill.js') ?>"></script>
<script src="<?= base_url('plugins/select2/select2.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2();

        // Inisialisasi Quill Editor
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, false]
                    }],
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

        // Sinkronkan konten Quill ke input tersembunyi
        var form = document.querySelector('form');
        form.onsubmit = function() {
            var contentInput = document.querySelector('#content-input');
            contentInput.value = quill.root.innerHTML;
        };
    });
</script>
<?= $this->endSection() ?>