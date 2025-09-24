<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Mengisi Survei: <?= esc($survey['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1><?= esc($survey['title']) ?></h1>
            <p><?= esc($survey['description']) ?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-10">
        <?= form_open('member/surveys/submit/' . $survey['id']) ?>

        <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $index => $question): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pertanyaan <?= $index + 1 ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="fs-5 mb-3"><?= esc($question['question_text']) ?></p>

                        <?php $options = json_decode($question['options'], true); ?>

                        <?php if ($question['question_type'] == 'text'): ?>
                            <input type="text" name="answers[<?= $question['id'] ?>]" class="form-control" placeholder="Jawaban Anda..." required>

                        <?php elseif ($question['question_type'] == 'textarea'): ?>
                            <textarea name="answers[<?= $question['id'] ?>]" class="form-control" rows="4" placeholder="Jawaban Anda..." required></textarea>

                        <?php elseif ($question['question_type'] == 'radio'): ?>
                            <?php foreach ($options as $key => $value): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?= $question['id'] ?>]" id="q<?= $question['id'] ?>_opt<?= $key ?>" value="<?= esc($value) ?>" required>
                                    <label class="form-check-label" for="q<?= $question['id'] ?>_opt<?= $key ?>">
                                        <?= esc($value) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                        <?php elseif ($question['question_type'] == 'checkbox'): ?>
                            <?php foreach ($options as $key => $value): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answers[<?= $question['id'] ?>][]" id="q<?= $question['id'] ?>_opt<?= $key ?>" value="<?= esc($value) ?>">
                                    <label class="form-check-label" for="q<?= $question['id'] ?>_opt<?= $key ?>">
                                        <?= esc($value) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Kirim Jawaban Survei</button>
                <a href="<?= base_url('member/surveys') ?>" class="btn btn-light btn-lg">Batal</a>
            </div>

        <?php else: ?>
            <div class="card">
                <div class="card-body text-center">
                    <p class="my-3">Survei ini belum memiliki pertanyaan.</p>
                    <a href="<?= base_url('member/surveys') ?>" class="btn btn-secondary">Kembali ke Daftar Survei</a>
                </div>
            </div>
        <?php endif; ?>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>