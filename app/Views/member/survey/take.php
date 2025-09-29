<?php

/**
 * View: take.php (Neptune-styled, auto-detect question source)
 *
 * Variabel yang didukung (ambil yang tersedia):
 * - $survey: array|object => id, title, description, start_date, end_date, time_limit, is_anonymous, question_count, questions (bisa array/JSON)
 * - $questions | $surveyQuestions | $questionList : array daftar pertanyaan (boleh object/array campur)
 * - $validation: CodeIgniter\Validation (opsional)
 * - $formAction: string URL submit (opsional; default current_url())
 * - $participantId: string|int (opsional; untuk kunci autosave)
 * - $backUrl: string (opsional; default base_url('admin/surveys'))
 */

$layout = $layout ?? 'layouts/main';
echo $this->extend($layout);

$surveyId = is_array($survey ?? null)
    ? ($survey['id'] ?? 'unknown')
    : (is_object($survey ?? null) ? ($survey->id ?? 'unknown') : 'unknown');

// 2) Baru susun action ke endpoint POST yang sudah ada di routes
$action   = $formAction ?? site_url('member/surveys/submit/' . $surveyId);

$participantKey = isset($participantId) ? (string)$participantId : 'guest';
$draftKey = 'survey_draft_' . $surveyId . '_' . $participantKey;

// ---------- Helpers umum ----------
function _arr($src, $key, $default = null)
{
    if (is_array($src) && array_key_exists($key, $src)) return $src[$key];
    if (is_object($src) && isset($src->{$key})) return $src->{$key};
    return $default;
}
function _any($src, array $keys, $default = null)
{
    foreach ($keys as $k) {
        $v = _arr($src, $k, null);
        if ($v !== null) return $v;
    }
    return $default;
}
function _boolish($v)
{
    if (is_bool($v)) return $v;
    if (is_numeric($v)) return (int)$v === 1;
    if (is_string($v)) return in_array(strtolower(trim($v)), ['1', 'true', 'yes', 'ya', 'y', 'required'], true);
    return false;
}
function _maybe_json_to_array($v)
{
    if (is_array($v)) return $v;
    if (is_object($v)) return (array)$v;
    if (is_string($v)) {
        $t = trim($v);
        if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
            $j = json_decode($v, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($j)) return $j;
        }
    }
    return $v;
}
function _flatten_candidates($cand)
{
    // ambil di bawah kunci umum: data, items, rows, list
    if (is_array($cand)) {
        foreach (['data', 'items', 'rows', 'list', 'questions', 'question_list'] as $k) {
            if (isset($cand[$k]) && is_array($cand[$k])) return $cand[$k];
        }
    }
    return $cand;
}
function _to_array($v)
{
    if (is_array($v)) return $v;
    if (is_object($v)) return (array)$v;
    if (is_string($v)) {
        $parts = preg_split('/[\r\n\|\;]+/', $v);
        if ($parts && count($parts) > 1) return array_values(array_filter(array_map('trim', $parts), 'strlen'));
        if (trim($v) !== '') return [trim($v)];
    }
    return [];
}
function _normalize_options($options)
{
    $arr = _to_array($options);
    $out = [];
    foreach ($arr as $opt) {
        if (is_array($opt)) {
            $lbl = _any($opt, ['label', 'text', 'name', 'title', 'option_label'], null);
            $val = _any($opt, ['value', 'val', 'id', 'key', 'code', 'option_value'], null);
            if ($lbl === null && $val !== null) $lbl = (string)$val;
            if ($val === null && $lbl !== null) $val = (string)$lbl;
            if ($lbl === null && $val === null) continue;
            $out[] = ['value' => (string)$val, 'label' => (string)$lbl];
        } else {
            $out[] = ['value' => (string)$opt, 'label' => (string)$opt];
        }
    }
    return $out;
}
function _map_type($typeRaw)
{
    $map = [1 => 'text', 2 => 'textarea', 3 => 'number', 4 => 'email', 5 => 'phone', 6 => 'date', 7 => 'time', 8 => 'radio', 9 => 'checkbox', 10 => 'dropdown', 11 => 'rating', 12 => 'scale', 13 => 'file'];
    if (is_numeric($typeRaw)) return $map[(int)$typeRaw] ?? 'text';
    $t = strtolower((string)$typeRaw);
    $aliases = ['select' => 'dropdown', 'tel' => 'phone', 'likert' => 'scale', 'star' => 'rating'];
    return $aliases[$t] ?? ($t ?: 'text');
}
function _norm_q($q, $index = 0)
{
    $qid  = _any($q, ['id', 'question_id', 'qid', 'uuid'], 'q_' . $index);
    $type = _map_type(_any($q, ['type', 'type_code', 'question_type'], 'text'));
    $lab  = _any($q, ['question_text', 'text', 'label', 'title', 'name'], 'Pertanyaan ' . $index);
    $req  = _boolish(_any($q, ['is_required', 'required', 'required_flag', 'mandatory'], false));
    $help = _any($q, ['help_text', 'hint', 'description', 'helper_text', 'notes'], null);
    $ph   = _any($q, ['placeholder', 'placeholder_text'], '');
    $min  = _any($q, ['min', 'min_value', 'minval', 'scale_min', 'rating_min'], null);
    $max  = _any($q, ['max', 'max_value', 'maxval', 'scale_max', 'rating_max'], null);
    $step = _any($q, ['step', 'step_value', 'increment'], null);
    $acc  = _any($q, ['accept', 'allowed_types', 'mimes'], null);
    $multi = _boolish(_any($q, ['multiple', 'is_multiple', 'allow_multiple'], $type === 'checkbox' || $type === 'file'));
    $opts = _normalize_options(_any($q, ['options', 'options_json', 'choices', 'scale_options', 'list'], []));
    $maxlength = _any($q, ['maxlength', 'max_length', 'text_maxlength'], null);

    if ($min !== null && !is_numeric($min)) $min = null;
    if ($max !== null && !is_numeric($max)) $max = null;

    return [
        'id' => $qid,
        'type' => $type,
        'label' => $lab,
        'is_required' => $req,
        'help_text' => $help,
        'placeholder' => $ph,
        'min' => $min,
        'max' => $max,
        'step' => $step,
        'accept' => $acc,
        'multiple' => $multi,
        'options' => $opts,
        'maxlength' => $maxlength,
    ];
}
function _collect_questions($survey, $questions = null, $surveyQuestions = null, $questionList = null)
{
    $candidates = [];
    $candidates[] = $questions ?? null;
    $candidates[] = $surveyQuestions ?? null;
    $candidates[] = $questionList ?? null;
    $candidates[] = _arr($survey, 'questions', null);
    $candidates[] = _arr($survey, 'question', null);

    foreach ($candidates as $cand) {
        if ($cand === null) continue;
        $cand = _maybe_json_to_array($cand);
        $cand = _flatten_candidates($cand);
        if (is_array($cand) && count($cand)) return $cand;
    }
    return [];
}
function _old_value($qid, $default = null)
{
    $v = old("answers.$qid");
    return $v !== null ? $v : $default;
}
function _old_array($qid, $default = [])
{
    $v = old("answers.$qid");
    return is_array($v) ? $v : $default;
}

