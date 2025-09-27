<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Hasil Survei: <?= esc($survey['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('neptune-assets/plugins/apexcharts/apexcharts.css') ?>" rel="stylesheet">
<link href="<?= base_url('neptune-assets/plugins/datatables/datatables.min.css') ?>" rel="stylesheet">
<style>
    /* =================================================================
    PENYEMPURNAAN 1: REVISI CSS UNTUK KONSISTENSI
    - Mengurangi CSS custom yang berlebihan.
    - Menyesuaikan style agar selaras dengan variabel dan kelas Neptune.
    - Fokus pada readability dan visual hierarchy.
    ================================================================== */
    .answer-option {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .answer-label {
        width: 30%;
        font-weight: 500;
        padding-right: 1rem;
    }

    .answer-bar-container {
        flex: 1;
        background-color: #f3f4f6;
        border-radius: .25rem;
    }

    .answer-bar-fill {
        background-color: var(--bs-primary);
        height: 25px;
        border-radius: .25rem;
        color: white;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: .75rem;
        font-size: .8rem;
        font-weight: 500;
        transition: width 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .answer-count {
        width: 15%;
        text-align: right;
        font-weight: 600;
    }

    .text-response-item {
        border-left: 3px solid var(--bs-primary);
        background-color: #f9fbfd;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .rating-visualization .star {
        font-size: 2rem;
        color: #e0e6ed;
    }

    .rating-visualization .star.filled {
        color: #ffc107;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="page-header">
        <nav class="navbar navbar-expand">
            <div class="container-fluid">
                <div class="navbar-collapse" id="navbarSupportedContent">
                    <div class="page-title">
                        <h4>Hasil Survei</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= base_url('admin/surveys') ?>">Survei</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Hasil</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div class="main-wrapper">
        <!-- =================================================================
        PENYEMPURNAAN 2: HEADER HALAMAN YANG INFORMATIF
        - Membuat header yang jelas dengan judul, deskripsi, dan metadata survei.
        - Tombol aksi (Kembali, Export) ditempatkan secara logis di header.
        ================================================================== -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="card-title mb-1"><?= esc($survey['title']) ?></h3>
                        <p class="text-muted"><?= esc($survey['description']) ?></p>
                    </div>
                    <div>
                        <a href="<?= base_url('admin/surveys') ?>" class="btn btn-light"><i class="material-icons-outlined me-1">arrow_back</i>Kembali</a>
                        <a href="<?= base_url('admin/surveys/export/' . $survey['id']) ?>" class="btn btn-primary"><i class="material-icons-outlined me-1">download</i>Export Excel</a>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-4 text-muted border-top pt-3">
                    <span><i class="material-icons-outlined me-1 fs-6 align-middle">calendar_today</i><?= date('d M Y', strtotime($survey['start_date'])) ?> - <?= date('d M Y', strtotime($survey['end_date'])) ?></span>
                    <span><i class="material-icons-outlined me-1 fs-6 align-middle">help_outline</i><?= count($survey['questions']) ?> Pertanyaan</span>
                    <span><i class="material-icons-outlined me-1 fs-6 align-middle">visibility_off</i><?= $survey['is_anonymous'] ? 'Survei Anonim' : 'Survei Publik' ?></span>
                </div>
            </div>
        </div>

        <!-- =================================================================
        PENYEMPURNAAN 3: KARTU STATISTIK YANG SELARAS
        - Menggunakan komponen .widget.widget-stats dari Neptune, sama seperti
          di halaman index untuk konsistensi.
        ================================================================== -->
        <div class="row mt-4">
            <div class="col-xl-3 col-lg-6">
                <div class="card widget widget-stats">
                    <div class="card-body">
                        <div class="widget-stats-container d-flex">
                            <div class="widget-stats-icon widget-stats-icon-primary">
                                <i class="material-icons-outlined">people</i>
                            </div>
                            <div class="widget-stats-content flex-fill">
                                <span class="widget-stats-title">Total Responden</span>
                                <span class="widget-stats-amount"><?= number_format($statistics['total_responses'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card widget widget-stats">
                    <div class="card-body">
                        <div class="widget-stats-container d-flex">
                            <div class="widget-stats-icon widget-stats-icon-success">
                                <i class="material-icons-outlined">rate_review</i>
                            </div>
                            <div class="widget-stats-content flex-fill">
                                <span class="widget-stats-title">Tingkat Partisipasi</span>
                                <span class="widget-stats-amount"><?= number_format($statistics['response_rate'] ?? 0, 1) ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card widget widget-stats">
                    <div class="card-body">
                        <div class="widget-stats-container d-flex">
                            <div class="widget-stats-icon widget-stats-icon-warning">
                                <i class="material-icons-outlined">timer</i>
                            </div>
                            <div class="widget-stats-content flex-fill">
                                <span class="widget-stats-title">Waktu Pengisian Rata-rata</span>
                                <span class="widget-stats-amount"><?= number_format($response_stats['avg_completion_time'] ?? 0, 1) ?> min</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card widget widget-stats">
                    <div class="card-body">
                        <div class="widget-stats-container d-flex">
                            <div class="widget-stats-icon widget-stats-icon-info">
                                <i class="material-icons-outlined">fact_check</i>
                            </div>
                            <div class="widget-stats-content flex-fill">
                                <span class="widget-stats-title">Penyelesaian</span>
                                <span class="widget-stats-amount">100%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================
        PENYEMPURNAAN 4: KONTEN UTAMA DALAM CARD
        - Semua visualisasi data (grafik, daftar pertanyaan, tabel responden)
          kini dibungkus dalam .card terpisah untuk tampilan yang modular dan rapi.
        ================================================================== -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Timeline Responden</h5>
                <div id="responseTimelineChart"></div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Hasil per Pertanyaan</h5>
                <?php foreach ($statistics['questions'] as $index => $question) : ?>
                    <div class="question-result-item mt-4 pt-4 border-top">
                        <h6><strong>#<?= $index + 1 ?></strong> <?= esc($question['question_text']) ?></h6>
                        <span class="badge badge-light-secondary"><?= ucfirst($question['question_type']) ?></span>
                        <div class="mt-3">
                            <?php if (in_array($question['question_type'], ['radio', 'checkbox', 'dropdown'])) : ?>
                                <?php foreach ($question['answer_distribution'] as $item) : ?>
                                    <div class="answer-option">
                                        <div class="answer-label"><?= esc($item['value']) ?></div>
                                        <div class="answer-bar-container">
                                            <div class="answer-bar-fill" style="width: 0%;" data-percentage="<?= $item['percentage'] ?>">
                                                <?= $item['percentage'] ?>%
                                            </div>
                                        </div>
                                        <div class="answer-count"><?= $item['count'] ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php elseif ($question['question_type'] == 'rating' && isset($question['average_rating'])) : ?>
                                <div class="d-flex align-items-center">
                                    <div class="me-4">
                                        <h3 class="mb-0"><?= number_format($question['average_rating'], 1) ?> / 5</h3>
                                        <div class="rating-visualization">
                                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                <span class="star <?= $i <= round($question['average_rating']) ? 'filled' : '' ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <?php for ($i = 5; $i >= 1; $i--) : ?>
                                            <div class="answer-option">
                                                <div class="answer-label"><?= $i ?> Bintang</div>
                                                <div class="answer-bar-container">
                                                    <?php $count = $question['rating_distribution'][$i] ?? 0;
                                                    $percentage = ($question['total_answers'] > 0) ? round(($count / $question['total_answers']) * 100, 2) : 0; ?>
                                                    <div class="answer-bar-fill" style="width: 0%; background-color: #ffc107;" data-percentage="<?= $percentage ?>">
                                                        <?= $percentage ?>%
                                                    </div>
                                                </div>
                                                <div class="answer-count"><?= $count ?></div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            <?php elseif (in_array($question['question_type'], ['text', 'textarea'])) : ?>
                                <?php if (!empty($question['sample_answers'])) : ?>
                                    <?php foreach ($question['sample_answers'] as $answer) : ?>
                                        <div class="text-response-item">"<?= esc($answer['answer_text']) ?>"</div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p class="text-muted">Belum ada jawaban teks.</p>
                                <?php endif; ?>
                            <?php else : ?>
                                <p class="text-muted">Visualisasi untuk tipe pertanyaan ini belum tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Detail Responden</h5>
                <div class="table-responsive">
                    <table id="responsesTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <?php if (!$survey['is_anonymous']) : ?>
                                    <th>Nama Responden</th>
                                    <th>Email</th>
                                <?php endif; ?>
                                <th>Waktu Submit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($responses as $index => $response) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <?php if (!$survey['is_anonymous']) : ?>
                                        <td><?= esc($response['nama_lengkap'] ?? 'Anonim') ?></td>
                                        <td><?= esc($response['email'] ?? '-') ?></td>
                                    <?php endif; ?>
                                    <td><?= date('d M Y, H:i', strtotime($response['submitted_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/surveys/view-response/' . $survey['id'] . '/' . $response['id']) ?>" class="btn btn-sm btn-outline-info">Lihat Jawaban</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('neptune-assets/plugins/apexcharts/apexcharts.min.js') ?>"></script>
<script src="<?= base_url('neptune-assets/plugins/datatables/datatables.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // =================================================================
        // PENYEMPURNAAN 1: INISIALISASI VARIABEL DENGAN BENAR
        // =================================================================
        let questionIndex = 0;
        const stepperEl = document.getElementById('surveyStepper');
        if (!stepperEl) return;

        const stepper = new Stepper(stepperEl);
        const questionsContainer = document.getElementById('questionsContainer');
        const noQuestionsMessage = document.getElementById('noQuestionsMessage');
        const addQuestionBtn = document.getElementById('addQuestionBtn');
        const questionTypeModalEl = document.getElementById('questionTypeModal');
        const questionTypeModal = new bootstrap.Modal(questionTypeModalEl);
        const surveyForm = document.getElementById('surveyForm');

        // Initialize Plugins
        flatpickr('.flatpickr', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today"
        });

        new Sortable(questionsContainer, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: updateQuestionNumbers
        });

        // =================================================================
        // PENYEMPURNAAN 2: EVENT LISTENER UNTUK TOMBOL & MODAL
        // =================================================================
        addQuestionBtn.addEventListener('click', () => questionTypeModal.show());

        questionTypeModalEl.querySelectorAll('.type-option').forEach(option => {
            option.addEventListener('click', function() {
                const type = this.dataset.type;
                questionTypeModal.hide();
                setTimeout(() => addQuestion(type), 300); // Tunggu animasi modal selesai
            });
        });

        // Form Validation & Submission
        surveyForm.addEventListener('submit', function(event) {
            if (!surveyForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else if (questionsContainer.children.length === 0) {
                event.preventDefault();
                Swal.fire('Peringatan', 'Survei harus memiliki minimal 1 pertanyaan.', 'warning');
                return;
            }
            surveyForm.classList.add('was-validated');
        }, false);

        // Update visibility of noQuestionsMessage
        function toggleNoQuestionsMessage() {
            noQuestionsMessage.style.display = questionsContainer.children.length > 0 ? 'none' : 'block';
        }

        // Initial check
        toggleNoQuestionsMessage();

        function addQuestion(type) {
            questionIndex++;
            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block';
            questionBlock.dataset.index = questionIndex;

            const typeDetails = <?= json_encode($question_types) ?>[type] || {
                label: 'Tipe Kustom'
            };

            let optionsHtml = '',
                constraintsHtml = '';
            if (['radio', 'checkbox', 'dropdown'].includes(type)) {
                optionsHtml = `<div class="options-container mt-3 pt-3 border-top"><label class="form-label fw-bold small">Opsi Jawaban:</label><div class="options-list"></div><button type="button" class="btn btn-sm btn-light mt-2 add-option-btn"><i class="material-icons-outlined small">add</i> Tambah Opsi</button></div>`;
            }

            questionBlock.innerHTML = `
                <div class="question-header">
                    <div class="d-flex align-items-center">
                        <i class="material-icons drag-handle me-2">drag_indicator</i>
                        <span class="badge bg-secondary">#<span class="question-number">${questionsContainer.children.length + 1}</span></span>
                        <span class="ms-2 fw-bold small text-muted">${typeDetails.label}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn"><i class="material-icons-outlined small">delete</i></button>
                </div>
                <input type="hidden" name="questions[${questionIndex}][type]" value="${type}">
                <div class="mb-2">
                    <input type="text" class="form-control" name="questions[${questionIndex}][text]" required placeholder="Tuliskan pertanyaan Anda di sini...">
                    <div class="invalid-feedback">Teks pertanyaan tidak boleh kosong.</div>
                </div>
                ${optionsHtml}
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="questions[${questionIndex}][required]" value="1" id="required-${questionIndex}" checked>
                    <label class="form-check-label small" for="required-${questionIndex}">Wajib diisi</label>
                </div>
            `;

            questionsContainer.appendChild(questionBlock);
            if (optionsHtml) {
                addOption(questionBlock.querySelector('.options-list'), questionIndex); // Add initial option
            }
            toggleNoQuestionsMessage();
            updateQuestionNumbers();
        }

        function removeQuestion(button) {
            button.closest('.question-block').remove();
            toggleNoQuestionsMessage();
            updateQuestionNumbers();
        }

        function addOption(container, qIndex) {
            const optionCount = container.children.length;
            const optionDiv = document.createElement('div');
            optionDiv.className = 'option-item input-group input-group-sm mb-2';
            optionDiv.innerHTML = `
                <input type="text" class="form-control" name="questions[${qIndex}][options][]" placeholder="Opsi ${optionCount + 1}" required>
                <button class="btn btn-outline-secondary remove-option-btn" type="button"><i class="material-icons-outlined small">close</i></button>
            `;
            container.appendChild(optionDiv);
        }

        function removeOption(button) {
            const container = button.closest('.options-list');
            if (container.children.length > 1) {
                button.closest('.option-item').remove();
            } else {
                Swal.fire('Peringatan', 'Minimal harus ada 1 opsi jawaban.', 'warning');
            }
        }

        function updateQuestionNumbers() {
            document.querySelectorAll('.question-block').forEach((block, index) => {
                block.querySelector('.question-number').textContent = index + 1;
            });
        }

        // Event delegation for dynamic elements
        questionsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-question-btn')) {
                removeQuestion(e.target.closest('.remove-question-btn'));
            }
            if (e.target.closest('.add-option-btn')) {
                const qBlock = e.target.closest('.question-block');
                const qIndex = qBlock.dataset.index;
                const optionsList = qBlock.querySelector('.options-list');
                addOption(optionsList, qIndex);
            }
            if (e.target.closest('.remove-option-btn')) {
                removeOption(e.target.closest('.remove-option-btn'));
            }
        });

        // Preview Generator
        stepperEl.addEventListener('show.bs-stepper', function(event) {
            if (event.detail.to === 4) { // Preview step index is 4 (1-based)
                generatePreview();
            }
        });

        function generatePreview() {
            let previewHtml = `<h5>${document.getElementById('title').value || '(Judul Survei)'}</h5><p>${document.getElementById('description').value || '(Deskripsi Survei)'}</p><hr>`;
            const questions = questionsContainer.querySelectorAll('.question-block');

            if (questions.length === 0) {
                previewHtml += `<p class="text-muted">Belum ada pertanyaan untuk ditampilkan.</p>`;
            } else {
                questions.forEach((block, index) => {
                    const qText = block.querySelector('[name*="[text]"]').value;
                    const qType = block.querySelector('[name*="[type]"]').value;
                    const isRequired = block.querySelector('[name*="[required]"]').checked;
                    previewHtml += `<div class="mb-3"><h6>${index + 1}. ${qText || '(Teks Pertanyaan)'} ${isRequired ? '<span class="text-danger">*</span>' : ''}</h6><div>${generateAnswerPreview(qType, block)}</div></div>`;
                });
            }
            document.getElementById('surveyPreview').innerHTML = previewHtml;
        }

        function generateAnswerPreview(type, block) {
            switch (type) {
                case 'text':
                    return '<input type="text" class="form-control" disabled placeholder="Jawaban singkat">';
                case 'textarea':
                    return '<textarea class="form-control" rows="3" disabled placeholder="Jawaban panjang"></textarea>';
                case 'number':
                    return '<input type="number" class="form-control" disabled placeholder="0">';
                case 'radio':
                case 'checkbox':
                    return Array.from(block.querySelectorAll('[name*="[options]"]')).map(i => `<div class="form-check"><input class="form-check-input" type="${type}" disabled><label class="form-check-label">${i.value || '(Opsi Kosong)'}</label></div>`).join('');
                case 'dropdown':
                    return `<select class="form-select" disabled><option>-- Pilih --</option>${Array.from(block.querySelectorAll('[name*="[options]"]')).map(i => `<option>${i.value || '(Opsi Kosong)'}</option>`).join('')}</select>`;
                case 'rating':
                    return '<div style="color: #adb5bd; font-size: 1.5rem;"><i class="material-icons-outlined">star_outline</i><i class="material-icons-outlined">star_outline</i><i class="material-icons-outlined">star_outline</i><i class="material-icons-outlined">star_outline</i><i class="material-icons-outlined">star_outline</i></div>';
                default:
                    return `<p class="text-muted"><small>Preview tidak tersedia.</small></p>`;
            }
        }
    });
</script>
<?= $this->endSection() ?>