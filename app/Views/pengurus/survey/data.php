<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading and Create Button -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title); ?></h1>
        <a href="<?= base_url('/pengurus/surveys/create'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Survei Baru
        </a>
    </div>

    <!-- Flash Message for Success/Error -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Content Row for Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Survei</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($total_surveys); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-poll-h fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Survei Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($active_surveys); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-play-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Survei Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($completed_surveys); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Responden</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($total_responses); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Survei</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Survei</th>
                            <th>Responden</th>
                            <th>Status</th>
                            <th>Tanggal Berakhir</th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($surveys)): ?>
                            <?php $i = 1; ?>
                            <?php foreach ($surveys as $survey): ?>
                                <?php
                                // Calculate status logic
                                $now = date('Y-m-d H:i:s');
                                $status_text = '';
                                $status_color = '';
                                if (!$survey['is_active']) {
                                    $status_text = 'Nonaktif';
                                    $status_color = 'secondary';
                                } elseif ($survey['end_date'] < $now) {
                                    $status_text = 'Selesai';
                                    $status_color = 'danger';
                                } elseif ($survey['start_date'] > $now) {
                                    $status_text = 'Akan Datang';
                                    $status_color = 'warning';
                                } else {
                                    $status_text = 'Aktif';
                                    $status_color = 'success';
                                }
                                ?>
                                <tr id="survey-row-<?= $survey['id']; ?>">
                                    <td><?= $i++; ?></td>
                                    <td><?= esc($survey['title']); ?></td>
                                    <td><?= esc($survey['responses']); ?></td>
                                    <td>
                                        <span class="badge badge-<?= $status_color; ?>"><?= $status_text; ?></span>
                                    </td>
                                    <td><?= date('d M Y H:i', strtotime($survey['end_date'])); ?></td>
                                    <td>
                                        <a href="<?= base_url('/pengurus/surveys/results/' . $survey['id']); ?>" class="btn btn-info btn-sm" title="Lihat Hasil">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="<?= base_url('/pengurus/surveys/edit/' . $survey['id']); ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('/pengurus/surveys/export/' . $survey['id']); ?>" class="btn btn-success btn-sm" title="Export CSV">
                                            <i class="fas fa-file-csv"></i>
                                        </a>
                                        <a href="<?= base_url('/pengurus/surveys/clone/' . $survey['id']); ?>" class="btn btn-primary btn-sm" title="Duplikasi" onclick="return confirm('Anda yakin ingin menduplikasi survei ini?')">
                                            <i class="fas fa-clone"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-survey" data-id="<?= $survey['id']; ?>" data-title="<?= esc($survey['title']); ?>" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada survei yang Anda buat.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<!-- SweetAlert for better confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-survey');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const surveyId = this.dataset.id;
                const surveyTitle = this.dataset.title;

                Swal.fire({
                    title: 'Anda yakin?',
                    html: `Survei "<strong>${surveyTitle}</strong>" akan dihapus. <br><b>Tindakan ini tidak dapat dibatalkan!</b>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send delete request to server
                        fetch(`<?= base_url('/pengurus/surveys/delete/'); ?>${surveyId}`, {
                                method: 'POST', // or DELETE, depending on your route config
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        data.message,
                                        'success'
                                    );
                                    // Remove the table row
                                    document.getElementById(`survey-row-${surveyId}`).remove();
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        data.message || 'Gagal menghapus survei.',
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat menghubungi server.',
                                    'error'
                                );
                            });
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection(); ?>