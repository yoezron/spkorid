<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Aktivitas Pengguna: <?= esc($user['username']) ?>
<?= $this->endSection() ?>

<?= $this->section('pageStyles') ?>
<link href="<?= base_url('neptune-assets/plugins/datatables/datatables.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Log Aktivitas Pengguna</h1>
            <p>Menampilkan rekaman aktivitas untuk pengguna: <strong><?= esc($user['username']) ?></strong></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="activity-log-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Deskripsi Aktivitas</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($activities)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($activity['description']) ?></td>
                                        <td><?= esc($activity['ip_address']) ?></td>
                                        <td>
                                            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="<?= esc($activity['user_agent']) ?>">
                                                <?= character_limiter(esc($activity['user_agent']), 50) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y, H:i:s', strtotime($activity['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <p class="my-4">Tidak ada aktivitas yang tercatat untuk pengguna ini.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('neptune-assets/plugins/datatables/datatables.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#activity-log-table').DataTable({
            "order": [
                [4, "desc"]
            ], // Urutkan berdasarkan kolom timestamp terbaru
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.22/i18n/Indonesian.json"
            }
        });
    });
</script>
<?= $this->endSection() ?>