<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Survei Anggota
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .survey-card {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .survey-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
    }

    .survey-card.completed {
        background-color: #f1f2f3;
        opacity: 0.7;
    }

    .survey-card h5 a {
        color: #3b3f5c;
        font-weight: 600;
    }

    .survey-meta {
        color: #888ea8;
        font-size: 0.9em;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row">
        <div class="col-12">
            <div class="widget-content widget-content-area">
                <div class="p-4">
                    <h2>Survei Tersedia</h2>
                    <p>Partisipasi Anda sangat berarti. Silakan isi survei yang tersedia di bawah ini.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row layout-top-spacing">
        <div class="col-lg-8">
            <h4>Survei Aktif</h4>
            <hr>
            <?php if (!empty($available_surveys)): ?>
                <?php foreach ($available_surveys as $survey): ?>
                    <div class="survey-card">
                        <h5><a href="<?= base_url('member/surveys/take/' . $survey['id']) ?>"><?= esc($survey['title']) ?></a></h5>
                        <p class="mb-2"><?= esc($survey['description']) ?></p>
                        <div class="survey-meta">
                            <span>Berakhir pada: <?= date('d F Y', strtotime($survey['end_date'])) ?></span>
                        </div>
                        <a href="<?= base_url('member/surveys/take/' . $survey['id']) ?>" class="btn btn-primary mt-3">Isi Survei</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada survei aktif yang tersedia saat ini.</p>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <h4>Survei Selesai</h4>
            <hr>
            <?php if (!empty($completed_surveys)): ?>
                <?php foreach ($completed_surveys as $survey): ?>
                    <div class="survey-card completed">
                        <h5><a href="<?= base_url('member/surveys/results/' . $survey['id']) ?>"><?= esc($survey['title']) ?></a></h5>
                        <div class="survey-meta">
                            <span>Anda telah mengisi survei ini.</span>
                        </div>
                        <a href="<?= base_url('member/surveys/results/' . $survey['id']) ?>" class="btn btn-info mt-3">Lihat Hasil</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Anda belum menyelesaikan survei apapun.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>