<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Hasil Survei: <?= esc($survey['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/apex/apexcharts.css') ?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<style>
    .statistics-card {
        background: #fff;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .stat-widget {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon.primary {
        background: #5c1ac320;
        color: #5c1ac3;
    }

    .stat-icon.success {
        background: #1abc9c20;
        color: #1abc9c;
    }

    .stat-icon.warning {
        background: #e2a03f20;
        color: #e2a03f;
    }

    .stat-icon.info {
        background: #4361ee20;
        color: #4361ee;
    }

    .question-result {
        background: #fff;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
    }

    .question-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e6ed;
    }

    .answer-option {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .answer-bar {
        flex: 1;
        margin: 0 15px;
        height: 30px;
        background: #f8f9fa;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
    }

    .answer-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #5c1ac3, #764ba2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 10px;
        color: white;
        font-size: 12px;
        font-weight: 600;
        transition: width 1s ease;
    }

    .answer-label {
        min-width: 200px;
        font-size: 14px;
    }

    .answer-count {
        min-width: 80px;
        text-align: right;
        font-weight: 600;
    }

    .response-timeline {
        margin-top: 20px;
    }

    .text-responses {
        max-height: 400px;
        overflow-y: auto;
    }

    .text-response-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .word-cloud {
        min-height: 300px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 20px;
    }

    .word-cloud-item {
        display: inline-block;
        padding: 5px 12px;
        background: #5c1ac320;
        color: #5c1ac3;
        border-radius: 20px;
        margin: 5px;
        transition: all 0.3s;
    }

    .word-cloud-item:hover {
        transform: scale(1.1);
        background: #5c1ac3;
        color: white;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
    }

    .rating-visualization {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 20px 0;
    }

    .star {
        font-size: 30px;
        color: #e0e6ed;
    }

    .star.filled {
        color: #ffb830;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12">
        <!-- Header -->
        <div class="statistics-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4><?= esc($survey['title']) ?></h4>
                    <p class="text-muted mb-0"><?= esc($survey['description']) ?></p>
                </div>
                <div class="export-buttons">
                    <a href="<?= base_url('admin/surveys') ?>" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Kembali
                    </a>
                    <button class="btn btn-info" onclick="printResults()">
                        <i data-feather="printer"></i> Cetak
                    </button>
                    <a href="<?= base_url('admin/surveys/export/' . $survey['id']) ?>" class="btn btn-success">
                        <i data-feather="download"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="statistics-card">
                    <div class="stat-widget">
                        <div>
                            <p class="text-muted mb-1">Total Responden</p>
                            <h3><?= number_format($statistics['total_responses'] ?? 0) ?></h3>
                        </div>
                        <div class="stat-icon primary">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="statistics-card">
                    <div class="stat-widget">
                        <div>
                            <p class="text-muted mb-1">Tingkat Partisipasi</p>
                            <h3><?= $statistics['response_rate'] ?? 0 ?>%</h3>
                        </div>
                        <div class="stat-icon success">
                            <i data-feather="trending-up"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="statistics-card">
                    <div class="stat-widget">
                        <div>
                            <p class="text-muted mb-1">Rata-rata Waktu</p>
                            <h3><?= $response_stats['avg_completion_time'] ?? 0 ?> menit</h3>
                        </div>
                        <div class="stat-icon warning">
                            <i data-feather="clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="statistics-card">
                    <div class="stat-widget">
                        <div>
                            <p class="text-muted mb-1">Total Pertanyaan</p>
                            <h3><?= count($survey['questions']) ?></h3>
                        </div>
                        <div class="stat-icon info">
                            <i data-feather="help-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Timeline Chart -->
        <div class="statistics-card">
            <h5 class="mb-3">Timeline Responden</h5>
            <div id="responseTimelineChart"></div>
        </div>

        <!-- Question Results -->
        <div class="statistics-card">
            <h5 class="mb-4">Hasil Per Pertanyaan</h5>

            <?php foreach ($survey['questions'] as $index => $question): ?>
                <div class="question-result">
                    <div class="question-title">
                        <span class="badge badge-primary mr-2"><?= $index + 1 ?></span>
                        <?= esc($question['question_text']) ?>
                        <span class="float-right text-muted">
                            <?= $question['total_answers'] ?? 0 ?> jawaban
                        </span>
                    </div>

                    <?php
                    $distribution = $question['distribution'] ?? [];
                    ?>

                    <?php if (in_array($question['question_type'], ['radio', 'dropdown'])): ?>
                        <!-- Single Choice Results -->
                        <?php if (!empty($distribution)): ?>
                            <?php foreach ($distribution as $option): ?>
                                <div class="answer-option">
                                    <div class="answer-label"><?= esc($option['value']) ?></div>
                                    <div class="answer-bar">
                                        <div class="answer-bar-fill" style="width: <?= $option['percentage'] ?>%">
                                            <?= $option['percentage'] ?>%
                                        </div>
                                    </div>
                                    <div class="answer-count"><?= $option['count'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php elseif ($question['question_type'] == 'checkbox'): ?>
                        <!-- Multiple Choice Results -->
                        <?php if (!empty($distribution)): ?>
                            <?php foreach ($distribution as $option): ?>
                                <div class="answer-option">
                                    <div class="answer-label"><?= esc($option['value']) ?></div>
                                    <div class="answer-bar">
                                        <div class="answer-bar-fill" style="width: <?= $option['percentage'] ?>%">
                                            <?= $option['percentage'] ?>%
                                        </div>
                                    </div>
                                    <div class="answer-count"><?= $option['count'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php elseif ($question['question_type'] == 'rating'): ?>
                        <!-- Rating Results -->
                        <?php if (!empty($distribution)): ?>
                            <div class="rating-visualization">
                                <?php
                                $avg = $distribution['average'] ?? 0;
                                for ($i = 1; $i <= 5; $i++):
                                ?>
                                    <span class="star <?= $i <= round($avg) ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <div class="text-center mb-3">
                                <h4><?= number_format($avg, 1) ?> / 5.0</h4>
                                <p class="text-muted">Rata-rata Rating</p>
                            </div>

                            <?php foreach ($distribution['distribution'] as $rating): ?>
                                <div class="answer-option">
                                    <div class="answer-label">
                                        <?= $rating['value'] ?> <span class="star filled">★</span>
                                    </div>
                                    <div class="answer-bar">
                                        <div class="answer-bar-fill" style="width: <?= $rating['percentage'] ?>%">
                                            <?= $rating['percentage'] ?>%
                                        </div>
                                    </div>
                                    <div class="answer-count"><?= $rating['count'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php elseif ($question['question_type'] == 'scale'): ?>
                        <!-- Scale Results -->
                        <?php if (!empty($distribution)): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <canvas id="scaleChart<?= $question['id'] ?>"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <div class="mt-4">
                                        <p>Rata-rata: <strong><?= $distribution['average'] ?? 0 ?></strong></p>
                                        <p>Minimum: <strong><?= $distribution['min'] ?? 0 ?></strong></p>
                                        <p>Maksimum: <strong><?= $distribution['max'] ?? 0 ?></strong></p>
                                        <p>Total Jawaban: <strong><?= $distribution['count'] ?? 0 ?></strong></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($question['question_type'] == 'number'): ?>
                        <!-- Number Statistics -->
                        <?php if (!empty($distribution)): ?>
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <h5><?= number_format($distribution['min'] ?? 0, 2) ?></h5>
                                    <p class="text-muted">Minimum</p>
                                </div>
                                <div class="col-md-3 text-center">
                                    <h5><?= number_format($distribution['max'] ?? 0, 2) ?></h5>
                                    <p class="text-muted">Maksimum</p>
                                </div>
                                <div class="col-md-3 text-center">
                                    <h5><?= number_format($distribution['average'] ?? 0, 2) ?></h5>
                                    <p class="text-muted">Rata-rata</p>
                                </div>
                                <div class="col-md-3 text-center">
                                    <h5><?= number_format($distribution['median'] ?? 0, 2) ?></h5>
                                    <p class="text-muted">Median</p>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php elseif (in_array($question['question_type'], ['text', 'textarea'])): ?>
                        <!-- Text Responses -->
                        <?php if (!empty($distribution)): ?>
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#responses<?= $question['id'] ?>">
                                        Jawaban Terbaru
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#wordcloud<?= $question['id'] ?>">
                                        Word Cloud
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="responses<?= $question['id'] ?>">
                                    <div class="text-responses">
                                        <?php foreach ($distribution['recent_answers'] as $answer): ?>
                                            <div class="text-response-item">
                                                <p class="mb-1"><?= nl2br(esc($answer['answer_text'])) ?></p>
                                                <small class="text-muted">
                                                    <?= date('d M Y H:i', strtotime($answer['created_at'])) ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="wordcloud<?= $question['id'] ?>">
                                    <div class="word-cloud">
                                        <?php foreach ($distribution['word_frequency'] as $word): ?>
                                            <span class="word-cloud-item" style="font-size: <?= $word['size'] ?>px">
                                                <?= esc($word['text']) ?> (<?= $word['count'] ?>)
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Other Types -->
                        <p class="text-muted">Visualisasi tidak tersedia untuk tipe pertanyaan ini.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Individual Responses Table -->
        <div class="statistics-card">
            <h5 class="mb-4">Detail Responden</h5>
            <div class="table-responsive">
                <table id="responsesTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <?php if (!$survey['is_anonymous']): ?>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. Anggota</th>
                            <?php endif; ?>
                            <th>Waktu Submit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($responses as $index => $response): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <?php if (!$survey['is_anonymous']): ?>
                                    <td><?= esc($response['nama_lengkap']) ?></td>
                                    <td><?= esc($response['email']) ?></td>
                                    <td><?= esc($response['nomor_anggota']) ?></td>
                                <?php endif; ?>
                                <td><?= date('d M Y H:i', strtotime($response['submitted_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/surveys/view-response/' . $survey['id'] . '/' . $response['id']) ?>"
                                        class="btn btn-sm btn-info">
                                        <i data-feather="eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Initialize DataTable
    $('#responsesTable').DataTable({
        "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
            "<'table-responsive'tr>" +
            "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        "oLanguage": {
            "oPaginate": {
                "sPrevious": '<i data-feather="arrow-left"></i>',
                "sNext": '<i data-feather="arrow-right"></i>'
            },
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ responden",
            "sSearch": '<i data-feather="search"></i>',
            "sSearchPlaceholder": "Cari...",
            "sLengthMenu": "Hasil :  _MENU_",
        },
        "stripeClasses": [],
        "lengthMenu": [10, 20, 50, 100],
        "pageLength": 10,
        drawCallback: function() {
            feather.replace();
        }
    });

    // Response Timeline Chart
    <?php if (!empty($response_stats['daily_responses'])): ?>
        var timelineOptions = {
            series: [{
                name: 'Responden',
                data: [<?= implode(',', array_column($response_stats['daily_responses'], 'count')) ?>]
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            colors: ['#5c1ac3'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: [<?= '"' . implode('","', array_column($response_stats['daily_responses'], 'date')) . '"' ?>],
                type: 'datetime',
                labels: {
                    format: 'dd MMM'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Responden'
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                }
            }
        };

        var timelineChart = new ApexCharts(document.querySelector("#responseTimelineChart"), timelineOptions);
        timelineChart.render();
    <?php endif; ?>

    // Scale Charts
    <?php foreach ($survey['questions'] as $question): ?>
        <?php if ($question['question_type'] == 'scale' && !empty($question['distribution'])): ?>
            var ctx = document.getElementById('scaleChart<?= $question['id'] ?>');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_column($question['distribution']['distribution'] ?? [], 'value')) ?>,
                        datasets: [{
                            label: 'Distribusi Jawaban',
                            data: <?= json_encode(array_column($question['distribution']['distribution'] ?? [], 'count')) ?>,
                            backgroundColor: 'rgba(92, 26, 195, 0.5)',
                            borderColor: 'rgba(92, 26, 195, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        <?php endif; ?>
    <?php endforeach; ?>

    // Print function
    function printResults() {
        window.print();
    }

    // Animate progress bars on load
    window.addEventListener('load', function() {
        const progressBars = document.querySelectorAll('.answer-bar-fill');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    });

    // Initialize feather icons
    feather.replace();
</script>
<?= $this->endSection() ?>