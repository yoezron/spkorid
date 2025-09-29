<?php

/**
 * View: member/survey/my_response.php (robust answer binding)
 * Menampilkan jawaban milik member untuk satu survei, tahan terhadap variasi struktur data.
 *
 * Variabel yang didukung (pakai yang tersedia):
 * - $survey: array|object => id, title, description, questions (opsional)
 * - $response|$myResponse: array|object => answers/answer_list/responseAnswers (opsional)
 * - $answers|$answerList|$responseAnswers|$myAnswers|$myAnswerList: array daftar jawaban
 * - $questions|$surveyQuestions|$questionList: array daftar pertanyaan
 */

$layout = $layout ?? 'layouts/main';
echo $this->extend($layout);

// ===== Helpers =====
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
function _to_array_any($v)
{
    if (is_array($v)) return $v;
    if (is_object($v)) return (array)$v;
    if (is_string($v)) {
        // coba JSON dulu
        $j = json_decode($v, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($j)) return $j;
            if ($j === null || $j === '') return [];
        }
        $t = trim($v);
        return $t === '' ? [] : [$t];
    }
    return $v ? [$v] : [];
}
function _collect_questions($survey, $questions = null, $surveyQuestions = null, $questionList = null)
{
    foreach ([$questions, $surveyQuestions, $questionList, _arr($survey, 'questions', null)] as $cand) {
        if (is_array($cand) && $cand) return $cand;
    }
    return [];
}
function _flatten_candidates($cand)
{
    if (is_array($cand)) {
        foreach (['data', 'items', 'rows', 'list', 'answers', 'answer_list', 'responseAnswers', 'myAnswers'] as $k) {
            if (isset($cand[$k]) && is_array($cand[$k])) return $cand[$k];
        }
    }
    return $cand;
}
function _collect_answer_sources($survey, $resp, ...$named)
{
    $cands = [];
    foreach ($named as $n) if ($n) $cands[] = $n;
    // ambil dari $response/$myResponse bila ada
    if ($resp) {
        foreach (['answers', 'answer_list', 'responseAnswers', 'myAnswers'] as $k) {
            $v = _arr($resp, $k, null);
            if ($v) $cands[] = $v;
        }
    }
    // beberapa implementasi menyimpan di $survey['answers']
    $svAns = _arr($survey ?? [], 'answers', null);
    if ($svAns) $cands[] = $svAns;

    // flatten jika dibungkus data/items/list
    $outs = [];
    foreach ($cands as $cand) {
        $cand = _flatten_candidates($cand);
        if (is_array($cand) && $cand) $outs[] = $cand;
    }
    return $outs;
}
function _build_answer_index(array $sources)
{
    $idx = [];
    foreach ($sources as $src) {
        // bentuk A: associative map [question_id => value]
        $isAssoc = is_array($src) && count($src) && array_keys($src) !== range(0, count($src) - 1);
        if ($isAssoc) {
            foreach ($src as $k => $v) {
                $qid = (string)$k;
                // value bisa string/array; simpan sebagai row seragam
                if (!isset($idx[$qid])) {
                    $idx[$qid] = [
                        'id'          => null,
                        'question_id' => $qid,
                        'answer_text' => is_array($v) ? json_encode(array_values($v), JSON_UNESCAPED_UNICODE) : (string)$v,
                        'file_path'   => null,
                    ];
                }
            }
            continue;
        }

        // bentuk B: array of rows
        foreach ((array)$src as $row) {
            if (!is_array($row) && !is_object($row)) continue;
            $qid = _any($row, ['question_id', 'qid', 'questionId'], null);
            // beberapa implementasi tidak ada question_id, tapi ada 'question' => ['id'=>...]
            if ($qid === null) {
                $qobj = _arr($row, 'question', null);
                if ($qobj) $qid = _arr($qobj, 'id', null);
            }
            if ($qid === null) continue;

            $qid = (string)$qid;
            if (isset($idx[$qid])) continue; // first wins

            $aid   = _any($row, ['id', 'answer_id'], null);
            $afile = _any($row, ['file_path', 'file', 'attachment', 'attachment_path', 'fileUrl'], null);
            $aval  = _any($row, ['answer_text', 'answer', 'value', 'answer_value', 'text', 'content'], '');

            // beberapa controller menyimpan nilai array di kolom 'value' sebagai array
            if (is_array($aval)) $aval = json_encode(array_values($aval), JSON_UNESCAPED_UNICODE);

            $idx[$qid] = [
                'id'          => $aid,
                'question_id' => $qid,
                'answer_text' => (string)$aval,
                'file_path'   => $afile,
            ];
        }
    }
    return $idx;
}

// ===== Data utama =====
$resp     = $response ?? $myResponse ?? [];
$surveyId = _arr($survey ?? [], 'id', 'unknown');
$questions = _collect_questions($survey ?? null, $questions ?? null, $surveyQuestions ?? null, $questionList ?? null);

// kumpulkan jawaban dari berbagai kemungkinan variabel
$sources  = _collect_answer_sources(
    $survey ?? null,
    $resp,
    $answers ?? null,
    $answerList ?? null,
    $responseAnswers ?? null,
    $myAnswers ?? null,
    $myAnswerList ?? null
);
$answersIndex = _build_answer_index($sources);
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

        <!-- Info -->
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
                        <div class="alert alert-info mb-0">Pertanyaan tidak tersedia di payload.</div>
                    <?php else: ?>
                        <?php foreach ($questions as $i => $q): ?>
                            <?php
                            $qid   = _any($q, ['id', 'question_id', 'qid'], $i + 1);
                            $label = _any($q, ['question_text', 'text', 'label', 'title', 'name'], 'Pertanyaan ' . ($i + 1));
                            $key   = (string)$qid;
                            $ans   = $answersIndex[$key] ?? null;

                            $atext = $ans ? _arr($ans, 'answer_text', '') : '';
                            $afile = $ans ? _arr($ans, 'file_path', null) : null;
                            $aid   = $ans ? _arr($ans, 'id', null) : null;

                            $vals  = _to_array_any($atext);
                            ?>
                            <div class="mb-4">
                                <div class="fw-semibold mb-1"><?= ($i + 1) . '. ' . esc($label) ?></div>

                                <?php if ($afile && $aid): ?>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="material-icons-outlined">attach_file</span>
                                        <a href="<?= site_url('member/surveys/download-file/' . $aid) ?>" class="link-primary">Unduh lampiran</a>
                                    </div>
                                <?php endif; ?>

                                <?php if ($vals && count($vals) > 1): ?>
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

                        <?php if (empty($answersIndex)): ?>
                            <div class="alert alert-warning mt-3 mb-0">
                                Jawaban tidak ditemukan pada variabel umum (<code>$answers</code>, <code>$answerList</code>,
                                <code>$responseAnswers</code>, <code>$myAnswers</code>, atau nested di <code>$response</code>).
                                Pastikan controller <em>myResponse()</em> mengirim salah satu dari itu.
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>