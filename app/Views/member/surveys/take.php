<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Isi Survei: <?= esc($survey['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .question-box {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .question-text {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .required-star {
        color: red;
        font-weight: bold;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-lg-8 col-md-10 col-sm-12 m-auto">
            <div class="widget-content widget-content-area br-6 p-4">
                <a href="<?= base_url('member/surveys') ?>" class="btn btn-secondary mb-4"><i data-feather="arrow-left"></i> Kembali</a>
                <h2><?= esc($survey['title']) ?></h2>
                <p><?= esc($survey['description']) ?></p>
                <hr>

                <form action="<?= base_url('member/surveys/submit/' . $survey['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <?php if (!empty($survey['questions'])): ?>
                        <?php foreach ($survey['questions'] as $key => $question): ?>
                            <div class="question-box">
                                <p class="question-text"><?= ($key + 1) . '. ' . esc($question['question_text']) ?>
                                    <?php if ($question['is_required']): ?>
                                        <span class="required-star">*</span>
                                    <?php endif; ?>
                                </p>

                                <?php
                                $options = json_decode($question['options']);
                                $inputName = "answers[" . $question['id'] . "]";
                                ?>

                                <?php switch ($question['question_type']):
                                    case 'text': ?>
                                        <input type="text" class="form-control" name="<?= $inputName ?>" <?= $question['is_required'] ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php
                                    case 'textarea': ?>
                                        <textarea class="form-control" name="<?= $inputName ?>" rows="4" <?= $question['is_required'] ? 'required' : '' ?>></textarea>
                                        <?php break; ?>

                                    <?php
                                    case 'number': ?>
                                        <input type="number" class="form-control" name="<?= $inputName ?>" <?= $question['is_required'] ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php
                                    case 'date': ?>
                                        <input type="date" class="form-control" name="<?= $inputName ?>" <?= $question['is_required'] ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php
                                    case 'radio': ?>
                                        <?php foreach ($options as $option): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="<?= $inputName ?>" id="q<?= $question['id'] ?>_opt_<?= esc($option) ?>" value="<?= esc($option) ?>" <?= $question['is_required'] ? 'required' : '' ?>>
                                                <label class="form-check-label" for="q<?= $question['id'] ?>_opt_<?= esc($option) ?>"><?= esc($option) ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php break; ?>

                                    <?php
                                    case 'checkbox': ?>
                                        <?php foreach ($options as $option): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="<?= $inputName ?>[]" id="q<?= $question['id'] ?>_opt_<?= esc($option) ?>" value="<?= esc($option) ?>">
                                                <label class="form-check-label" for="q<?= $question['id'] ?>_opt_<?= esc($option) ?>"><?= esc($option) ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php break; ?>

                                    <?php
                                    case 'dropdown': ?>
                                        <select class="form-control" name="<?= $inputName ?>" <?= $question['is_required'] ? 'required' : '' ?>>
                                            <option value="">Pilih salah satu</option>
                                            <?php foreach ($options as $option): ?>
                                                <option value="<?= esc($option) ?>"><?= esc($option) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php break; ?>

                                    <?php
                                    case 'rating': ?>
                                        <div class="d-flex justify-content-around">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="<?= $inputName ?>" id="q<?= $question['id'] ?>_rate_<?= $i ?>" value="<?= $i ?>" <?= $question['is_required'] ? 'required' : '' ?>>
                                                    <label class="form-check-label" for="q<?= $question['id'] ?>_rate_<?= $i ?>"><?= $i ?></label>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                        <?php break; ?>

                                <?php endswitch; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-lg btn-success">Kirim Jawaban Survei</button>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Survei ini belum memiliki pertanyaan.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>