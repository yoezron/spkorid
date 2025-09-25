<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?? 'Buat Diskusi Baru' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1><?= $title ?? 'Buat Diskusi Baru' ?></h1>
            <span>Mulai percakapan baru dengan anggota lain di forum diskusi.</span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open('member/forum/store') ?>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Diskusi</label>
                    <input type="text" class="form-control <?= (validation_show_error('judul')) ? 'is-invalid' : '' ?>" id="judul" name="judul" value="<?= old('judul') ?>" placeholder="Tulis judul yang jelas dan ringkas" required>
                    <div class="invalid-feedback">
                        <?= validation_show_error('judul') ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Pilih Kategori</label>
                    <select class="form-select <?= (validation_show_error('category_id')) ? 'is-invalid' : '' ?>" id="category_id" name="category_id" required>
                        <option value="" disabled selected>-- Silakan Pilih Kategori --</option>
                        <?php if (!empty($categories)) : ?>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('category_id') ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="isi" class="form-label">Isi Diskusi</label>
                    <textarea class="form-control <?= (validation_show_error('isi')) ? 'is-invalid' : '' ?>" id="isi" name="isi" rows="10" placeholder="Jelaskan pertanyaan atau topik diskusi Anda secara rinci di sini..." required><?= old('isi') ?></textarea>
                    <div class="invalid-feedback">
                        <?= validation_show_error('isi') ?>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="<?= base_url('member/forum') ?>" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Publikasikan Diskusi</button>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>