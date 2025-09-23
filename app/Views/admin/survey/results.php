<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Hasil Survei: <?= esc($survey['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/apex/apexcharts.css') ?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget-content widget-content-area br-6 p-4">
            <a href="<?= base_url('admin/surveys') ?>" class="btn btn-secondary mb-4">
                <i data-feather="arrow-left"></i> Kembali ke Daftar Survei
            </a>
            <h2 class="mb-3"><?= esc($survey['title']) ?></h2>
            <p><?= esc($survey['description']) ?></p>
            <hr>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="widget-content widget-content-area bg-gradient-primary text-white br-6 p-3">
                        <h5>Total Responden</h5>
                        <h2><?= number_format($statistics['total_responses'] ?? 0) ?></h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="widget-content widget-content-area bg-gradient-info text-white br-6 p-3">
                        <h5>Total Pertanyaan</h5>
                        <h2><?= count($statistics['questions'] ?? []) ?></h2>
                    </div>
                </div>
            </div>

            <h4 class="mt-5">Analisis per Pertanyaan</h4>
            <?php if (!empty($statistics['questions'])): ?>
                <?php foreach ($statistics['questions'] as $question): ?>
                    <div class="my-4">
                        <h5><?= esc($question['question_text']) ?></h5>
                        <?php if (in_array($question['question_type'], ['radio', 'checkbox', 'dropdown'])): ?>
                            <div class="w-100">
                                <div id="chart-question-<?= $question['id'] ?>"></div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Jawaban untuk tipe pertanyaan ini ditampilkan di tabel responden di bawah.</p>
                        <?php endif; ?>
                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>


            <h4 class="mt-5">Daftar Responden & Jawaban</h4>
            <div class="table-responsive">
                <table id="responses-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Responden</th>
                            <th>Tanggal Submit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php // Data akan diisi oleh JavaScript dari JSON 
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/apex/apexcharts.min.js') ?>"></script>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Data dari PHP Controller
        const surveyData = <?= json_encode($statistics) ?>;
        const responsesData = <?= json_encode($responses) ?>;
        const survey = <?= json_encode($survey) ?>;

        // Render Chart untuk setiap pertanyaan pilihan
        surveyData.questions.forEach(q => {
            if (['radio', 'checkbox', 'dropdown'].includes(q.question_type)) {
                // Logika agregasi jawaban (disederhanakan, idealnya dari backend)
                let answerCounts = {};
                responsesData.forEach(r => {
                    if (r.answers && r.answers[q.id]) {
                        let answer = r.answers[q.id];
                        try {
                            // Handle checkbox (JSON array)
                            let answerArray = JSON.parse(answer);
                            if (Array.isArray(answerArray)) {
                                answerArray.forEach(ans => {
                                    answerCounts[ans] = (answerCounts[ans] || 0) + 1;
                                });
                                return; // Lanjut ke responden berikutnya
                            }
                        } catch (e) {
                            /* Bukan JSON, lanjutkan */
                        }
                        answerCounts[answer] = (answerCounts[answer] || 0) + 1;
                    }
                });

                const chartOptions = {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Jumlah Jawaban',
                        data: Object.values(answerCounts)
                    }],
                    xaxis: {
                        categories: Object.keys(answerCounts)
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true
                        }
                    }
                };
                new ApexCharts(document.querySelector(`#chart-question-${q.id}`), chartOptions).render();
            }
        });

        // Persiapan DataTables
        let columns = [{
                data: 'nama_lengkap',
                title: 'Responden'
            },
            {
                data: 'submitted_at',
                title: 'Tanggal Submit'
            }
        ];
        if (survey.is_anonymous == 1) {
            columns[0].render = function(data, type, row) {
                return 'Anonim';
            };
        }


        surveyData.questions.forEach(q => {
            columns.push({
                data: `answers.${q.id}`,
                title: q.question_text,
                defaultContent: '-'
            });
        });

        $('#responses-table').DataTable({
            data: responsesData,
            columns: columns,
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'fB>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            buttons: {
                buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm'
                }, {
                    extend: 'print',
                    className: 'btn btn-sm'
                }]
            },
            "oLanguage": {
                /* Terjemahan... */
            },
            "stripeClasses": [],
            "lengthMenu": [10, 25, 50],
            "pageLength": 10
        });
    });
</script>
<?= $this->endSection() ?>