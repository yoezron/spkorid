<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Survei: <?= esc($survey['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/flatpickr/flatpickr.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('assets/css/forms/switches.css') ?>" rel="stylesheet" type="text/css">
<style>
    .question-block {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
    }

    .question-block .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .options-container .input-group {
        margin-bottom: 10px;
    }

    .remove-question,
    .remove-option {
        cursor: pointer;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Formulir Edit Survei</h4>
                <p>Ubah detail survei dan pertanyaannya di bawah ini.</p>
                <hr>

                <form action="<?= base_url('admin/surveys/update/' . $survey['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <h5>Detail Survei</h5>
                    <div class="form-group">
                        <label for="title">Judul Survei</label>
                        <input type="text" class="form-control" name="title" id="title" value="<?= old('title', $survey['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" name="description" id="description" rows="3" required><?= old('description', $survey['description']) ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_date">Tanggal Mulai</label>
                            <input id="start_date" name="start_date" class="form-control flatpickr" type="text" value="<?= old('start_date', $survey['start_date']) ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date">Tanggal Selesai</label>
                            <input id="end_date" name="end_date" class="form-control flatpickr" type="text" value="<?= old('end_date', $survey['end_date']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Opsi Survei</label>
                        <div>
                            <label class="switch s-icons s-outline s-outline-primary">
                                <input type="checkbox" name="is_anonymous" value="1" <?= old('is_anonymous', $survey['is_anonymous']) ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                            <span class="ml-2">Survei Anonim</span>
                        </div>
                    </div>

                    <hr>
                    <h5>Pertanyaan Survei</h5>
                    <div id="questions-container">
                        <?php if (!empty($survey['questions'])): ?>
                            <?php foreach ($survey['questions'] as $index => $question): ?>
                                <div class="question-block" data-index="<?= $index ?>">
                                    <div class="question-header">
                                        <h5>Pertanyaan #<?= $index + 1 ?></h5>
                                        <button type="button" class="btn btn-danger btn-sm remove-question"><i data-feather="trash-2"></i></button>
                                    </div>
                                    <input type="hidden" name="questions[<?= $index ?>][id]" value="<?= $question['id'] ?>">
                                    <div class="form-row">
                                        <div class="form-group col-md-8">
                                            <label>Teks Pertanyaan</label>
                                            <input type="text" name="questions[<?= $index ?>][text]" class="form-control" value="<?= esc($question['question_text']) ?>" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Tipe Pertanyaan</label>
                                            <select name="questions[<?= $index ?>][type]" class="form-control question-type-select">
                                                <option value="text" <?= $question['question_type'] == 'text' ? 'selected' : '' ?>>Teks Singkat</option>
                                                <option value="textarea" <?= $question['question_type'] == 'textarea' ? 'selected' : '' ?>>Paragraf</option>
                                                <option value="number" <?= $question['question_type'] == 'number' ? 'selected' : '' ?>>Angka</option>
                                                <option value="radio" <?= $question['question_type'] == 'radio' ? 'selected' : '' ?>>Pilihan Ganda</option>
                                                <option value="checkbox" <?= $question['question_type'] == 'checkbox' ? 'selected' : '' ?>>Kotak Centang</option>
                                                <option value="dropdown" <?= $question['question_type'] == 'dropdown' ? 'selected' : '' ?>>Dropdown</option>
                                                <option value="date" <?= $question['question_type'] == 'date' ? 'selected' : '' ?>>Tanggal</option>
                                                <option value="rating" <?= $question['question_type'] == 'rating' ? 'selected' : '' ?>>Rating (1-5)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="switch s-icons s-outline s-outline-primary">
                                            <input type="checkbox" name="questions[<?= $index ?>][required]" value="1" <?= $question['is_required'] ? 'checked' : '' ?>>
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="ml-2">Wajib diisi</span>
                                    </div>
                                    <div class="options-container" style="<?= in_array($question['question_type'], ['radio', 'checkbox', 'dropdown']) ? '' : 'display: none;' ?>">
                                        <label>Opsi Jawaban</label>
                                        <?php if (!empty($question['options'])): ?>
                                            <?php foreach (json_decode($question['options']) as $option): ?>
                                                <div class="input-group">
                                                    <input type="text" name="questions[<?= $index ?>][options][]" class="form-control" value="<?= esc($option) ?>">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-danger remove-option" type="button"><i data-feather="x"></i></button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-info btn-sm mt-2 add-option">Tambah Opsi</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <button type="button" id="add-question" class="btn btn-info mt-3"><i data-feather="plus"></i> Tambah Pertanyaan</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="<?= base_url('admin/surveys') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/flatpickr/flatpickr.js') ?>"></script>
<script>
    $(document).ready(function() {
        flatpickr(".flatpickr");

        // Mengambil index terakhir dari pertanyaan yang sudah ada
        let questionIndex = $('#questions-container .question-block').length;

        // Logika JavaScript lainnya (add-question, change type, add-option, remove)
        // sama persis seperti di file create.php
        $('#add-question').click(function() {
            // ... (salin kode JS dari create.php)
        });
        // ... (salin sisa kode JS dari create.php)
    });
</script>
<?= $this->endSection() ?>