// ---------- Data utama ----------
$surveyId = _arr($survey, 'id', 'unknown');
$participantKey = isset($participantId) ? (string)$participantId : 'guest';
$draftKey = 'survey_draft_' . $surveyId . '_' . $participantKey;

$qRawList = _collect_questions($survey, $questions ?? null, $surveyQuestions ?? null, $questionList ?? null);
$qList = [];
foreach ($qRawList as $i => $rq) {
    $qList[] = _norm_q($rq, $i + 1);
}
?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="row g-3">

        <!-- Header & Deskripsi -->
        <div class="col-12">
            <div class="page-description">
                <h1 class="mb-1"><?= esc(_arr($survey, 'title', 'Survei')) ?></h1>
                <?php if ($desc = _arr($survey, 'description', null)): ?>
                    <div class="text-muted"><?= esc($desc) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notifikasi -->
        <div class="col-12">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (isset($validation) && $validation->getErrors()): ?>
                <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
            <?php endif; ?>
            <?php if ($errs = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ((array)$errs as $e): ?>
                            <li><?= esc($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>

        <!-- Kolom Kiri: Progress + Form -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <!-- Progress -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Progress Pengisian</span>
                        <span id="progress-percentage" class="fw-semibold">0%</span>
                    </div>
                    <div class="progress mb-3">
                        <div id="survey-progress" class="progress-bar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <!-- Form -->
                    <form id="surveyForm" action="<?= esc($action) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="survey_id" value="<?= esc($surveyId) ?>">

                        <?php if (count($qList) === 0): ?>
                            <div class="alert alert-warning mb-0">
                                Pertanyaan belum tersedia pada view. Sumber yang dicek: <code>$questions</code>, <code>$surveyQuestions</code>, <code>$questionList</code>, dan <code>$survey['questions']</code>.
                            </div>
                        <?php endif; ?>

                        <?php foreach ($qList as $idx => $q): ?>
                            <?php
                            $qid = $q['id'];
                            $type = $q['type'];
                            $req = $q['is_required'];
                            $help = $q['help_text'];
                            $ph = $q['placeholder'];
                            $min = $q['min'];
                            $max = $q['max'];
                            $step = $q['step'];
                            $acc = $q['accept'];
                            $multi = $q['multiple'];
                            $opts = $q['options'];
                            $maxlength = $q['maxlength'];
                            ?>
                            <div class="card mb-3" data-question-id="<?= esc($qid) ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;"><?= $idx + 1 ?></span>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">
                                                <?= esc($q['label']) ?><?php if ($req): ?><span class="text-danger ms-1">*</span><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($help)): ?>
                                        <div class="text-muted small mb-3">
                                            <span class="material-icons-outlined align-text-top me-1" style="font-size:18px;">help_outline</span>
                                            <?= esc($help) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (in_array($type, ['text', 'email', 'number', 'date', 'time', 'phone', 'tel'])): ?>
                                        <?php $inputType = $type === 'phone' ? 'tel' : $type; ?>
                                        <input
                                            type="<?= esc($inputType) ?>"
                                            class="form-control"
                                            id="q_<?= esc($qid) ?>"
                                            name="question_<?= esc($qid) ?>"
                                            placeholder="<?= esc($ph) ?>"
                                            value="<?= esc(_old_value($qid) ?? '') ?>"
                                            <?= $req ? 'required' : '' ?>
                                            <?= ($min !== null && $inputType === 'number') ? 'min="' . esc($min) . '"' : '' ?>
                                            <?= ($max !== null && $inputType === 'number') ? 'max="' . esc($max) . '"' : '' ?>
                                            <?= ($step !== null && $inputType === 'number') ? 'step="' . esc($step) . '"' : '' ?>
                                            <?= ($maxlength !== null && is_numeric($maxlength)) ? 'maxlength="' . esc($maxlength) . '"' : '' ?>>

                                    <?php elseif ($type === 'textarea'): ?>
                                        <textarea
                                            class="form-control"
                                            id="q_<?= esc($qid) ?>"
                                            name="question_<?= esc($qid) ?>"

                                            rows="4"
                                            placeholder="<?= esc($ph) ?>"
                                            <?= $req ? 'required' : '' ?>
                                            <?= ($maxlength !== null && is_numeric($maxlength)) ? 'maxlength="' . esc($maxlength) . '"' : '' ?>><?= esc(_old_value($qid) ?? '') ?></textarea>

                                    <?php elseif ($type === 'radio'): ?>
                                        <?php $current = _old_value($qid); ?>
                                        <div class="d-flex flex-column gap-2">
                                            <?php if (!$opts): ?>
                                                <div class="text-muted small">Belum ada opsi.</div>
                                            <?php endif; ?>
                                            <?php foreach ($opts as $i => $o): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        id="q_<?= esc($qid) ?>_<?= $i ?>"
                                                        name="question_<?= esc($qid) ?>"

                                                        value="<?= esc($o['value']) ?>"
                                                        <?= ($current !== null && (string)$current === (string)$o['value']) ? 'checked' : '' ?>
                                                        <?= $req && $i === 0 ? 'required' : '' ?>>
                                                    <label class="form-check-label" for="q_<?= esc($qid) ?>_<?= $i ?>"><?= esc($o['label']) ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                    <?php elseif ($type === 'checkbox'): ?>
                                        <?php $currArr = _old_array($qid); ?>
                                        <div class="d-flex flex-column gap-2">
                                            <?php if (!$opts): ?>
                                                <div class="text-muted small">Belum ada opsi.</div>
                                            <?php endif; ?>
                                            <?php foreach ($opts as $i => $o): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="q_<?= esc($qid) ?>_<?= $i ?>"
                                                        name="answers[<?= esc($qid) ?>][]"
                                                        value="<?= esc($o['value']) ?>"
                                                        <?= in_array((string)$o['value'], array_map('strval', $currArr), true) ? 'checked' : '' ?>
                                                        <?= $req && $i === 0 ? 'required' : '' ?>>
                                                    <label class="form-check-label" for="q_<?= esc($qid) ?>_<?= $i ?>"><?= esc($o['label']) ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                    <?php elseif ($type === 'dropdown'): ?>
                                        <?php $current = _old_value($qid); ?>
                                        <select class="form-select" id="q_<?= esc($qid) ?>" name="question_<?= esc($qid) ?>"
                                            <?= $req ? 'required' : '' ?>>
                                            <option value="" hidden selected>Pilih...</option>
                                            <?php foreach ($opts as $o): ?>
                                                <option value="<?= esc($o['value']) ?>" <?= ($current !== null && (string)$current === (string)$o['value']) ? 'selected' : '' ?>>
                                                    <?= esc($o['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                    <?php elseif ($type === 'rating'): ?>
                                        <?php
                                        $minR = (int)($min ?? 1);
                                        $maxR = (int)($max ?? 5);
                                        if ($maxR < $minR) $maxR = $minR;
                                        $current = (string)(_old_value($qid) ?? '');
                                        ?>
                                        <div class="d-flex align-items-center gap-2" role="radiogroup" aria-label="Rating">
                                            <?php for ($r = $minR; $r <= $maxR; $r++): ?>
                                                <input type="radio" class="btn-check"
                                                    name="question_<?= esc($qid) ?>"

                                                    id="q_<?= esc($qid) ?>_star_<?= $r ?>"
                                                    value="<?= $r ?>"
                                                    <?= $current === (string)$r ? 'checked' : '' ?>
                                                    <?= $r === $minR && $req ? 'required' : '' ?>>
                                                <label class="btn btn-outline-secondary" for="q_<?= esc($qid) ?>_star_<?= $r ?>" title="<?= $r ?>">
                                                    <span class="material-icons-outlined">grade</span>
                                                </label>
                                            <?php endfor; ?>
                                        </div>

                                    <?php elseif ($type === 'scale'): ?>
                                        <?php
                                        $normOpts = $opts;
                                        if (!$normOpts) {
                                            $minS = (int)($min ?? 1);
                                            $maxS = (int)($max ?? 5);
                                            for ($s = $minS; $s <= $maxS; $s++) $normOpts[] = ['value' => $s, 'label' => (string)$s];
                                        }
                                        $current = _old_value($qid);
                                        ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-muted small fw-normal" style="width:35%;">Pilihan</th>
                                                        <?php foreach ($normOpts as $o): ?>
                                                            <th class="text-center small"><?= esc($o['label']) ?></th>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-muted small">Pilih salah satu</td>
                                                        <?php foreach ($normOpts as $i => $o): ?>
                                                            <td class="text-center">
                                                                <input class="form-check-input" type="radio"
                                                                    id="q_<?= esc($qid) ?>_<?= $i ?>"
                                                                    name="question_<?= esc($qid) ?>"

                                                                    value="<?= esc($o['value']) ?>"
                                                                    <?= ($current !== null && (string)$current === (string)$o['value']) ? 'checked' : '' ?>
                                                                    <?= $req && $i === 0 ? 'required' : '' ?>>
                                                            </td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    <?php elseif ($type === 'file'): ?>
                                        <div class="d-grid gap-2">
                                            <input class="form-control" type="file"
                                                id="q_<?= esc($qid) ?>"
                                                name="files[<?= esc($qid) ?>]<?= $multi ? '[]' : '' ?>"
                                                <?= $acc ? 'accept="' . esc($acc) . '"' : '' ?>
                                                <?= $multi ? 'multiple' : '' ?>
                                                <?= $req ? 'required' : '' ?>>
                                            <div class="form-text">Format: <?= $acc ? esc($acc) : 'bebas' ?><?= $multi ? ', bisa lebih dari satu' : '' ?>.</div>
                                        </div>

                                    <?php else: /* fallback text */ ?>
                                        <input type="text" class="form-control"
                                            id="q_<?= esc($qid) ?>"
                                            name="question_<?= esc($qid) ?>"

                                            placeholder="<?= esc($ph) ?>"
                                            value="<?= esc(_old_value($qid) ?? '') ?>"
                                            <?= $req ? 'required' : '' ?>>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Tombol aksi -->
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" id="btnCancel">
                                <span class="material-icons-outlined me-1">close</span> Batal
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="btnSaveDraft">
                                <span class="material-icons-outlined me-1">save</span> Simpan Draft
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <span class="material-icons-outlined me-1">send</span> Kirim Survei
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Meta -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted small">Total Pertanyaan</div>
                        <div class="fw-semibold">
                            <?php
                            $qc = _arr($survey, 'question_count', null);
                            if ($qc === null) $qc = count($qList);
                            echo number_format((int)$qc);
                            ?>
                        </div>
                    </div>

                    <?php if (_arr($survey, 'start_date', null) || _arr($survey, 'end_date', null)): ?>
                        <div class="d-flex align-items-start gap-2">
                            <span class="material-icons-outlined">event</span>
                            <div>
                                <?php if ($sd = _arr($survey, 'start_date', null)): ?>
                                    <div class="small text-muted">Mulai</div>
                                    <div class="fw-semibold"><?= esc($sd) ?></div>
                                <?php endif; ?>
                                <?php if ($ed = _arr($survey, 'end_date', null)): ?>
                                    <div class="small text-muted mt-2">Selesai</div>
                                    <div class="fw-semibold"><?= esc($ed) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($tl = _arr($survey, 'time_limit', null)): ?>
                        <div class="d-flex align-items-start gap-2">
                            <span class="material-icons-outlined">schedule</span>
                            <div>
                                <div class="small text-muted">Batas Waktu</div>
                                <div class="fw-semibold"><?= esc($tl) ?> menit</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (null !== ($anon = _arr($survey, 'is_anonymous', null))): ?>
                        <div class="d-flex align-items-start gap-2">
                            <span class="material-icons-outlined">privacy_tip</span>
                            <div>
                                <div class="small text-muted">Kerahasiaan</div>
                                <div class="fw-semibold"><?= _boolish($anon) ? 'Anonym' : 'Nama dicatat' ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="small text-muted mb-2">Tips</div>
                    <ul class="small ps-3 mb-0">
                        <li>Gunakan <em>Simpan Draft</em> agar jawaban tidak hilang.</li>
                        <li>Periksa kembali isian bertanda * (wajib).</li>
                        <li>Klik <strong>Kirim Survei</strong> untuk menyimpan ke server.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Toast autosave -->
<div id="autoSaveIndicator" class="position-fixed top-0 end-0 m-3 d-none">
    <div class="toast align-items-center show" role="status" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <span class="material-icons-outlined align-text-top me-1">autorenew</span>Tersimpan otomatis
            </div>
        </div>
    </div>
</div>

<!-- Overlay -->
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center bg-dark bg-opacity-50" style="z-index:1055;">
    <div class="card shadow-lg">
        <div class="card-body d-flex align-items-center gap-2">
            <span class="spinner-border" role="status" aria-hidden="true"></span><span>Memproses, mohon tunggu…</span>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function() {
        const form = document.getElementById('surveyForm');
        const progressBar = document.getElementById('survey-progress');
        const progressPct = document.getElementById('progress-percentage');
        const btnCancel = document.getElementById('btnCancel');
        const btnSaveDraft = document.getElementById('btnSaveDraft');
        const btnSubmit = document.getElementById('btnSubmit');
        const overlay = document.getElementById('loadingOverlay');
        const indicator = document.getElementById('autoSaveIndicator');

        const draftKey = <?= json_encode($draftKey) ?>;
        const backUrl = <?= json_encode(site_url('member/surveys')) ?>;

        function getCards() {
            return Array.from(document.querySelectorAll('[data-question-id]'));
        }

        function inputsFor(qId) {
            return document.querySelectorAll(
                '[name="question_' + CSS.escape(qId) + '"], [name="question_' + CSS.escape(qId) + '[]"], [name^="files[' + CSS.escape(qId) + ']"]'
            );
        }

        function isAnsweredByInputs(nodeList) {
            const any = Array.from(nodeList);
            if (!any.length) return false;
            const t = any[0].type;

            if (t === 'radio') return any.some(r => r.checked);
            if (t === 'checkbox') return any.some(c => c.checked);
            if (t === 'file') return any.some(f => f.files && f.files.length);

            // select/text/textarea/number/date/time/tel/email
            const el = any[0];
            if (el.tagName === 'SELECT') return (el.value || '').toString().trim().length > 0;
            return (el.value || '').toString().trim().length > 0;
        }

        function updateProgress() {
            const cards = getCards();
            const total = cards.length || 1;
            let answered = 0;
            cards.forEach(card => {
                const qId = card.getAttribute('data-question-id');
                if (isAnsweredByInputs(inputsFor(qId))) answered++;
            });
            const pct = Math.round((answered / total) * 100);
            progressBar.style.width = pct + '%';
            progressBar.setAttribute('aria-valuenow', pct);
            progressPct.textContent = pct + '%';
        }

        // === Autosave: simpan nilai dari question_{ID} ke localStorage ===
        function snapshotForm() {
            const data = {};
            getCards().forEach(card => {
                const qId = card.getAttribute('data-question-id');
                const inputs = Array.from(inputsFor(qId));
                if (!inputs.length) return;

                const t = inputs[0].type;
                if (t === 'checkbox') {
                    data[qId] = inputs.filter(i => i.checked).map(i => i.value);
                } else if (t === 'radio') {
                    const c = inputs.find(i => i.checked);
                    data[qId] = c ? c.value : '';
                } else if (t === 'file') {
                    // tidak disimpan di localStorage
                } else {
                    const el = inputs[0];
                    data[qId] = (el.value || '').toString();
                }
            });
            return data;
        }

        function restoreDraft() {
            try {
                const raw = localStorage.getItem(draftKey);
                if (!raw) return;
                const {
                    data
                } = JSON.parse(raw) || {};
                if (!data) return;

                Object.keys(data).forEach(qId => {
                    const val = data[qId];
                    const inputs = Array.from(inputsFor(qId));
                    if (!inputs.length) return;

                    const t = inputs[0].type;
                    if (Array.isArray(val)) {
                        // checkbox
                        inputs.forEach(i => {
                            if (val.includes(i.value)) i.checked = true;
                        });
                    } else if (t === 'radio') {
                        const target = inputs.find(i => (i.value === String(val)));
                        if (target) target.checked = true;
                    } else if (t !== 'file') {
                        inputs[0].value = String(val ?? '');
                    }
                });
            } catch (e) {}
        }

        let saveTimer = null;

        function scheduleAutosave() {
            if (saveTimer) clearTimeout(saveTimer);
            saveTimer = setTimeout(() => {
                try {
                    const data = snapshotForm();
                    localStorage.setItem(draftKey, JSON.stringify({
                        t: Date.now(),
                        data
                    }));
                    showIndicator();
                } catch (e) {}
            }, 500);
        }

        function showIndicator() {
            indicator.classList.remove('d-none');
            setTimeout(() => indicator.classList.add('d-none'), 1200);
        }

        function lockUI(lock) {
            overlay.classList.toggle('d-none', !lock);
            btnSubmit.disabled = !!lock;
        }

        document.addEventListener('input', e => {
            if (!form.contains(e.target)) return;
            updateProgress();
            scheduleAutosave();
        });
        document.addEventListener('change', e => {
            if (!form.contains(e.target)) return;
            updateProgress();
            if (e.target.type !== 'file') scheduleAutosave();
        });

        btnSaveDraft.addEventListener('click', () => {
            try {
                const data = snapshotForm();
                localStorage.setItem(draftKey, JSON.stringify({
                    t: Date.now(),
                    data
                }));
                showIndicator();
            } catch (e) {
                alert('Gagal menyimpan draft.');
            }
        });
        btnCancel.addEventListener('click', () => {
            if (confirm('Batalkan pengisian? Perubahan yang belum disimpan draft akan hilang.')) window.location.href = backUrl;
        });

        form.addEventListener('submit', e => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            lockUI(true);
            try {
                const data = snapshotForm();
                localStorage.setItem(draftKey, JSON.stringify({
                    t: Date.now(),
                    data
                }));
            } catch (e) {}
        });

        // init
        restoreDraft();
        updateProgress();
    })();
</script>

<?= $this->endSection() ?>