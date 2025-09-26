<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Buat Survei Baru
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/flatpickr/flatpickr.css') ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('plugins/noUiSlider/nouislider.min.css') ?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/forms/switches.css') ?>">
<style>
    .question-block {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        position: relative;
    }

    .question-block.dragging {
        opacity: 0.5;
    }

    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e6ed;
    }

    .question-number {
        background: #5c1ac3;
        color: white;
        padding: 5px 12px;
        border-radius: 4px;
        font-weight: bold;
    }

    .question-actions button {
        margin-left: 5px;
    }

    .options-container {
        margin-top: 15px;
    }

    .option-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .option-item input {
        flex: 1;
        margin: 0 10px;
    }

    .drag-handle {
        cursor: move;
        color: #888;
    }

    .question-type-selector {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }

    .type-option {
        padding: 15px;
        border: 2px solid #e0e6ed;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .type-option:hover {
        border-color: #5c1ac3;
        background: #f8f9fa;
    }

    .type-option.selected {
        border-color: #5c1ac3;
        background: #5c1ac320;
    }

    .type-option i {
        font-size: 24px;
        margin-bottom: 5px;
        display: block;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Buat Survei Baru</h4>
                    <a href="<?= base_url('admin/surveys') ?>" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Kembali
                    </a>
                </div>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="surveyForm" action="<?= base_url('admin/surveys/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Survey Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Survei</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Judul Survei <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="<?= old('title') ?>" required maxlength="255"
                                    placeholder="Masukkan judul survei yang menarik">
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description"
                                    rows="3" required><?= old('description') ?></textarea>
                                <small class="form-text text-muted">Jelaskan tujuan dan manfaat survei ini</small>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control flatpickr" id="start_date"
                                            name="start_date" value="<?= old('start_date') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">Tanggal Berakhir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control flatpickr" id="end_date"
                                            name="end_date" value="<?= old('end_date') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Survey Settings -->
                            <div class="form-row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="switch s-icons s-outline s-outline-primary">
                                            <input type="checkbox" name="is_anonymous" value="1" <?= old('is_anonymous') ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                        <label class="ml-2">Survei Anonim</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="switch s-icons s-outline s-outline-primary">
                                            <input type="checkbox" name="allow_multiple" value="1" <?= old('allow_multiple') ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                        <label class="ml-2">Izinkan Isi Berulang</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="switch s-icons s-outline s-outline-primary">
                                            <input type="checkbox" name="show_results" value="1" <?= old('show_results') ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                        <label class="ml-2">Tampilkan Hasil ke Peserta</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="switch s-icons s-outline s-outline-primary">
                                            <input type="checkbox" name="randomize" value="1" <?= old('randomize') ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                        <label class="ml-2">Acak Pertanyaan</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="switch s-icons s-outline s-outline-success">
                                    <input type="checkbox" name="notify_members" value="1" checked>
                                    <span class="slider"></span>
                                </label>
                                <label class="ml-2">Kirim notifikasi ke semua anggota</label>
                            </div>
                        </div>
                    </div>

                    <!-- Questions -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pertanyaan Survei</h5>
                            <button type="button" class="btn btn-primary btn-sm" id="addQuestion">
                                <i data-feather="plus"></i> Tambah Pertanyaan
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="questionsContainer">
                                <!-- Questions will be added here dynamically -->
                            </div>

                            <div id="noQuestionsMessage" class="text-center py-5 text-muted">
                                <i data-feather="help-circle" style="width: 48px; height: 48px;"></i>
                                <p class="mt-3">Belum ada pertanyaan. Klik tombol "Tambah Pertanyaan" untuk memulai.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-4 text-right">
                        <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                            <i data-feather="save"></i> Simpan Draft
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="send"></i> Buat Survei
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Question Template Modal -->
<div class="modal fade" id="questionTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tipe Pertanyaan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="question-type-selector">
                    <?php foreach ($question_types as $key => $type): ?>
                        <div class="type-option" data-type="<?= $key ?>">
                            <i data-feather="<?= $type['icon'] ?>"></i>
                            <div class="font-weight-bold"><?= $type['label'] ?></div>
                            <small><?= $type['description'] ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/flatpickr/flatpickr.js') ?>"></script>
<script src="<?= base_url('plugins/sweetalerts/sweetalert2.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    let questionIndex = 0;
    const questionsContainer = document.getElementById('questionsContainer');
    const noQuestionsMessage = document.getElementById('noQuestionsMessage');

    // Initialize flatpickr
    flatpickr('.flatpickr', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today"
    });

    // Initialize Sortable
    new Sortable(questionsContainer, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'dragging'
    });

    // Add question button
    document.getElementById('addQuestion').addEventListener('click', function() {
        $('#questionTypeModal').modal('show');
    });

    // Select question type
    document.querySelectorAll('.type-option').forEach(option => {
        option.addEventListener('click', function() {
            const type = this.dataset.type;
            $('#questionTypeModal').modal('hide');
            addQuestion(type);
        });
    });

    function addQuestion(type) {
        questionIndex++;
        noQuestionsMessage.style.display = 'none';

        const questionBlock = document.createElement('div');
        questionBlock.className = 'question-block';
        questionBlock.dataset.index = questionIndex;

        let optionsHtml = '';
        if (['radio', 'checkbox', 'dropdown'].includes(type)) {
            optionsHtml = `
            <div class="options-container">
                <label>Opsi Jawaban:</label>
                <div class="options-list" id="options-${questionIndex}">
                    <div class="option-item">
                        <i data-feather="circle"></i>
                        <input type="text" class="form-control" name="questions[${questionIndex}][options][]" placeholder="Opsi 1">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="option-item">
                        <i data-feather="circle"></i>
                        <input type="text" class="form-control" name="questions[${questionIndex}][options][]" placeholder="Opsi 2">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="addOption(${questionIndex})">
                    <i data-feather="plus"></i> Tambah Opsi
                </button>
            </div>
        `;
        }

        let constraintsHtml = '';
        if (['number', 'rating', 'scale'].includes(type)) {
            constraintsHtml = `
            <div class="form-row mt-3">
                <div class="col-md-6">
                    <label>Nilai Minimum</label>
                    <input type="number" class="form-control" name="questions[${questionIndex}][min_value]" placeholder="0">
                </div>
                <div class="col-md-6">
                    <label>Nilai Maksimum</label>
                    <input type="number" class="form-control" name="questions[${questionIndex}][max_value]" placeholder="100">
                </div>
            </div>
        `;
        }

        if (['text', 'textarea'].includes(type)) {
            constraintsHtml = `
            <div class="form-row mt-3">
                <div class="col-md-6">
                    <label>Minimal Karakter</label>
                    <input type="number" class="form-control" name="questions[${questionIndex}][min_length]" placeholder="0">
                </div>
                <div class="col-md-6">
                    <label>Maksimal Karakter</label>
                    <input type="number" class="form-control" name="questions[${questionIndex}][max_length]" placeholder="500">
                </div>
            </div>
        `;
        }

        questionBlock.innerHTML = `
        <div class="question-header">
            <div class="d-flex align-items-center">
                <i data-feather="move" class="drag-handle mr-2"></i>
                <span class="question-number">Pertanyaan ${questionIndex}</span>
            </div>
            <div class="question-actions">
                <button type="button" class="btn btn-info btn-sm" onclick="duplicateQuestion(${questionIndex})">
                    <i data-feather="copy"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(${questionIndex})">
                    <i data-feather="trash-2"></i>
                </button>
            </div>
        </div>
        <input type="hidden" name="questions[${questionIndex}][type]" value="${type}">
        <div class="form-group">
            <label>Teks Pertanyaan <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="questions[${questionIndex}][text]" required>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Placeholder</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][placeholder]" placeholder="Teks bantuan">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Help Text</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][help_text]" placeholder="Petunjuk pengisian">
                </div>
            </div>
        </div>
        ${constraintsHtml}
        ${optionsHtml}
        <div class="form-group mt-3">
            <label class="switch s-icons s-outline s-outline-danger">
                <input type="checkbox" name="questions[${questionIndex}][required]" value="1" checked>
                <span class="slider"></span>
            </label>
            <label class="ml-2">Wajib diisi</label>
        </div>
    `;

        questionsContainer.appendChild(questionBlock);
        feather.replace();
        updateQuestionNumbers();
    }

    function removeQuestion(index) {
        Swal.fire({
            title: 'Hapus Pertanyaan?',
            text: 'Pertanyaan ini akan dihapus dari survei.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector(`[data-index="${index}"]`).remove();
                updateQuestionNumbers();

                if (questionsContainer.children.length === 0) {
                    noQuestionsMessage.style.display = 'block';
                }
            }
        });
    }

    function duplicateQuestion(index) {
        const original = document.querySelector(`[data-index="${index}"]`);
        const clone = original.cloneNode(true);
        questionIndex++;
        clone.dataset.index = questionIndex;

        // Update all name attributes
        clone.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${questionIndex}]`);
        });

        questionsContainer.appendChild(clone);
        feather.replace();
        updateQuestionNumbers();
    }

    function addOption(questionId) {
        const optionsList = document.getElementById(`options-${questionId}`);
        const optionCount = optionsList.children.length + 1;

        const optionDiv = document.createElement('div');
        optionDiv.className = 'option-item';
        optionDiv.innerHTML = `
        <i data-feather="circle"></i>
        <input type="text" class="form-control" name="questions[${questionId}][options][]" placeholder="Opsi ${optionCount}">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
            <i data-feather="x"></i>
        </button>
    `;

        optionsList.appendChild(optionDiv);
        feather.replace();
    }

    function removeOption(button) {
        const optionItem = button.closest('.option-item');
        const optionsList = optionItem.parentElement;

        if (optionsList.children.length > 2) {
            optionItem.remove();
        } else {
            Swal.fire('Peringatan', 'Minimal harus ada 2 opsi', 'warning');
        }
    }

    function updateQuestionNumbers() {
        document.querySelectorAll('.question-block').forEach((block, index) => {
            const numberSpan = block.querySelector('.question-number');
            numberSpan.textContent = `Pertanyaan ${index + 1}`;
        });
    }

    function saveDraft() {
        // Implementation for saving as draft (set is_active = 0)
        const form = document.getElementById('surveyForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'save_as_draft';
        input.value = '1';
        form.appendChild(input);
        form.submit();
    }

    // Form validation
    document.getElementById('surveyForm').addEventListener('submit', function(e) {
        const questions = questionsContainer.querySelectorAll('.question-block');

        if (questions.length === 0) {
            e.preventDefault();
            Swal.fire('Error', 'Survei harus memiliki minimal 1 pertanyaan', 'error');
            return false;
        }

        // Validate date range
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (new Date(startDate) >= new Date(endDate)) {
            e.preventDefault();
            Swal.fire('Error', 'Tanggal berakhir harus setelah tanggal mulai', 'error');
            return false;
        }
    });

    // Initialize feather icons
    feather.replace();
</script>
<?= $this->endSection() ?>