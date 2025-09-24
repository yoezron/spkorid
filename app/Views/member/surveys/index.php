<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Survei Anggota
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Survei Anggota</h1>
            <p>Partisipasi Anda sangat berarti untuk kemajuan serikat. Silakan isi survei yang tersedia.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Daftar Survei</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($surveys)): ?>
                        <?php foreach ($surveys as $survey): ?>
                            <li class="list-group-item d-md-flex align-items-center justify-content-between">
                                <div class="flex-grow-1 me-3">
                                    <h6 class="mb-1"><?= esc($survey['title']) ?></h6>
                                    <p class="text-muted mb-1"><?= esc(character_limiter($survey['description'], 120)) ?></p>
                                    <small class="text-muted">
                                        Berakhir pada: <?= date('d F Y', strtotime($survey['end_date'])) ?>
                                    </small>
                                </div>
                                <div class="text-md-end mt-3 mt-md-0">
                                    <?php if ($survey['has_completed']): ?>
                                        <span class="badge badge-style-light rounded-pill badge-success"><i class="material-icons-outlined me-1">check_circle</i> Sudah Diisi</span>
                                        <a href="<?= base_url('member/surveys/results/' . $survey['id']) ?>" class="btn btn-sm btn-secondary ms-2">Lihat Hasil</a>
                                    <?php else: ?>
                                        <span class="badge badge-style-light rounded-pill badge-info">Tersedia</span>
                                        <a href="<?= base_url('member/surveys/take/' . $survey['id']) ?>" class="btn btn-sm btn-primary ms-2">Isi Survei</a>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center">
                            <p class="my-3">Saat ini tidak ada survei yang tersedia.</p>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="mt-4">
                    <?= $pager->links('default', 'bootstrap_5') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>