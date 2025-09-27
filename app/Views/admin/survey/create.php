<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Buat Survei Baru
<?= $this->endSection() ?>

<?= $this->section('pageStyles') ?>
<link href="<?= base_url('neptune-assets/plugins/flatpickr/flatpickr.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('neptune-assets/plugins/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('neptune-assets/plugins/bs-stepper/bs-stepper.min.css') ?>" rel="stylesheet">
<style>
    .card+.card {
        margin-top: 1rem
    }

    .form-help {
        font-size: .875rem;
        color: var(--bs-secondary-color)
    }

    .required::after {
        content: " *";
        color: var(--bs-danger)
    }

    .question-block {
        border: 1px dashed var(--bs-border-color);
        border-radius: .75rem;
        padding: 1rem;
        background: #fff;
        margin-bottom: .75rem
    }

    .question-block.dragging {
        opacity: .65
    }

    .options-list .input-group+.input-group {
        margin-top: .5rem
    }

    .drag-handle {
        cursor: grab;
        color: #adb5bd
    }

    .sticky-actions {
        position: sticky;
        bottom: 0;
        background: rgba(255, 255, 255, .92);
        backdrop-filter: blur(6px);
        border-top: 1px solid var(--bs-border-color);
        padding: .75rem 1rem;
        z-index: 5
    }

    .stepper-action {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Buat Survei Baru</h5>

            <div id="surveyStepper" class="bs-stepper">
                <div class="bs-stepper-header" role="tablist">
                    <div class="step" data-target="#detail-part">
                        <button type="button" class="step-trigger" role="tab" id="detail-part-trigger">
                            <span class="bs-stepper-circle">1</span>
                            <span class="bs-stepper-label">Detail Survei</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#settings-part">
                        <button type="button" class="step-trigger" role="tab" id="settings-part-trigger">
                            <span class="bs-stepper-circle">2</span>
                            <span class="bs-stepper-label">Pengaturan</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#questions-part">
                        <button type="button" class="step-trigger" role="tab" id="questions-part-trigger">
                            <span class="bs-stepper-circle">3</span>
                            <span class="bs-stepper-label">Pertanyaan</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#preview-part">
                        <button type="button" class="step-trigger" role="tab" id="preview-part-trigger">
                            <span class="bs-stepper-circle">4</span>
                            <span class="bs-stepper-label">Pratinjau & Simpan</span>
                        </button>
                    </div>
                </div>

                <div class="bs-stepper-content">
                    <!-- Form harus membungkus SEMUA step content -->
                    <form id="surveyForm" method="POST" action="<?= base_url('admin/surveys/store') ?>" novalidate>
                        <?= csrf_field() ?>

                        <!-- Hidden input untuk validasi questions -->
                        <input type="hidden" name="questions" value="1">

                        <!-- ERROR DISPLAY -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <strong>Error!</strong> <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <strong>Terdapat beberapa kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <!-- form fields -->

                        <!-- Step 1: Detail Survei -->
                        <div id="detail-part" class="content" role="tabpanel" aria-labelledby="detail-part-trigger">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="title" class="form-label required">Judul Survei</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Contoh: Survei Kepuasan Layanan" required>
                                    <div class="invalid-feedback">Judul wajib diisi (min 5 karakter).</div>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label required">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Tuliskan tujuan, target responden, durasi, dll." required></textarea>
                                    <div class="invalid-feedback">Deskripsi wajib diisi (min 10 karakter).</div>
                                </div>
                                <!-- CARI input start_date dan end_date, pastikan formatnya seperti ini -->
                                <div class="col-md-6">
                                    <label class="form-label required">Tanggal & Waktu Mulai</label>
                                    <input type="datetime-local"
                                        class="form-control"
                                        id="start_date"
                                        name="start_date"
                                        required
                                        min="<?= date('Y-m-d\TH:i') ?>">
                                    <div class="invalid-feedback">
                                        Tanggal mulai wajib diisi
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Tanggal & Waktu Berakhir</label>
                                    <input type="datetime-local"
                                        class="form-control"
                                        id="end_date"
                                        name="end_date"
                                        required
                                        min="<?= date('Y-m-d\TH:i', strtotime('+1 hour')) ?>">
                                    <div class="invalid-feedback">
                                        Tanggal berakhir wajib diisi
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 stepper-action">
                                <button class="btn btn-primary" type="button" id="btn-step1-next">Selanjutnya</button>
                            </div>
                        </div>

                        <!-- Step 2: Pengaturan -->
                        <div id="settings-part" class="content" role="tabpanel" aria-labelledby="settings-part-trigger">
                            <div class="list-group">
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Mode Anonim</strong>
                                        <p class="mb-0 text-muted small">Tidak menyimpan identitas responden.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_anonymous" value="1">
                                    </div>
                                </label>
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Izinkan Pengisian Ganda</strong>
                                        <p class="mb-0 text-muted small">Responden dapat mengisi lebih dari sekali.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="allow_multiple" value="1">
                                    </div>
                                </label>
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Wajib Login</strong>
                                        <p class="mb-0 text-muted small">Hanya anggota yang login yang dapat mengisi.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="require_login" value="1" checked>
                                    </div>
                                </label>
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Tampilkan Hasil ke Peserta</strong>
                                        <p class="mb-0 text-muted small">Ringkasan hasil dapat dilihat setelah submit.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="show_results" value="1">
                                    </div>
                                </label>
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Acak Urutan Pertanyaan</strong>
                                        <p class="mb-0 text-muted small">Urutan pertanyaan diacak untuk tiap responden.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="randomize" value="1">
                                    </div>
                                </label>
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Kirim Notifikasi ke Anggota</strong>
                                        <p class="mb-0 text-muted small">Kirim email/notification kepada anggota saat survei dipublikasikan.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notify_members" value="1">
                                    </div>
                                </label>
                            </div>
                            <div class="mt-3 stepper-action">
                                <button class="btn btn-light" type="button" id="btn-step2-prev">Sebelumnya</button>
                                <button class="btn btn-primary" type="button" id="btn-step2-next">Selanjutnya</button>
                            </div>
                        </div>

                        <!-- Step 3: Pertanyaan -->
                        <div id="questions-part" class="content" role="tabpanel" aria-labelledby="questions-part-trigger">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Pembuat Pertanyaan</h6>
                                <div class="stepper-action">
                                    <button type="button" class="btn btn-primary" id="addQuestionBtn" data-bs-toggle="modal" data-bs-target="#questionTypeModal">
                                        <i class="material-icons-outlined me-1">add</i> Tambah Pertanyaan
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="expandAllBtn">Buka Semua</button>
                                    <button type="button" class="btn btn-outline-secondary" id="collapseAllBtn">Tutup Semua</button>
                                </div>
                            </div>
                            <div id="questionsContainer"></div>
                            <div id="noQuestionsMessage" class="text-center p-4 border rounded bg-light">
                                <p class="text-muted mb-0">Belum ada pertanyaan. Klik <strong>Tambah Pertanyaan</strong> untuk memulai.</p>
                            </div>
                            <div class="mt-3 stepper-action">
                                <button class="btn btn-light" type="button" id="btn-step3-prev">Sebelumnya</button>
                                <button class="btn btn-primary" type="button" id="btn-step3-next">Selanjutnya</button>
                            </div>
                        </div>

                        <!-- Step 4: Pratinjau & Simpan -->
                        <div id="preview-part" class="content" role="tabpanel" aria-labelledby="preview-part-trigger">
                            <h6 class="mb-3">Pratinjau Survei</h6>
                            <div id="surveyPreview" class="p-3 border rounded bg-light" style="min-height: 200px;"></div>
                            <div class="sticky-actions mt-3 d-flex justify-content-between align-items-center">
                                <div class="text-muted small">Periksa kembali sebelum mempublikasikan.</div>
                                <div class="stepper-action">
                                    <button class="btn btn-light" type="button" id="btn-step4-prev">Sebelumnya</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="material-icons-outlined me-1">publish</i>
                                        Publikasikan Survei
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form> <!-- TUTUP FORM DI SINI, SETELAH SEMUA STEP -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Tipe Pertanyaan -->
    <?= $this->include('partials/question_type_modal') ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('neptune-assets/plugins/flatpickr/flatpickr.js') ?>"></script>
<script src="<?= base_url('neptune-assets/plugins/flatpickr/l10n/id.js') ?>"></script>
<script src="<?= base_url('neptune-assets/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="<?= base_url('neptune-assets/plugins/bs-stepper/bs-stepper.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const stepperEl = document.getElementById('surveyStepper');
        const stepper = new window.Stepper(stepperEl, {
            linear: false
        });
        const startEl = document.getElementById('start_date');
        const endEl = document.getElementById('end_date');
        const questionsContainer = document.getElementById('questionsContainer');
        const noQuestionsMessage = document.getElementById('noQuestionsMessage');
        const surveyForm = document.getElementById('surveyForm');

        // Flatpickr (ID locale + 24h + constraint end >= start)
        const fpOpts = {
            enableTime: true,
            time_24hr: true,
            dateFormat: 'Y-m-d H:i:s',
            locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
            allowInput: true
        };
        const startPicker = flatpickr(startEl, {
            ...fpOpts,
            minDate: 'today',
            onChange: function(sel) {
                if (sel?.length) {
                    endPicker.set('minDate', sel[0]);
                    const ed = endPicker.selectedDates[0];
                    if (ed && ed < sel[0]) endPicker.setDate(sel[0], true);
                }
            }
        });
        const endPicker = flatpickr(endEl, {
            ...fpOpts,
            minDate: 'today'
        });

        // Sortable
        new Sortable(questionsContainer, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: updateQuestionNumbers
        });

        // Stepper Nav
        document.getElementById('btn-step1-next').addEventListener('click', () => stepper.next());
        document.getElementById('btn-step2-prev').addEventListener('click', () => stepper.previous());
        document.getElementById('btn-step2-next').addEventListener('click', () => stepper.next());
        document.getElementById('btn-step3-prev').addEventListener('click', () => stepper.previous());
        document.getElementById('btn-step3-next').addEventListener('click', () => {
            buildPreview();
            stepper.next();
        });
        document.getElementById('btn-step4-prev').addEventListener('click', () => stepper.previous());

        // Expand/Collapse all
        document.getElementById('expandAllBtn').addEventListener('click', () => toggleAll(true));
        document.getElementById('collapseAllBtn').addEventListener('click', () => toggleAll(false));

        function toggleAll(open) {
            questionsContainer.querySelectorAll('.question-block .q-content').forEach((c) => {

                c.style.display = open ? '' : 'none';
            });
        }

        function toggleEmptyState() {
            noQuestionsMessage.style.display = questionsContainer.children.length ? 'none' : 'block';
        }

        function updateQuestionNumbers() {
            [...questionsContainer.querySelectorAll('.question-block')].forEach((el, i) => {
                const badge = el.querySelector('.q-number');
                if (badge) badge.textContent = i + 1;
            });
        }

        // Modal: pilih tipe pertanyaan
        const modalEl = document.getElementById('questionTypeModal');
        modalEl?.addEventListener('click', function(e) {
            const opt = e.target.closest('.type-option');
            if (!opt) return;
            const type = opt.dataset.type || 'text';
            addQuestion(type);
            bootstrap.Modal.getInstance(modalEl)?.hide();
        });

        // Tambah pertanyaan builder
        function addQuestion(type) {
            const index = questionsContainer.children.length;
            const block = document.createElement('div');
            block.className = 'question-block';
            block.innerHTML = renderQuestionBlock(index, type);
            questionsContainer.appendChild(block);
            attachQuestionEvents(block);
            toggleEmptyState();
            updateQuestionNumbers();
        }

        function renderQuestionBlock(i, type) {
            const header = `
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <i class="material-icons drag-handle">drag_indicator</i>
            <span class="badge bg-secondary">#<span class="q-number">${i+1}</span></span>
            <span class="ms-1 fw-semibold text-muted small">${labelOf(type)}</span>
          </div>
          <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-secondary toggleBtn">Tutup</button>
            <button type="button" class="btn btn-outline-danger deleteBtn"><i class="material-icons-outlined small">delete</i></button>
          </div>
        </div>`;

            const basic = `
        <input type="hidden" name="questions[${i}][type]" value="${type}">
        <div class="mb-2">
          <label class="form-label required">Teks Pertanyaan</label>
          <input type="text" name="questions[${i}][text]" class="form-control" placeholder="Ketik pertanyaan..." required>
          <div class="invalid-feedback">Teks pertanyaan wajib diisi.</div>
        </div>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Placeholder (opsional)</label>
            <input type="text" name="questions[${i}][placeholder]" class="form-control" placeholder="Contoh: Masukkan jawaban">
          </div>
          <div class="col-md-6">
            <label class="form-label">Bantuan (opsional)</label>
            <input type="text" name="questions[${i}][help_text]" class="form-control" placeholder="Petunjuk singkat untuk responden">
          </div>
        </div>`;

            const required = `
        <div class="form-check form-switch mt-2">
          <input class="form-check-input" type="checkbox" id="req-${i}" name="questions[${i}][required]" value="1" checked>
          <label class="form-check-label small" for="req-${i}">Wajib diisi</label>
        </div>`;

            let extra = '';
            if (['radio', 'checkbox', 'dropdown'].includes(type)) {
                extra = `
        <div class="mt-2">
          <label class="form-label">Opsi Jawaban</label>
          <div class="options-list">
            <div class="input-group">
              <span class="input-group-text">1</span>
              <input type="text" name="questions[${i}][options][]" class="form-control" placeholder="Tulis opsi...">
              <button class="btn btn-outline-danger removeOptionBtn" type="button"><i class="material-icons-outlined small">close</i></button>
            </div>
          </div>
          <button class="btn btn-sm btn-outline-primary mt-2 addOptionBtn" type="button"><i class="material-icons-outlined small">add</i> Tambah Opsi</button>
        </div>`;
            } else if (['number', 'rating', 'scale'].includes(type)) {
                extra = `
        <div class="row g-2 mt-2">
          <div class="col-md-6">
            <label class="form-label">Nilai Minimum</label>
            <input type="number" step="any" name="questions[${i}][min_value]" class="form-control" placeholder="cth: 1">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nilai Maksimum</label>
            <input type="number" step="any" name="questions[${i}][max_value]" class="form-control" placeholder="cth: 5">
          </div>
        </div>`;
            } else if (['text', 'textarea'].includes(type)) {
                extra = `
        <div class="row g-2 mt-2">
          <div class="col-md-6">
            <label class="form-label">Panjang Minimum</label>
            <input type="number" name="questions[${i}][min_length]" class="form-control" placeholder="cth: 0">
          </div>
          <div class="col-md-6">
            <label class="form-label">Panjang Maksimum</label>
            <input type="number" name="questions[${i}][max_length]" class="form-control" placeholder="cth: 255">
          </div>
        </div>`;
            }

            return `${header}<div class="q-content">${basic}${extra}${required}</div>`;

        }

        function attachQuestionEvents(block) {
            block.addEventListener('click', function(e) {
                if (e.target.closest('.deleteBtn')) {
                    block.remove();
                    reindexQuestions();
                    toggleEmptyState();
                    return;
                }
                if (e.target.closest('.toggleBtn')) {
                    const c = block.querySelector('.q-content');
                    const btn = e.target.closest('.toggleBtn');
                    const closed = c.style.display === 'none';
                    c.style.display = closed ? '' : 'none';
                    btn.textContent = closed ? 'Tutup' : 'Buka';
                    return;
                }
                if (e.target.closest('.addOptionBtn')) {
                    const list = block.querySelector('.options-list');
                    const idx = list.querySelectorAll('.input-group').length + 1;
                    const ig = document.createElement('div');
                    ig.className = 'input-group';
                    ig.innerHTML = `<span class="input-group-text">${idx}</span><input type="text" name="${getOptionName(block)}" class="form-control" placeholder="Tulis opsi..."><button class="btn btn-outline-danger removeOptionBtn" type="button"><i class="material-icons-outlined small">close</i></button>`;
                    list.appendChild(ig);
                    return;
                }
                if (e.target.closest('.removeOptionBtn')) {
                    const ig = e.target.closest('.input-group');
                    const list = ig.parentElement;
                    ig.remove();
                    // re-number badges
                    [...list.querySelectorAll('.input-group-text')].forEach((b, i) => b.textContent = i + 1);
                    return;
                }
            });
        }

        function getOptionName(block) {
            const type = block.querySelector('input[name*="[type]"]').value;
            const name = block.querySelector('input[name*="[text]"]').name; // questions[i][text]
            const i = (name.match(/questions\[(\d+)\]\[text\]/) || [])[1];
            return `questions[${i}][options][]`;
        }

        function reindexQuestions() {
            const blocks = [...questionsContainer.querySelectorAll('.question-block')];
            blocks.forEach((el, i) => {
                el.querySelector('.q-number').textContent = i + 1;
                // rename inputs to keep continuous indices
                el.querySelectorAll('[name^="questions["]').forEach((inp) => {
                    inp.name = inp.name.replace(/questions\[(\d+)\]/, `questions[${i}]`);
                });
            });
        }

        function labelOf(type) {
            const map = {
                text: 'Jawaban Singkat',
                textarea: 'Paragraf',
                radio: 'Pilihan Ganda',
                checkbox: 'Checkbox',
                dropdown: 'Dropdown',
                number: 'Angka',
                rating: 'Rating',
                scale: 'Skala',
                date: 'Tanggal'
            };
            return map[type] || type;
        }

        // Build Preview (sederhana)
        function buildPreview() {
            const prev = document.getElementById('surveyPreview');
            const blocks = [...questionsContainer.querySelectorAll('.question-block')];
            if (!blocks.length) {
                prev.innerHTML = '<p class="text-muted">(Belum ada pertanyaan)</p>';
                return;
            }
            prev.innerHTML = blocks.map((b) => renderPreviewItem(b)).join('');
        }

        function renderPreviewItem(block) {
            const type = block.querySelector('input[name*="[type]"]').value;
            const text = block.querySelector('input[name*="[text]"]').value || '(Pertanyaan)';
            const opts = [...block.querySelectorAll('.options-list input')].map(i => i.value).filter(Boolean);
            let field = '';
            switch (type) {
                case 'text':
                    field = '<input class="form-control" disabled placeholder="Jawaban singkat">';
                    break;
                case 'textarea':
                    field = '<textarea class="form-control" disabled rows="3" placeholder="Paragraf"></textarea>';
                    break;
                case 'radio':
                    field = opts.map((o, i) => `<div class="form-check"><input class="form-check-input" type="radio" disabled><label class="form-check-label">${o||('Opsi '+(i+1))}</label></div>`).join('');
                    break;
                case 'checkbox':
                    field = opts.map((o, i) => `<div class="form-check"><input class="form-check-input" type="checkbox" disabled><label class="form-check-label">${o||('Opsi '+(i+1))}</label></div>`).join('');
                    break;
                case 'dropdown':
                    field = `<select class="form-select" disabled>${opts.map(o=>`<option>${o||'Opsi'}</option>`).join('')}</select>`;
                    break;
                case 'number':
                    field = '<input type="number" class="form-control" disabled>';
                    break;
                case 'rating':
                    field = '<div style="color:#adb5bd;font-size:1.25rem">★ ★ ★ ★ ★</div>';
                    break;
                case 'scale':
                    field = '<div class="text-muted small">Skala (min–maks)</div>';
                    break;
                case 'date':
                    field = '<input type="text" class="form-control" disabled placeholder="YYYY-MM-DD">';
                    break;
                default:
                    field = '<div class="text-muted">(preview tidak tersedia)</div>';
            }
            return `<div class="mb-3"><div class="fw-semibold mb-1">${text}</div>${field}</div>`;
        }

        // Validation & submit guard
        surveyForm.addEventListener('submit', function(e) {
            // native required
            if (!surveyForm.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                surveyForm.classList.add('was-validated');
                stepper.to(3); // lompat ke Step 3: Pertanyaan
                document.querySelector('.question-block .q-content :invalid')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                if (window.Swal) Swal.fire({
                    icon: 'error',
                    title: 'Lengkapi pertanyaan',
                    text: 'Isi teks pertanyaan/opsi wajib sebelum mempublikasikan.'
                });
                return;
            }
            // logical checks
            const sd = startPicker.selectedDates[0];
            const ed = endPicker.selectedDates[0];
            if (sd && ed && ed < sd) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Rentang waktu tidak valid',
                    text: 'Tanggal Selesai harus setelah Tanggal Mulai.'
                });
                stepper.to(3);
                return;

            }
            if (!questionsContainer.children.length) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada pertanyaan',
                    text: 'Tambahkan minimal satu pertanyaan sebelum mempublikasikan.'
                });
            }
        });

        // === Rehydrate dari old input saat validasi gagal ===
        const OLD_QUESTIONS = <?= json_encode(old('questions') ?? []) ?>;
        if (Array.isArray(OLD_QUESTIONS) && OLD_QUESTIONS.length) {
            OLD_QUESTIONS.forEach((q, idx) => {
                const t = q?.type || 'text';
                addQuestion(t);
                const block = questionsContainer.lastElementChild;
                // isi nilai dasar
                block.querySelector(`input[name="questions[${idx}][text]"]`)?.setAttribute('value', q?.text || '');
                block.querySelector(`input[name="questions[${idx}][placeholder]"]`)?.setAttribute('value', q?.placeholder || '');
                block.querySelector(`input[name="questions[${idx}][help_text]"]`)?.setAttribute('value', q?.help_text || '');
                if (q?.required) block.querySelector(`input[name="questions[${idx}][required]"]`)?.setAttribute('checked', 'checked');
                // isi opsi bila tipe pilihan
                if (['radio', 'checkbox', 'dropdown'].includes(t) && Array.isArray(q?.options)) {
                    const list = block.querySelector('.options-list');
                    list.innerHTML = '';
                    q.options.forEach((opt, i) => {
                        const row = document.createElement('div');
                        row.className = 'input-group';
                        row.innerHTML = `<span class="input-group-text">${i+1}</span>
          <input type="text" name="questions[${idx}][options][]" class="form-control" value="${opt || ''}">
          <button class="btn btn-outline-danger removeOptionBtn" type="button"><i class="material-icons-outlined small">close</i></button>`;
                        list.appendChild(row);
                    });
                }
            });
        }
        // Empty state on load
        toggleEmptyState();
    });

    // Form validation before submit
    document.getElementById('surveyForm').addEventListener('submit', function(e) {
        console.log('Form is being submitted...');

        // Validasi minimal ada 1 pertanyaan
        const questions = document.querySelectorAll('.question-block');
        if (questions.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Minimal tambahkan satu pertanyaan dalam survei!'
            });
            return false;
        }

        // Validasi tanggal
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (!startDate || !endDate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Pastikan tanggal mulai dan berakhir sudah diisi!'
            });
            return false;
        }

        if (new Date(startDate) >= new Date(endDate)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Tanggal berakhir harus setelah tanggal mulai!'
            });
            return false;
        }

        // Validasi setiap pertanyaan punya text
        let hasEmptyQuestion = false;
        questions.forEach((block, index) => {
            const textInput = block.querySelector('input[name*="[text]"]');
            if (!textInput || !textInput.value.trim()) {
                hasEmptyQuestion = true;
                textInput?.classList.add('is-invalid');
            }

            // Untuk pertanyaan pilihan, validasi minimal ada 1 opsi
            const type = block.querySelector('input[name*="[type]"]').value;
            if (['radio', 'checkbox', 'dropdown'].includes(type)) {
                const options = block.querySelectorAll('input[name*="[options][]"]');
                const hasValidOption = Array.from(options).some(opt => opt.value.trim());
                if (!hasValidOption) {
                    hasEmptyQuestion = true;
                    options.forEach(opt => opt.classList.add('is-invalid'));
                }
            }
        });

        if (hasEmptyQuestion) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Pertanyaan Tidak Lengkap',
                text: 'Pastikan semua pertanyaan memiliki teks dan opsi (untuk pertanyaan pilihan)!'
            });
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

        console.log('Form validation passed, submitting...');
    });

    // Debug: Log form data saat akan submit
    document.getElementById('surveyForm').addEventListener('formdata', (e) => {
        console.log('Form Data being sent:');
        for (let [key, value] of e.formData.entries()) {
            console.log(key, value);
        }
    });
</script>
<?= $this->endSection() ?>