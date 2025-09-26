<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Isi Survei: <?= esc($survey['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/flatpickr/flatpickr.css') ?>" rel="stylesheet" type="text/css">
<style>
    .survey-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .question-card {
        background: #fff;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .question-card:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .question-number {
        background: #5c1ac3;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        margin-right: 10px;
    }

    .required-mark {
        color: #e7515a;
        font-weight: bold;
    }

    .radio-option,
    .checkbox-option {
        display: block;
        position: relative;
        padding: 12px 15px;
        margin-bottom: 10px;
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .radio-option:hover,
    .checkbox-option:hover {
        background: #f8f9fa;
        border-color: #5c1ac3;
    }

    .radio-option input[type="radio"],
    .checkbox-option input[type="checkbox"] {
        margin-right: 10px;
    }

    .radio-option.selected,
    .checkbox-option.selected {
        background: #5c1ac320;
        border-color: #5c1ac3;
    }

    .rating-container {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .rating-option {
        width: 50px;
        height: 50px;
        border: 2px solid #e0e6ed;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: bold;
    }

    .rating-option:hover {
        border-color: #5c1ac3;
        background: #f8f9fa;
    }

    .rating-option.selected {
        background: #5c1ac3;
        color: white;
        border-color: #5c1ac3;
    }

    .scale-container {
        margin-top: 20px;
    }

    .scale-labels {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 13px;
        color: #888ea8;
    }

    .progress-indicator {
        position: sticky;
        top: 20px;
        background: white;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 20px;
    }

    .progress-step {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        opacity: 0.5;
        transition: opacity 0.3s;
    }

    .progress-step.completed {
        opacity: 1;
    }

    .progress-step.completed .step-number {
        background: #1abc9c;
    }

    .progress-step.current {
        opacity: 1;
    }

    .progress-step.current .step-number {
        background: #5c1ac3;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(92, 26, 195, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(92, 26, 195, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(92, 26, 195, 0);
        }
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #e0e6ed;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 12px;
        font-weight: bold;
    }

    .auto-save-indicator {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #fff;
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 10px 15px;
        display: none;
        align-items: center;
        gap: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .auto-save-indicator.show {
        display: flex;
    }

    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #5c1ac3;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .file-upload-area {
        border: 2px dashed #e0e6ed;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .file-upload-area:hover {
        border-color: #5c1ac3;
        background: #f8f9fa;
    }

    .file-upload-area.dragover {
        border-color: #5c1ac3;
        background: #5c1ac320;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-9 col-lg-8 col-md-12">
        <!-- Survey Header -->
        <div class="survey-header">
            <h3><?= esc($survey['title']) ?></h3>
            <p class="mb-3"><?= esc($survey['description']) ?></p>
            <div class="d-flex gap-3 text-white-50">
                <span><i data-feather="calendar"></i> Berakhir: <?= date('d M Y H:i', strtotime($survey['end_date'])) ?></span>
                <?php if ($survey['is_anonymous']): ?>
                    <span><i data-feather="user-x"></i> Survei Anonim</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alert for errors -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Survey Form -->
        <form id="surveyForm" action="<?= base_url('member/surveys/submit/' . $survey['id']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <?php foreach ($survey['questions'] as $index => $question): ?>
                <div class="question-card" data-question="<?= $question['id'] ?>">
                    <div class="question-header mb-3">
                        <span class="question-number"><?= $index + 1 ?></span>
                        <span class="question-text">
                            <?= esc($question['question_text']) ?>
                            <?php if ($question['is_required']): ?>
                                <span class="required-mark">*</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if (!empty($question['help_text'])): ?>
                        <p class="text-muted small mb-3"><?= esc($question['help_text']) ?></p>
                    <?php endif; ?>

                    <!-- Answer Input Based on Question Type -->
                    <?php
                    $fieldName = 'question_' . $question['id'];
                    $savedAnswer = $partial_response ?
                        array_column($partial_response['answers'], 'answer_text', 'question_id')[$question['id']] ?? '' :
                        old($fieldName);
                    ?>

                    <?php switch ($question['question_type']):
                        case 'text': ?>
                            <input type="text"
                                class="form-control"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                value="<?= esc($savedAnswer) ?>"
                                placeholder="<?= esc($question['placeholder'] ?? 'Masukkan jawaban Anda') ?>"
                                <?= $question['is_required'] ? 'required' : '' ?>
                                <?= $question['min_length'] ? 'minlength="' . $question['min_length'] . '"' : '' ?>
                                <?= $question['max_length'] ? 'maxlength="' . $question['max_length'] . '"' : '' ?>>
                            <?php break; ?>

                        <?php
                        case 'textarea': ?>
                            <textarea class="form-control"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                rows="4"
                                placeholder="<?= esc($question['placeholder'] ?? 'Masukkan jawaban Anda') ?>"
                                <?= $question['is_required'] ? 'required' : '' ?>
                                <?= $question['min_length'] ? 'minlength="' . $question['min_length'] . '"' : '' ?>
                                <?= $question['max_length'] ? 'maxlength="' . $question['max_length'] . '"' : '' ?>><?= esc($savedAnswer) ?></textarea>
                            <?php break; ?>

                        <?php
                        case 'number': ?>
                            <input type="number"
                                class="form-control"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                value="<?= esc($savedAnswer) ?>"
                                placeholder="<?= esc($question['placeholder'] ?? 'Masukkan angka') ?>"
                                <?= $question['is_required'] ? 'required' : '' ?>
                                <?= $question['min_value'] !== null ? 'min="' . $question['min_value'] . '"' : '' ?>
                                <?= $question['max_value'] !== null ? 'max="' . $question['max_value'] . '"' : '' ?>>
                            <?php break; ?>

                        <?php
                        case 'radio': ?>
                            <?php if (!empty($question['options'])): ?>
                                <?php foreach ($question['options'] as $option): ?>
                                    <label class="radio-option">
                                        <input type="radio"
                                            name="<?= $fieldName ?>"
                                            value="<?= esc($option) ?>"
                                            <?= $savedAnswer == $option ? 'checked' : '' ?>
                                            <?= $question['is_required'] ? 'required' : '' ?>>
                                        <?= esc($option) ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php break; ?>

                        <?php
                        case 'checkbox': ?>
                            <?php if (!empty($question['options'])): ?>
                                <?php
                                $savedCheckboxes = !empty($savedAnswer) ? json_decode($savedAnswer, true) : [];
                                ?>
                                <?php foreach ($question['options'] as $option): ?>
                                    <label class="checkbox-option">
                                        <input type="checkbox"
                                            name="<?= $fieldName ?>[]"
                                            value="<?= esc($option) ?>"
                                            <?= in_array($option, $savedCheckboxes) ? 'checked' : '' ?>>
                                        <?= esc($option) ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php break; ?>

                        <?php
                        case 'dropdown': ?>
                            <?php if (!empty($question['options'])): ?>
                                <select class="form-control"
                                    name="<?= $fieldName ?>"
                                    id="<?= $fieldName ?>"
                                    <?= $question['is_required'] ? 'required' : '' ?>>
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($question['options'] as $option): ?>
                                        <option value="<?= esc($option) ?>" <?= $savedAnswer == $option ? 'selected' : '' ?>>
                                            <?= esc($option) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                            <?php break; ?>

                        <?php
                        case 'date': ?>
                            <input type="text"
                                class="form-control flatpickr-date"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                value="<?= esc($savedAnswer) ?>"
                                placeholder="Pilih tanggal"
                                <?= $question['is_required'] ? 'required' : '' ?>>
                            <?php break; ?>

                        <?php
                        case 'time': ?>
                            <input type="text"
                                class="form-control flatpickr-time"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                value="<?= esc($savedAnswer) ?>"
                                placeholder="Pilih waktu"
                                <?= $question['is_required'] ? 'required' : '' ?>>
                            <?php break; ?>

                        <?php
                        case 'rating': ?>
                            <div class="rating-container">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option <?= $savedAnswer == $i ? 'selected' : '' ?>"
                                        data-value="<?= $i ?>"
                                        onclick="selectRating('<?= $fieldName ?>', <?= $i ?>)">
                                        <?= $i ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                value="<?= esc($savedAnswer) ?>"
                                <?= $question['is_required'] ? 'required' : '' ?>>
                            <?php break; ?>

                        <?php
                        case 'scale': ?>
                            <?php
                            $min = $question['min_value'] ?? 1;
                            $max = $question['max_value'] ?? 10;
                            ?>
                            <div class="scale-container">
                                <div class="scale-labels">
                                    <span><?= $min ?></span>
                                    <span><?= $max ?></span>
                                </div>
                                <input type="range"
                                    class="form-control-range"
                                    name="<?= $fieldName ?>"
                                    id="<?= $fieldName ?>"
                                    min="<?= $min ?>"
                                    max="<?= $max ?>"
                                    value="<?= $savedAnswer ?: $min ?>"
                                    oninput="updateScaleValue('<?= $fieldName ?>', this.value)"
                                    <?= $question['is_required'] ? 'required' : '' ?>>
                                <div class="text-center mt-2">
                                    <span class="badge badge-primary" id="<?= $fieldName ?>_value">
                                        <?= $savedAnswer ?: $min ?>
                                    </span>
                                </div>
                            </div>
                            <?php break; ?>

                        <?php
                        case 'file': ?>
                            <div class="file-upload-area"
                                onclick="document.getElementById('<?= $fieldName ?>').click()"
                                ondrop="handleDrop(event, '<?= $fieldName ?>')"
                                ondragover="handleDragOver(event)"
                                ondragleave="handleDragLeave(event)">
                                <i data-feather="upload-cloud" style="width: 48px; height: 48px; color: #888ea8;"></i>
                                <p class="mt-3">Klik atau drag file untuk upload</p>
                                <small class="text-muted">Maksimal 5MB (PDF, DOC, DOCX, JPG, PNG)</small>
                            </div>
                            <input type="file"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                style="display: none;"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                onchange="handleFileSelect(event, '<?= $fieldName ?>')"
                                <?= $question['is_required'] ? 'required' : '' ?>>
                            <div id="<?= $fieldName ?>_preview" class="mt-3"></div>
                            <?php break; ?>

                        <?php
                        case 'email': ?>
                            <input type="email"
                                class="form-control"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                value="<?= esc($savedAnswer) ?>"
                                placeholder="contoh@email.com"
                                <?= $question['is_required'] ? 'required' : '' ?>>
                            <?php break; ?>

                        <?php
                        case 'phone': ?>
                            <input type="tel"
                                class="form-control"
                                name="<?= $fieldName ?>"
                                id="<?= $fieldName ?>"
                                value="<?= esc($savedAnswer) ?>"
                                placeholder="08xxxxxxxxxx"
                                pattern="[0-9+\-\s\(\)]+"
                                <?= $question['is_required'] ? 'required' : '' ?>>
                            <?php break; ?>

                    <?php endswitch; ?>

                </div>
            <?php endforeach; ?>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                    <i data-feather="save"></i> Simpan Sementara
                </button>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i data-feather="send"></i> Kirim Survei
                </button>
            </div>
        </form>
    </div>

    <!-- Progress Sidebar -->
    <div class="col-xl-3 col-lg-4 col-md-12">
        <div class="progress-indicator">
            <h5 class="mb-3">Progress Pengisian</h5>
            <div class="progress mb-3">
                <div class="progress-bar bg-primary" id="progressBar" role="progressbar"
                    style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="text-center text-muted mb-4">
                <span id="answeredCount">0</span> dari <?= count($survey['questions']) ?> pertanyaan
            </p>

            <div class="question-progress-list">
                <?php foreach ($survey['questions'] as $index => $question): ?>
                    <div class="progress-step" id="step-<?= $question['id'] ?>">
                        <span class="step-number"><?= $index + 1 ?></span>
                        <span class="step-title text-truncate" style="max-width: 150px;">
                            <?= character_limiter(esc($question['question_text']), 30) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($partial_response): ?>
                <div class="alert alert-info mt-3" role="alert">
                    <i data-feather="info"></i>
                    <small>Anda memiliki jawaban yang tersimpan sebelumnya.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Auto Save Indicator -->
<div class="auto-save-indicator" id="autoSaveIndicator">
    <div class="spinner"></div>
    <span>Menyimpan...</span>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/flatpickr/flatpickr.js') ?>"></script>
<script src="<?= base_url('plugins/sweetalerts/sweetalert2.min.js') ?>"></script>

<script>
    // Initialize Flatpickr
    flatpickr('.flatpickr-date', {
        dateFormat: "Y-m-d"
    });

    flatpickr('.flatpickr-time', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    // Initialize feather icons
    feather.replace();

    // Track progress
    function updateProgress() {
        const questions = document.querySelectorAll('.question-card');
        let answered = 0;

        questions.forEach(card => {
            const questionId = card.dataset.question;
            const inputs = card.querySelectorAll('input, textarea, select');
            let hasValue = false;

            inputs.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    if (input.checked) hasValue = true;
                } else if (input.value) {
                    hasValue = true;
                }
            });

            const stepElement = document.getElementById(`step-${questionId}`);
            if (hasValue) {
                answered++;
                stepElement.classList.add('completed');
            } else {
                stepElement.classList.remove('completed');
            }
        });

        const percentage = Math.round((answered / questions.length) * 100);
        document.getElementById('progressBar').style.width = percentage + '%';
        document.getElementById('progressBar').setAttribute('aria-valuenow', percentage);
        document.getElementById('answeredCount').textContent = answered;
    }

    // Auto save functionality
    let autoSaveTimer;

    function setupAutoSave() {
        const form = document.getElementById('surveyForm');
        const inputs = form.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            input.addEventListener('change', () => {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(autoSave, 3000); // Auto save after 3 seconds
                updateProgress();
            });

            input.addEventListener('input', () => {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(autoSave, 3000);
                updateProgress();
            });
        });
    }

    function autoSave() {
        const form = document.getElementById('surveyForm');
        const formData = new FormData(form);

        const indicator = document.getElementById('autoSaveIndicator');
        indicator.classList.add('show');

        fetch('<?= base_url('member/surveys/auto-save/' . $survey['id']) ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                setTimeout(() => {
                    indicator.classList.remove('show');
                    if (data.success) {
                        console.log('Auto-saved at', data.timestamp);
                    }
                }, 1000);
            })
            .catch(error => {
                console.error('Auto-save failed:', error);
                indicator.classList.remove('show');
            });
    }

    // Rating selection
    function selectRating(fieldName, value) {
        const options = document.querySelectorAll(`[onclick*="${fieldName}"]`);
        options.forEach(opt => opt.classList.remove('selected'));

        event.target.classList.add('selected');
        document.getElementById(fieldName).value = value;
        updateProgress();
    }

    // Scale value update
    function updateScaleValue(fieldName, value) {
        document.getElementById(fieldName + '_value').textContent = value;
        updateProgress();
    }

    // File upload handling
    function handleFileSelect(event, fieldName) {
        const file = event.target.files[0];
        if (file) {
            const preview = document.getElementById(fieldName + '_preview');
            preview.innerHTML = `
            <div class="alert alert-success">
                <i data-feather="file"></i>
                ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
            </div>
        `;
            feather.replace();
            updateProgress();
        }
    }

    function handleDragOver(event) {
        event.preventDefault();
        event.currentTarget.classList.add('dragover');
    }

    function handleDragLeave(event) {
        event.currentTarget.classList.remove('dragover');
    }

    function handleDrop(event, fieldName) {
        event.preventDefault();
        event.currentTarget.classList.remove('dragover');

        const file = event.dataTransfer.files[0];
        const input = document.getElementById(fieldName);
        input.files = event.dataTransfer.files;

        handleFileSelect({
            target: input
        }, fieldName);
    }

    // Save draft
    function saveDraft() {
        autoSave();
        Swal.fire({
            icon: 'success',
            title: 'Tersimpan',
            text: 'Jawaban Anda telah disimpan sementara',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Form submission
    document.getElementById('surveyForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengirim survei ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#5c1ac3',
            cancelButtonColor: '#e7515a',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form
                this.submit();
            }
        });
    });

    // Highlight current question on scroll
    function highlightCurrentQuestion() {
        const questions = document.querySelectorAll('.question-card');
        const scrollPosition = window.scrollY + 200;

        questions.forEach(card => {
            const questionId = card.dataset.question;
            const stepElement = document.getElementById(`step-${questionId}`);
            stepElement.classList.remove('current');

            const cardTop = card.offsetTop;
            const cardBottom = cardTop + card.offsetHeight;

            if (scrollPosition >= cardTop && scrollPosition <= cardBottom) {
                stepElement.classList.add('current');
            }
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateProgress();
        setupAutoSave();

        window.addEventListener('scroll', highlightCurrentQuestion);
        highlightCurrentQuestion();
    });

    // Handle radio/checkbox visual selection
    document.querySelectorAll('.radio-option input, .checkbox-option input').forEach(input => {
        input.addEventListener('change', function() {
            const parent = this.closest('.radio-option, .checkbox-option');

            if (this.type === 'radio') {
                // Clear other selections
                const siblings = this.closest('.question-card').querySelectorAll('.radio-option');
                siblings.forEach(sib => sib.classList.remove('selected'));
            }

            if (this.checked) {
                parent.classList.add('selected');
            } else {
                parent.classList.remove('selected');
            }
        });
    });

    // Prevent accidental navigation
    let formChanged = false;
    document.getElementById('surveyForm').addEventListener('change', function() {
        formChanged = true;
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            const confirmationMessage = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        }
    });

    // Remove warning when submitting
    document.getElementById('surveyForm').addEventListener('submit', function() {
        formChanged = false;
    });
</script>
<?= $this->endSection() ?>