<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Hasil Survei
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .stat-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }

    .stat-card .icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: #fff;
    }

    .answer-bar {
        background: #eef2ff;
        border-radius: 999px;
        height: 10px;
        overflow: hidden;
        position: relative;
    }

    .answer-bar-fill {
        background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
        height: 100%;
        border-radius: inherit;
    }

    .question-result-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 12px 32px rgba(80, 72, 229, 0.08);
    }

    .word-badge {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 6px 10px;
        margin: 4px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
    }

    .member-answer {
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px;
        border: 1px solid #e2e8f0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$surveyTitle   = esc($survey['title'] ?? 'Survei');
$surveyDesc    = $survey['description'] ?? '';
$statistics    = $statistics ?? [];
$questions     = $survey['questions'] ?? [];
$memberResp    = $member_response ?? [];
$memberAnswers = [];

if (!empty($memberResp['answers']) && is_array($memberResp['answers'])) {
    foreach ($memberResp['answers'] as $answerRow) {
        if (!is_array($answerRow)) {
            continue;
        }
        $qid = $answerRow['question_id'] ?? null;
        if ($qid === null) {
            continue;
        }
        $memberAnswers[$qid] = $answerRow;
    }
}

$totalResponses = $statistics['total_responses'] ?? 0;
$totalMembers   = $statistics['total_members'] ?? null;
$responseRate   = $statistics['response_rate'] ?? null;

$submittedAt = $memberResp['submitted_at'] ?? null;
$completionTime = $memberResp['completion_time'] ?? null;

$formatAnswer = function ($answer, $type) {
    $value = $answer['answer_text'] ?? '';
    if ($value === '' || $value === null) {
        return '<span class="text-muted">Tidak ada jawaban</span>';
    }

    if (in_array($type, ['checkbox'])) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $items = array_filter(array_map('trim', $decoded));
            if (!$items) {
                return '<span class="text-muted">Tidak ada jawaban</span>';
            }
            $badges = array_map(fn($item) => '<span class="badge bg-light text-dark me-1 mb-1">' . esc($item) . '</span>', $items);
            return implode('', $badges);
        }
    }

    if (in_array($type, ['number'])) {
        return '<span class="fw-semibold">' . esc($value) . '</span>';
    }

    return nl2br(esc($value));
};
?>

