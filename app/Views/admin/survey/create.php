<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Buat Survei Baru
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
                <h4>Formulir Survei Baru</h4>
                <p>Isi detail survei dan tambahkan pertanyaan yang dibutuhkan.</p>
                <hr>

                <form action="<?= base_url('admin/surveys/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <h5>Detail Survei</h5>
                    <div class="form-group">
                        <label for="title">Judul Survei</label>
                        <input type="text" class="form-control" name="title" id="title" value="<?= old('title') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" name="description" id="description" rows="3" required><?= old('description') ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_date">Tanggal Mulai</label>
                            <input id="start_date" name="start_date" class="form-control flatpickr" type="text" placeholder="Pilih tanggal.." value="<?= old('start_date') ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date">Tanggal Selesai</label>
                            <input id="end_date" name="end_date" class="form-control flatpickr" type="text" placeholder="Pilih tanggal.." value="<?= old('end_date') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Opsi Survei</label>
                        <div>
                            <label class="switch s-icons s-outline s-outline-primary">
                                <input type="checkbox" name="is_anonymous" value="1" <?= old('is_anonymous') ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                            <span class="ml-2">Survei Anonim (Responden tidak akan teridentifikasi)</span>
                        </div>
                    </div>

                    <hr>
                    <h5>Pertanyaan Survei</h5>
                    <div id="questions-container">
                    </div>

                    <button type="button" id="add-question" class="btn btn-info mt-3"><i data-feather="plus"></i> Tambah Pertanyaan</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Simpan dan Publikasikan Survei</button>
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
        // Inisialisasi Date Picker
        flatpickr(".flatpickr");

        let questionIndex = 0;

        // Fungsi untuk menambah pertanyaan baru
        $('#add-question').click(function() {
            const questionHtml = `
                    <div class="question-block" data-index="${questionIndex}">
                        <div class="question-header">
                            <h5>Pertanyaan #${questionIndex + 1}</h5>
                            <button type="button" class="btn btn-danger btn-sm remove-question">
                                <i data-feather="trash-2"></i>
                            </button>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="question_text_${questionIndex}">Teks Pertanyaan</label>
                                <input type="text" name="questions[${questionIndex}][text]" id="question_text_${questionIndex}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="question_type_${questionIndex}">Tipe Pertanyaan</label>
                                <select name="questions[${questionIndex}][type]" id="question_type_${questionIndex}" class="form-control question-type-select">
                                    <option value="text">Teks Singkat</option>
                                    <option value="textarea">Paragraf</option>
                                    <option value="number">Angka</option>
                                    <option value="radio">Pilihan Ganda</option>
                                    <option value="checkbox">Kotak Centang</option>
                                    <option value="dropdown">Dropdown</option>
                                    <option value="date">Tanggal</option>
                                    <option value="rating">Rating (1-5)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                             <label class="switch s-icons s-outline s-outline-primary">
                                <input type="checkbox" name="questions[${questionIndex}][required]" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                            <span class="ml-2">Wajib diisi</span>
                        </div>
                        <div class="options-container" id="options_container_${questionIndex}" style="display: none;">
                            <label>Opsi Jawaban</label>
                            <button type="button" class="btn btn-info btn-sm mb-2 add-option">Tambah Opsi</button>
                        </div>
                    </div>
                `;
            $('#questions-container').append(questionHtml);
            feather.replace(); // Re-initialize feather icons
            questionIndex++;
        });

        // Tampilkan/sembunyikan kontainer opsi berdasarkan tipe pertanyaan
        $('#questions-container').on('change', '.question-type-select', function() {
            const selectedType = $(this).val();
            const container = $(this).closest('.question-block').find('.options-container');
            if (['radio', 'checkbox', 'dropdown'].includes(selectedType)) {
                container.show();
                // Tambahkan minimal satu opsi jika belum ada
                if (container.find('.input-group').length === 0) {
                    container.find('.add-option').click();
                }
            } else {
                container.hide();
                container.find('.input-group').remove(); // Hapus opsi jika tipe diubah
            }
        });

        // Fungsi untuk menambah opsi jawaban
        $('#questions-container').on('click', '.add-option', function() {
            const block = $(this).closest('.question-block');
            const index = block.data('index');
            const optionHtml = `
                    <div class="input-group">
                        <input type="text" name="questions[${index}][options][]" class="form-control" placeholder="Tulis opsi jawaban di sini">
                        <div class="input-group-append">
                            <button class="btn btn-outline-danger remove-option" type="button">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                    </div>
                `;
            $(this).before(optionHtml);
            feather.replace();
        });

        // Hapus pertanyaan
        $('#questions-container').on('click', '.remove-question', function() {
            $(this).closest('.question-block').remove();
        });

        // Hapus opsi
        $('#questions-container').on('click', '.remove-option', function() {
            $(this).closest('.input-group').remove();
        });

        // Tambah satu pertanyaan saat halaman dimuat
        $('#add-question').click();

    });
</script>
<?= $this->endSection() ?>