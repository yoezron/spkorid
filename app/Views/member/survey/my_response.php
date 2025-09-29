<?php

/**
 * View: member/survey/my_response.php
 * Menampilkan jawaban milik member untuk satu survei.
 *
 * Variabel yang didukung (pakai yang tersedia):
 * - $survey: array|object => id, title, description, questions (opsional)
 * - $response|$myResponse: array|object => id, is_complete, submitted_at, started_at, completion_time (opsional)
 * - $answers|$answerList|$responseAnswers: array daftar jawaban, idealnya berkey question_id
 *      Elemen bisa berisi: id, question_id, answer_text, file_path, created_at, question_text (opsional)
 * - $questions|$surveyQuestions: array daftar pertanyaan (opsional)
 */

$layout = $layout ?? 'layouts/main';
echo $this->extend($layout);

// helper kecil supaya view tahan array|object
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
function _to_array($v)
{
    if (is_array($v)) return $v;
    if (is_object($v)) return (array)$v;
    if (is_string($v)) {
        $j = json_decode($v, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($j)) return $j;
        // single string fallback
        $t = trim($v);
        return $t === '' ? [] : [$t];
    }
    return $v ? [$v] : [];
}
function _collect_questions($survey, $questions = null, $surveyQuestions = null)
{
    $cands = [];
    if ($questions)        $cands[] = $questions;
    if ($surveyQuestions)  $cands[] = $surveyQuestions;
    if ($survey && _arr($survey, 'questions', null)) $cands[] = _arr($survey, 'questions');
    foreach ($cands as $c) if (is_array($c) && $c) return $c;
    return [];
}
function _answers_map($answers, $answerList = null, $responseAnswers = null)
{
    $src = $answers ?? $answerList ?? $responseAnswers ?? [];
    // jika bentuknya list jawaban, ubah ke peta question_id => row
    $map = [];
    foreach ((array)$src as $row) {
        $qid = _any($row, ['question_id', 'qid', 'questionId'], null);
        if ($qid === null) continue;
        $map[(string)$qid] = $row;
    }
    return $map ?: (is_array($src) ? $src : []);
}

$surveyId   = _arr($survey ?? [], 'id', 'unknown');
$resp       = $response ?? $myResponse ?? [];
$answersMap = _answers_map($answers ?? null, $answerList ?? null, $responseAnswers ?? null);
$questions  = _collect_questions($survey ?? null, $questions ?? null, $surveyQuestions ?? null);
?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="row g-3">

        <!-- Header -->
        <div class="col-12">
            <div class="page-description d-flex flex-column">
                <h1 class="mb-1"><?= esc(_arr($survey ?? [], 'title', 'Survei')) ?></h1>
                <div class="text-muted">Jawaban Saya</div>
            </div>
        </div>

        <!-- Info status -->
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted small">Status</div>
                        <?php $isComplete = (int)_arr($resp, 'is_complete', 1) === 1; ?>
                        <span class="badge <?= $isComplete ? 'bg-success' : 'bg-warning text-dark' ?>">
                            <?= $isComplete ? 'Selesai' : 'Draft' ?>
                        </span>
                    </div>

                    <?php if ($t = _arr($resp, 'submitted_at', null)): ?>
                        <div class="d-flex align-items-start gap-2">
                            <span class="material-icons-outlined">event</span>
                            <div>
                                <div class="small text-muted">Dikirim</div>
                                <div class="fw-semibold"><?= esc($t) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($c = _arr($resp, 'completion_time', null)): ?>
                        <div class="d-flex align-items-start gap-2">
                            <span class="material-icons-outlined">schedule</span>
                            <div>
                                <div class="small text-muted">Durasi</div>
                                <div class="fw-semibold"><?= esc($c) ?> detik</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <a href="<?= site_url('member/surveys') ?>" class="btn btn-light w-100">
                            <span class="material-icons-outlined me-1">arrow_back</span> Kembali
                        </a>
                        <a href="<?= site_url('member/surveys/take/' . $surveyId) ?>" class="btn btn-outline-primary w-100">
                            <span class="material-icons-outlined me-1">edit</span> Isi Ulang
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jawaban -->
        <div class="col-lg-8 order-lg-1">
            <div class="card">
                <div class="card-body">
                    <?php if (!$questions): ?>
                        <div class="alert alert-info mb-0">
                            Pertanyaan tidak tersedia di payload. Controller sebaiknya mengirim <code>$questions</code> atau <code>$survey['questions']</code> agar urutan & teks pertanyaan akurat.
                        </div>
                    <?php else: ?>
                        <?php foreach ($questions as $i => $q): ?>
                            <?php
                            $qid   = _any($q, ['id', 'question_id', 'qid'], $i + 1);
                            $label = _any($q, ['question_text', 'text', 'label', 'title', 'name'], 'Pertanyaan ' . ($i + 1));
                            $ans   = $answersMap[(string)$qid] ?? null;
                            $atext = _arr($ans ?? [], 'answer_text', '');
                            $afile = _arr($ans ?? [], 'file_path', null);
                            $aid   = _arr($ans ?? [], 'id', null); // jika controller kirim id answer
                            // decode jika JSON (checkbox dsb)
                            $vals  = _to_array($atext);
                            ?>
                            <div class="mb-4">
                                <div class="fw-semibold mb-1"><?= ($i + 1) . '. ' . esc($label) ?></div>

                                <?php if ($afile): ?>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="material-icons-outlined">attach_file</span>
                                        <?php if ($aid !== null): ?>
                                            <a href="<?= site_url('member/surveys/download-file/' . $aid) ?>" class="link-primary">Unduh lampiran</a>
                                        <?php else: ?>
                                            <a href="<?= site_url('member/surveys/download-file/' . $qid) ?>" class="link-primary">Unduh lampiran</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (count($vals) > 1): ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($vals as $v): ?>
                                            <span class="badge bg-secondary"><?= esc((string)$v) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-body"><?= esc($vals ? (string)$vals[0] : '') ?></div>
                                <?php endif; ?>
                            </div>
                            <hr class="text-muted my-3">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>