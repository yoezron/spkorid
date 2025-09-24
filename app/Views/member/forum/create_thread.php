<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Buat Thread Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('member/forum') ?>">Forum</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buat Thread Baru</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Buat Diskusi Baru</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Diskusi</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('member/forum/store-thread') ?>

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Diskusi</label>
                    <input type="text" class="form-control <?= (validation_show_error('title')) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= old('title') ?>" placeholder="Apa yang ingin Anda diskusikan?" required>
                    <?php if (validation_show_error('title')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('title') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Pilih Kategori</label>
                    <select class="form-select <?= (validation_show_error('category_id')) ? 'is-invalid' : '' ?>" id="category_id" name="category_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= (old('category_id') == $category['id']) ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (validation_show_error('category_id')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('category_id') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Isi Diskusi</label>
                    <textarea class="form-control <?= (validation_show_error('content')) ? 'is-invalid' : '' ?>" id="content" name="content" rows="8" placeholder="Tuliskan detail pertanyaan atau topik diskusi Anda di sini." required><?= old('content') ?></textarea>
                    <?php if (validation_show_error('content')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('content') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Publikasikan Thread</button>
                    <a href="<?= base_url('member/forum') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>