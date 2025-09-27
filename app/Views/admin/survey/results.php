<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/apex/apexcharts.min.js') ?>"></script>
<script src="<?= base_url('plugins/table/datatable/datatables.js') ?>"></script>
<script src="<?= base_url('plugins/sweetalerts/sweetalert2.min.js') ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeDataTable();
        initializeCharts();
        initializeAnimations();
        bindFilterTabs();
        feather.replace();
    });

    // Initialize DataTable
    function initializeDataTable() {
        $('#responsesTable').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<i data-feather="arrow-left"></i>',
                    "sNext": '<i data-feather="arrow-right"></i>'
                },
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ responden",
                "sSearch": '<i data-feather="search"></i>',
                "sSearchPlaceholder": "Cari responden...",
                "sLengthMenu": "Tampilkan: _MENU_",
                "sEmptyTable": "Belum ada responden",
                "sZeroRecords": "Tidak ada responden yang cocok"
            },
            "stripeClasses": [],
            "lengthMenu": [10, 25, 50, 100],
            "pageLength": 10,
            "order": [
                [<?= !$survey['is_anonymous'] ? '4' : '1' ?>, "desc"]
            ],
            drawCallback: function() {
                feather.replace();
            }
        });
    }

    // Initialize Charts
    function initializeCharts() {
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
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                colors: ['#5c1ac3'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.8,
                        opacityTo: 0.2,
                        stops: [0, 100]
                    }
                },
                xaxis: {
                    categories: [<?= '"' . implode('","', array_column($response_stats['daily_responses'], 'date')) . '"' ?>],
                    type: 'datetime',
                    labels: {
                        format: 'dd MMM',
                        style: {
                            colors: '#666',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Responden',
                        style: {
                            color: '#666',
                            fontSize: '14px',
                            fontWeight: 500
                        }
                    },
                    labels: {
                        style: {
                            colors: '#666'
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy'
                    },
                    theme: 'light'
                },
                grid: {
                    borderColor: '#e0e6ed',
                    strokeDashArray: 5
                }
            };

            var timelineChart = new ApexCharts(document.querySelector("#responseTimelineChart"), timelineOptions);
            timelineChart.render();
        <?php endif; ?>
    }

    // Initialize Animations
    function initializeAnimations() {
        // Animate progress bars
        const progressBars = document.querySelectorAll('.answer-bar-fill');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const percentage = bar.dataset.percentage;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = percentage + '%';
                    }, 100);
                }
            });
        }, {
            threshold: 0.1
        });

        progressBars.forEach(bar => observer.observe(bar));

        // Animate stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Animate question cards
        const questionCards = document.querySelectorAll('.question-result-card');
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'slideInUp 0.6s ease forwards';
                }
            });
        }, {
            threshold: 0.1
        });

        questionCards.forEach(card => cardObserver.observe(card));
    }

    // Filter tabs functionality
    function bindFilterTabs() {
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const filter = this.dataset.filter;

                // Update active tab
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Filter question cards
                const questionCards = document.querySelectorAll('.question-result-card');
                questionCards.forEach(card => {
                    const questionType = card.dataset.type;
                    let shouldShow = false;

                    switch (filter) {
                        case 'all':
                            shouldShow = true;
                            break;
                        case 'multiple-choice':
                            shouldShow = ['radio', 'checkbox', 'dropdown'].includes(questionType);
                            break;
                        case 'text':
                            shouldShow = ['text', 'textarea'].includes(questionType);
                            break;
                        case 'rating':
                            shouldShow = ['rating', 'scale'].includes(questionType);
                            break;
                    }

                    card.style.display = shouldShow ? 'block' : 'none';
                });
            });
        });

        // Tab content switching for text responses
        document.querySelectorAll('[data-toggle="tab"]').forEach(tab => {
            tab.addEventListener('click', function() {
                const target = this.dataset.target;
                const parent = this.closest('.question-result-card');

                // Update active tab
                parent.querySelectorAll('[data-toggle="tab"]').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Show/hide content
                parent.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('active');
                });
                parent.querySelector(target).classList.add('active');
            });
        });
    }

    // Export functions
    function printResults() {
        window.print();
    }

    function shareResults() {
        const url = window.location.href;
        const title = '<?= esc($survey["title"]) ?>';

        if (navigator.share) {
            navigator.share({
                title: 'Hasil Survei: ' + title,
                text: 'Lihat hasil survei: ' + title,
                url: url
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Disalin',
                    text: 'Link hasil survei telah disalin ke clipboard',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    }

    function exportToCSV() {
        window.open('<?= base_url('admin/surveys/export/' . $survey['id'] . '?format=csv') ?>', '_blank');
    }

    function exportToExcel() {
        window.open('<?= base_url('admin/surveys/export/' . $survey['id'] . '?format=excel') ?>', '_blank');
    }

    function downloadResponse(responseId) {
        window.open('<?= base_url('admin/surveys/download-response/') ?>' + responseId + '?format=pdf', '_blank');
    }

    // Advanced analytics functions
    function showAdvancedAnalytics() {
        Swal.fire({
            title: 'Analisis Lanjutan',
            html: `
            <div class="text-left">
                <h6>Fitur yang tersedia:</h6>
                <ul>
                    <li>Cross-tabulation analysis</li>
                    <li>Correlation matrix</li>
                    <li>Response pattern analysis</li>
                    <li>Demographic breakdown</li>
                    <li>Time-based trends</li>
                </ul>
                <p class="mt-3"><small>Fitur ini akan segera tersedia</small></p>
            </div>
        `,
            icon: 'info',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#5c1ac3'
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'p':
                    e.preventDefault();
                    printResults();
                    break;
                case 's':
                    e.preventDefault();
                    shareResults();
                    break;
                case 'e':
                    e.preventDefault();
                    exportToExcel();
                    break;
            }
        }
    });

    // Tooltip initialization
    document.querySelectorAll('[title]').forEach(element => {
        element.addEventListener('mouseenter', function() {
            const title = this.getAttribute('title');
            if (title) {
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.textContent = title;
                tooltip.style.cssText = `
                position: absolute;
                background: #333;
                color: white;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 1000;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.3s;
            `;
                document.body.appendChild(tooltip);

                const rect = this.getBoundingClientRect();
                tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

                setTimeout(() => tooltip.style.opacity = '1', 10);

                this.addEventListener('mouseleave', function() {
                    tooltip.remove();
                }, {
                    once: true
                });
            }
        });
    });

    // Performance monitoring
    const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (entry.entryType === 'largest-contentful-paint') {
                console.log('LCP:', entry.startTime);
            }
        }
    });
    observer.observe({
        entryTypes: ['largest-contentful-paint']
    });

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .stat-card:hover {
        animation: pulse 0.6s ease-in-out;
    }
    
    .custom-tooltip {
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .export-btn:active {
        transform: scale(0.95);
    }
    
    .filter-tab {
        position: relative;
        overflow: hidden;
    }
    
    .filter-tab::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .filter-tab:hover::before {
        left: 100%;
    }
    
    .answer-bar-fill {
        position: relative;
        overflow: hidden;
    }
    
    .answer-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
`;
    document.head.appendChild(style);

    // Initialize feather icons
    feather.replace();

    // Auto-refresh functionality (optional)
    let autoRefreshInterval;

    function toggleAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
            Swal.fire('Auto-refresh dimatikan', '', 'info');
        } else {
            autoRefreshInterval = setInterval(() => {
                location.reload();
            }, 300000); // 5 minutes
            Swal.fire('Auto-refresh diaktifkan', 'Halaman akan refresh setiap 5 menit', 'success');
        }
    }

    // Add auto-refresh toggle to export buttons (optional)
    // Uncomment if needed
    /*
    document.querySelector('.export-buttons').insertAdjacentHTML('beforeend', `
        <button class="export-btn btn-secondary" onclick="toggleAutoRefresh()">
            <i data-feather="refresh-cw"></i> Auto Refresh
        </button>
    `);
    */

    console.log('Survey Results initialized successfully');
</script>
<?= $this->endSection() ?><?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Hasil Survei: <?= esc($survey['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/apex/apexcharts.css') ?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/datatables.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/table/datatable/dt-global_style.css') ?>">
<style>
    .results-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .results-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .results-header h3 {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .results-meta {
        display: flex;
        gap: 30px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        opacity: 0.9;
    }

    .meta-item i {
        width: 16px;
        height: 16px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #e0e6ed;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--accent-color, #5c1ac3);
    }

    .stat-card.primary::before {
        background: #5c1ac3;
    }

    .stat-card.success::before {
        background: #1abc9c;
    }

    .stat-card.warning::before {
        background: #f39c12;
    }

    .stat-card.info::before {
        background: #3498db;
    }

    .stat-header {
        display: flex;
        justify-content: between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
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
        background: #f39c1220;
        color: #f39c12;
    }

    .stat-icon.info {
        background: #3498db20;
        color: #3498db;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #888ea8;
        font-size: 14px;
        font-weight: 500;
    }

    .stat-change {
        font-size: 12px;
        margin-top: 8px;
    }

    .stat-change.positive {
        color: #1abc9c;
    }

    .stat-change.negative {
        color: #e74c3c;
    }

    .question-result-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
        border: 1px solid #e0e6ed;
        transition: all 0.3s ease;
    }

    .question-result-card:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .question-header {
        display: flex;
        justify-content: between;
        align-items: flex-start;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f8f9fa;
    }

    .question-number {
        background: linear-gradient(135deg, #5c1ac3, #764ba2);
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .question-info {
        flex: 1;
    }

    .question-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .question-meta {
        display: flex;
        gap: 20px;
        font-size: 13px;
        color: #888ea8;
    }

    .answer-option {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .answer-option:hover {
        background: #f1f2f3;
    }

    .answer-label {
        min-width: 200px;
        font-weight: 500;
        margin-right: 20px;
    }

    .answer-bar-container {
        flex: 1;
        margin: 0 20px;
        position: relative;
    }

    .answer-bar {
        height: 35px;
        background: #e9ecef;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }

    .answer-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #5c1ac3, #764ba2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 15px;
        color: white;
        font-size: 13px;
        font-weight: 600;
        transition: width 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
    }

    .answer-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2));
        border-radius: 20px;
    }

    .answer-count {
        min-width: 80px;
        text-align: right;
        font-weight: 600;
        color: #2c3e50;
    }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border: 1px solid #e0e6ed;
    }

    .chart-header {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0e6ed;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .chart-desc {
        color: #888ea8;
        font-size: 14px;
    }

    .text-responses {
        max-height: 500px;
        overflow-y: auto;
    }

    .text-response-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #5c1ac3;
        transition: all 0.3s ease;
    }

    .text-response-item:hover {
        background: #f1f2f3;
        transform: translateX(5px);
    }

    .response-text {
        margin-bottom: 10px;
        line-height: 1.6;
        font-size: 15px;
    }

    .response-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #888ea8;
        border-top: 1px solid #dee2e6;
        padding-top: 10px;
    }

    .rating-visualization {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 30px 0;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .star {
        font-size: 40px;
        color: #e0e6ed;
        transition: all 0.3s ease;
    }

    .star.filled {
        color: #ffc107;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .rating-summary {
        text-align: center;
        margin-bottom: 20px;
    }

    .rating-value {
        font-size: 48px;
        font-weight: bold;
        color: #5c1ac3;
        line-height: 1;
    }

    .rating-label {
        color: #888ea8;
        font-size: 16px;
        margin-top: 5px;
    }

    .numeric-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }

    .numeric-stat {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .numeric-value {
        font-size: 24px;
        font-weight: bold;
        color: #5c1ac3;
        margin-bottom: 5px;
    }

    .numeric-label {
        font-size: 12px;
        color: #888ea8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .word-cloud {
        min-height: 300px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .word-cloud-item {
        display: inline-block;
        padding: 8px 16px;
        background: white;
        color: #5c1ac3;
        border-radius: 25px;
        margin: 5px;
        transition: all 0.3s ease;
        border: 2px solid #5c1ac320;
        font-weight: 500;
    }

    .word-cloud-item:hover {
        transform: scale(1.1);
        background: #5c1ac3;
        color: white;
        box-shadow: 0 5px 15px rgba(92, 26, 195, 0.3);
    }

    .responses-table-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #e0e6ed;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .export-btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background: #5c1ac3;
        color: white;
    }

    .btn-success {
        background: #1abc9c;
        color: white;
    }

    .btn-info {
        background: #3498db;
        color: white;
    }

    .btn-warning {
        background: #f39c12;
        color: white;
    }

    .filter-tabs {
        display: flex;
        gap: 5px;
        margin-bottom: 20px;
        border-bottom: 1px solid #e0e6ed;
    }

    .filter-tab {
        padding: 12px 20px;
        border: none;
        background: transparent;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .filter-tab:hover {
        background: #f8f9fa;
    }

    .filter-tab.active {
        border-bottom-color: #5c1ac3;
        background: #5c1ac320;
        color: #5c1ac3;
    }

    .insights-panel {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border: 1px solid #e0e6ed;
    }

    .insight-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        border-left: 4px solid #5c1ac3;
    }

    .insight-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #5c1ac320;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5c1ac3;
    }

    .insight-content h6 {
        margin: 0 0 5px 0;
        font-weight: 600;
    }

    .insight-content p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .results-meta {
            flex-direction: column;
            gap: 10px;
        }

        .export-buttons {
            flex-direction: column;
        }

        .answer-option {
            flex-direction: column;
            align-items: stretch;
        }

        .answer-label {
            min-width: auto;
            margin-bottom: 10px;
        }

        .answer-bar-container {
            margin: 10px 0;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12">

        <!-- Results Header -->
        <div class="results-header">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h3><?= esc($survey['title']) ?></h3>
                    <p class="mb-0 opacity-90"><?= esc($survey['description']) ?></p>

                    <div class="results-meta">
                        <div class="meta-item">
                            <i data-feather="calendar"></i>
                            <span><?= date('d M Y', strtotime($survey['start_date'])) ?> - <?= date('d M Y', strtotime($survey['end_date'])) ?></span>
                        </div>
                        <div class="meta-item">
                            <i data-feather="users"></i>
                            <span><?= $statistics['total_responses'] ?? 0 ?> responden</span>
                        </div>
                        <div class="meta-item">
                            <i data-feather="help-circle"></i>
                            <span><?= count($survey['questions']) ?> pertanyaan</span>
                        </div>
                        <?php if ($survey['is_anonymous']): ?>
                            <div class="meta-item">
                                <i data-feather="user-x"></i>
                                <span>Survei Anonim</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="export-buttons">
                    <a href="<?= base_url('admin/surveys') ?>" class="export-btn btn-secondary">
                        <i data-feather="arrow-left"></i> Kembali
                    </a>
                    <button class="export-btn btn-info" onclick="printResults()">
                        <i data-feather="printer"></i> Cetak
                    </button>
                    <a href="<?= base_url('admin/surveys/export/' . $survey['id']) ?>" class="export-btn btn-success">
                        <i data-feather="download"></i> Export Excel
                    </a>
                    <button class="export-btn btn-warning" onclick="shareResults()">
                        <i data-feather="share-2"></i> Bagikan
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?= number_format($statistics['total_responses'] ?? 0) ?></div>
                        <div class="stat-label">Total Responden</div>
                        <div class="stat-change positive">
                            <i data-feather="trending-up"></i>
                            +<?= number_format($statistics['response_growth'] ?? 0) ?>% dari minggu lalu
                        </div>
                    </div>
                    <div class="stat-icon primary">
                        <i data-feather="users"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?= number_format($statistics['completion_rate'] ?? 0, 1) ?>%</div>
                        <div class="stat-label">Tingkat Penyelesaian</div>
                        <div class="stat-change positive">
                            <i data-feather="check-circle"></i>
                            Sangat baik
                        </div>
                    </div>
                    <div class="stat-icon success">
                        <i data-feather="trending-up"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?= number_format($response_stats['avg_completion_time'] ?? 0, 1) ?></div>
                        <div class="stat-label">Rata-rata Waktu (menit)</div>
                        <div class="stat-change">
                            <i data-feather="clock"></i>
                            Estimasi ideal: 5-10 menit
                        </div>
                    </div>
                    <div class="stat-icon warning">
                        <i data-feather="clock"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?= number_format($statistics['response_rate'] ?? 0, 1) ?>%</div>
                        <div class="stat-label">Tingkat Partisipasi</div>
                        <div class="stat-change <?= ($statistics['response_rate'] ?? 0) > 50 ? 'positive' : 'negative' ?>">
                            <i data-feather="<?= ($statistics['response_rate'] ?? 0) > 50 ? 'trending-up' : 'trending-down' ?>"></i>
                            dari total anggota
                        </div>
                    </div>
                    <div class="stat-icon info">
                        <i data-feather="target"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Panel -->
        <div class="insights-panel">
            <h5 class="mb-3"><i data-feather="lightbulb"></i> Insights & Rekomendasi</h5>
            <div class="insight-item">
                <div class="insight-icon">
                    <i data-feather="trending-up"></i>
                </div>
                <div class="insight-content">
                    <h6>Partisipasi Tinggi</h6>
                    <p>Tingkat respons <?= number_format($statistics['response_rate'] ?? 0, 1) ?>% menunjukkan engagement yang baik dari anggota</p>
                </div>
            </div>

            <?php if (($response_stats['avg_completion_time'] ?? 0) > 10): ?>
                <div class="insight-item">
                    <div class="insight-icon">
                        <i data-feather="alert-triangle"></i>
                    </div>
                    <div class="insight-content">
                        <h6>Waktu Pengisian Agak Lama</h6>
                        <p>Pertimbangkan untuk menyederhanakan pertanyaan atau mengurangi jumlah pertanyaan di survei berikutnya</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="insight-item">
                <div class="insight-icon">
                    <i data-feather="calendar"></i>
                </div>
                <div class="insight-content">
                    <h6>Waktu Optimal Respons</h6>
                    <p>Sebagian besar responden mengisi survei pada <?= $response_stats['peak_response_day'] ?? 'hari kerja' ?></p>
                </div>
            </div>
        </div>

        <!-- Response Timeline Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h5 class="chart-title">Timeline Responden</h5>
                <p class="chart-desc">Distribusi respons berdasarkan waktu</p>
            </div>
            <div id="responseTimelineChart"></div>
        </div>

        <!-- Question Results -->
        <div class="question-results-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5><i data-feather="bar-chart"></i> Hasil Per Pertanyaan</h5>
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">Semua</button>
                    <button class="filter-tab" data-filter="multiple-choice">Pilihan Ganda</button>
                    <button class="filter-tab" data-filter="text">Teks</button>
                    <button class="filter-tab" data-filter="rating">Rating</button>
                </div>
            </div>

            <?php foreach ($survey['questions'] as $index => $question): ?>
                <div class="question-result-card" data-type="<?= $question['question_type'] ?>">
                    <div class="question-header">
                        <div class="d-flex align-items-start">
                            <span class="question-number"><?= $index + 1 ?></span>
                            <div class="question-info">
                                <h6 class="question-title"><?= esc($question['question_text']) ?></h6>
                                <div class="question-meta">
                                    <span><i data-feather="tag"></i> <?= ucfirst($question['question_type']) ?></span>
                                    <span><i data-feather="users"></i> <?= $question['total_answers'] ?? 0 ?> jawaban</span>
                                    <?php if ($question['is_required']): ?>
                                        <span><i data-feather="star"></i> Wajib</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
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
                                    <div class="answer-bar-container">
                                        <div class="answer-bar">
                                            <div class="answer-bar-fill" style="width: <?= $option['percentage'] ?>%" data-percentage="<?= $option['percentage'] ?>">
                                                <?= $option['percentage'] ?>%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="answer-count"><?= $option['count'] ?> responden</div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php elseif ($question['question_type'] == 'checkbox'): ?>
                        <!-- Multiple Choice Results -->
                        <?php if (!empty($distribution)): ?>
                            <?php foreach ($distribution as $option): ?>
                                <div class="answer-option">
                                    <div class="answer-label"><?= esc($option['value']) ?></div>
                                    <div class="answer-bar-container">
                                        <div class="answer-bar">
                                            <div class="answer-bar-fill" style="width: <?= $option['percentage'] ?>%" data-percentage="<?= $option['percentage'] ?>">
                                                <?= $option['percentage'] ?>%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="answer-count"><?= $option['count'] ?> dipilih</div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php elseif ($question['question_type'] == 'rating'): ?>
                        <!-- Rating Results -->
                        <?php if (!empty($distribution)): ?>
                            <div class="rating-summary">
                                <div class="rating-value"><?= number_format($distribution['average'] ?? 0, 1) ?></div>
                                <div class="rating-label">dari 5.0 bintang</div>
                            </div>

                            <div class="rating-visualization">
                                <?php
                                $avg = $distribution['average'] ?? 0;
                                for ($i = 1; $i <= 5; $i++):
                                ?>
                                    <span class="star <?= $i <= round($avg) ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>

                            <?php if (!empty($distribution['distribution'])): ?>
                                <?php foreach ($distribution['distribution'] as $rating): ?>
                                    <div class="answer-option">
                                        <div class="answer-label">
                                            <?= $rating['value'] ?> ★
                                        </div>
                                        <div class="answer-bar-container">
                                            <div class="answer-bar">
                                                <div class="answer-bar-fill" style="width: <?= $rating['percentage'] ?>%" data-percentage="<?= $rating['percentage'] ?>">
                                                    <?= $rating['percentage'] ?>%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="answer-count"><?= $rating['count'] ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php elseif ($question['question_type'] == 'number'): ?>
                        <!-- Number Statistics -->
                        <?php if (!empty($distribution)): ?>
                            <div class="numeric-stats">
                                <div class="numeric-stat">
                                    <div class="numeric-value"><?= number_format($distribution['min'] ?? 0, 2) ?></div>
                                    <div class="numeric-label">Minimum</div>
                                </div>
                                <div class="numeric-stat">
                                    <div class="numeric-value"><?= number_format($distribution['max'] ?? 0, 2) ?></div>
                                    <div class="numeric-label">Maksimum</div>
                                </div>
                                <div class="numeric-stat">
                                    <div class="numeric-value"><?= number_format($distribution['average'] ?? 0, 2) ?></div>
                                    <div class="numeric-label">Rata-rata</div>
                                </div>
                                <div class="numeric-stat">
                                    <div class="numeric-value"><?= number_format($distribution['median'] ?? 0, 2) ?></div>
                                    <div class="numeric-label">Median</div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php elseif (in_array($question['question_type'], ['text', 'textarea'])): ?>
                        <!-- Text Responses -->
                        <?php if (!empty($distribution)): ?>
                            <div class="filter-tabs">
                                <button class="filter-tab active" data-toggle="tab" data-target="#responses<?= $question['id'] ?>">
                                    Jawaban Terbaru
                                </button>
                                <button class="filter-tab" data-toggle="tab" data-target="#wordcloud<?= $question['id'] ?>">
                                    Word Cloud
                                </button>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane active" id="responses<?= $question['id'] ?>">
                                    <div class="text-responses">
                                        <?php foreach ($distribution['recent_answers'] ?? [] as $answer): ?>
                                            <div class="text-response-item">
                                                <div class="response-text"><?= nl2br(esc($answer['answer_text'])) ?></div>
                                                <div class="response-meta">
                                                    <?php if (!$survey['is_anonymous']): ?>
                                                        <span><i data-feather="user"></i> <?= esc($answer['responder_name'] ?? 'Anonim') ?></span>
                                                    <?php endif; ?>
                                                    <span><i data-feather="clock"></i> <?= date('d M Y H:i', strtotime($answer['created_at'])) ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="tab-pane" id="wordcloud<?= $question['id'] ?>">
                                    <div class="word-cloud">
                                        <?php foreach ($distribution['word_frequency'] ?? [] as $word): ?>
                                            <span class="word-cloud-item" style="font-size: <?= min(max($word['size'] ?? 14, 12), 24) ?>px">
                                                <?= esc($word['text']) ?> <small>(<?= $word['count'] ?>)</small>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Other Types -->
                        <div class="text-center py-4 text-muted">
                            <i data-feather="info"></i>
                            <p class="mt-2">Visualisasi tidak tersedia untuk tipe pertanyaan ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Individual Responses Table -->
        <div class="responses-table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5><i data-feather="list"></i> Detail Responden</h5>
                <div class="export-buttons">
                    <button class="export-btn btn-primary" onclick="exportToCSV()">
                        <i data-feather="file-text"></i> CSV
                    </button>
                    <button class="export-btn btn-success" onclick="exportToExcel()">
                        <i data-feather="file"></i> Excel
                    </button>
                </div>
            </div>

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
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($responses as $index => $response): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <?php if (!$survey['is_anonymous']): ?>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm mr-2">
                                                <span class="avatar-title bg-primary rounded-circle">
                                                    <?= strtoupper(substr($response['nama_lengkap'] ?? 'A', 0, 1)) ?>
                                                </span>
                                            </div>
                                            <?= esc($response['nama_lengkap'] ?? 'Anonim') ?>
                                        </div>
                                    </td>
                                    <td><?= esc($response['email'] ?? '') ?></td>
                                    <td><span class="badge badge-secondary"><?= esc($response['nomor_anggota'] ?? '') ?></span></td>
                                <?php endif; ?>
                                <td><?= date('d M Y H:i', strtotime($response['submitted_at'])) ?></td>
                                <td>
                                    <span class="badge badge-success">Selesai</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= base_url('admin/surveys/view-response/' . $survey['id'] . '/' . $response['id']) ?>"
                                            class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i data-feather="eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-secondary" onclick="downloadResponse(<?= $response['id'] ?>)" title="Download PDF">
                                            <i data-feather="download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>