<div class="row g-4">
    <div class="col-12">
        <div class="page-description">
            <h1 class="mb-2">Hasil Survei</h1>
            <p class="text-muted mb-0">Terima kasih sudah berpartisipasi. Berikut ringkasan hasil survei <strong><?= $surveyTitle ?></strong>.</p>
        </div>
    </div>

    <?php if (!empty($surveyDesc)): ?>
        <div class="col-12">
            <div class="alert alert-info shadow-sm border-0">
                <?= esc($surveyDesc) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="material-icons-outlined">groups</i></div>
                    <div>
                        <div class="text-muted small">Total Responden</div>
                        <div class="display-6 fw-semibold mb-0"><?= number_format($totalResponses) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($totalMembers !== null): ?>
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon"><i class="material-icons-outlined">diversity_3</i></div>
                        <div>
                            <div class="text-muted small">Total Anggota</div>
                            <div class="display-6 fw-semibold mb-0"><?= number_format($totalMembers) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($responseRate !== null): ?>
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon"><i class="material-icons-outlined">speed</i></div>
                        <div>
                            <div class="text-muted small">Tingkat Respon</div>
                            <div class="display-6 fw-semibold mb-0"><?= esc($responseRate) ?>%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0">Ringkasan Jawaban Anda</h5>
            </div>
            <div class="card-body d-flex flex-column gap-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small">Status</span>
                    <span class="badge bg-success">Terkirim</span>
                </div>
                <?php if ($submittedAt): ?>
                    <div class="d-flex align-items-start gap-2">
                        <span class="material-icons-outlined">event</span>
                        <div>
                            <div class="small text-muted">Dikirim pada</div>
                            <div class="fw-semibold"><?= esc($submittedAt) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($completionTime): ?>
                    <div class="d-flex align-items-start gap-2">
                        <span class="material-icons-outlined">schedule</span>
                        <div>
                            <div class="small text-muted">Durasi Pengisian</div>
                            <div class="fw-semibold"><?= esc($completionTime) ?> detik</div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="member-answer">
                    <div class="text-muted small mb-2">Total Pertanyaan</div>
                    <div class="fw-semibold mb-0"><?= count($questions) ?></div>
                </div>
                <a href="<?= site_url('member/surveys/my-response/' . ($survey['id'] ?? '')) ?>" class="btn btn-outline-primary w-100">
                    Lihat Jawaban Detail
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0">Ikhtisar Pertanyaan</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($questions as $idx => $question): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-semibold"><?= ($idx + 1) ?>. <?= esc($question['question_text'] ?? 'Pertanyaan') ?></div>
                                <div class="text-muted small text-uppercase"><?= esc($question['question_type'] ?? 'text') ?></div>
                            </div>
                            <?php $myAnswer = $memberAnswers[$question['id']] ?? null; ?>
                            <div class="text-end small text-muted">
                                <?php if ($myAnswer): ?>
                                    <span class="text-success">Sudah dijawab</span>
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada jawaban</span>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <?php foreach ($questions as $index => $question): ?>
        <?php
        $type = $question['question_type'] ?? 'text';
        $distribution = $question['distribution'] ?? [];
        $questionId = $question['id'] ?? ($index + 1);
        $memberAnswerRow = $memberAnswers[$questionId] ?? null;
        ?>
        <div class="col-12">
            <div class="card question-result-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <div class="badge bg-light text-dark text-uppercase mb-2">Pertanyaan <?= $index + 1 ?></div>
                            <h5 class="mb-2"><?= esc($question['question_text'] ?? 'Pertanyaan') ?></h5>
                            <?php if (!empty($question['description'])): ?>
                                <div class="text-muted small mb-2"><?= esc($question['description']) ?></div>
                            <?php endif; ?>
                            <span class="badge bg-light text-secondary text-uppercase">Tipe: <?= esc($type) ?></span>
                        </div>
                        <div class="member-answer flex-grow-1">
                            <div class="text-muted small mb-1">Jawaban Anda</div>
                            <div><?= $formatAnswer($memberAnswerRow ?? [], $type) ?></div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <?php if (in_array($type, ['radio', 'dropdown', 'checkbox']) && !empty($distribution)): ?>
                        <?php foreach ($distribution as $item): ?>
                            <?php
                            $label = $item['value'] ?? $item['answer_text'] ?? '-';
                            $count = $item['count'] ?? 0;
                            $percentage = $item['percentage'] ?? 0;
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold"><?= esc($label) ?></span>
                                    <span class="text-muted small"><?= number_format($count) ?> responden (<?= number_format((float)$percentage, 2) ?>%)</span>
                                </div>
                                <div class="answer-bar">
                                    <div class="answer-bar-fill" style="width: <?= min(100, max(0, (float)$percentage)) ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif (in_array($type, ['rating', 'scale']) && !empty($distribution)): ?>
                        <?php
                        $dist = $distribution['distribution'] ?? [];
                        $average = $distribution['average'] ?? 0;
                        $total = $distribution['total_responses'] ?? 0;
                        ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div>
                                <div class="text-muted small">Rata-rata Penilaian</div>
                                <div class="display-6 fw-semibold mb-0"><?= number_format((float)$average, 2) ?></div>
                            </div>
                            <div class="text-muted small">Dari <?= number_format($total) ?> responden</div>
                        </div>
                        <?php foreach ($dist as $item): ?>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Skor <?= esc($item['value'] ?? '-') ?></span>
                                    <span class="text-muted small"><?= number_format($item['count'] ?? 0) ?> responden (<?= number_format((float)($item['percentage'] ?? 0), 2) ?>%)</span>
                                </div>
                                <div class="answer-bar">
                                    <div class="answer-bar-fill" style="width: <?= min(100, max(0, (float)($item['percentage'] ?? 0))) ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ($type === 'number' && !empty($distribution)): ?>
                        <div class="row g-3">
                            <?php foreach (['min' => 'Nilai Minimum', 'max' => 'Nilai Maksimum', 'average' => 'Rata-rata', 'median' => 'Median', 'std_dev' => 'Standar Deviasi', 'count' => 'Jumlah Jawaban'] as $key => $label): ?>
                                <?php if (isset($distribution[$key])): ?>
                                    <div class="col-sm-4">
                                        <div class="border rounded-3 p-3 text-center bg-light">
                                            <div class="text-muted small mb-1"><?= esc($label) ?></div>
                                            <div class="fw-semibold"><?= esc($distribution[$key]) ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <?php
                        $recentAnswers = $distribution['recent_answers'] ?? [];
                        $totalTexts = $distribution['total_answers'] ?? 0;
                        $wordFreq = $distribution['word_frequency'] ?? [];
                        ?>
                        <?php if (!empty($recentAnswers)): ?>
                            <div class="mb-3">
                                <div class="text-muted small mb-2">Cuplikan Jawaban</div>
                                <div class="row g-3">
                                    <?php foreach ($recentAnswers as $sample): ?>
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-3 bg-light h-100">
                                                <div class="small">
                                                    <?= nl2br(esc($sample['answer_text'] ?? '')) ?>
                                                </div>
                                                <?php if (!empty($sample['created_at'])): ?>
                                                    <div class="text-muted mt-2 small"><?= esc($sample['created_at']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-light text-primary">Total jawaban: <?= number_format($totalTexts) ?></span>
                            <?php foreach (array_slice($wordFreq, 0, 6) as $word): ?>
                                <span class="word-badge">
                                    <span class="fw-semibold"><?= esc($word['text'] ?? '') ?></span>
                                    <span class="text-muted">×<?= number_format($word['count'] ?? 0) ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>