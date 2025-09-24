<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Anggota
<?= $this->endSection() ?>

<?= $this->section('pageStyles') ?>
<link href="<?= base_url('neptune-assets/plugins/datatables/datatables.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description d-flex align-items-center">
            <h1 class="flex-grow-1">Manajemen Anggota</h1>
            <a href="<?= base_url('admin/members/create') ?>" class="btn btn-primary">
                <i class="material-icons-outlined">add</i>Tambah Anggota Baru
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>
                <div class="table-responsive">
                    <table id="members-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Lengkap</th>
                                <th>Nomor Anggota</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Tanggal Bergabung</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($members)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($member['nama_lengkap']) ?></td>
                                        <td><?= esc($member['nomor_anggota']) ?></td>
                                        <td><?= esc($member['email']) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'badge-secondary';
                                            if ($member['status_keanggotaan'] == 'active') {
                                                $statusClass = 'badge-success';
                                            } elseif ($member['status_keanggotaan'] == 'pending') {
                                                $statusClass = 'badge-warning';
                                            } elseif ($member['status_keanggotaan'] == 'suspended') {
                                                $statusClass = 'badge-danger';
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= esc(ucfirst($member['status_keanggotaan'])) ?></span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($member['tanggal_bergabung'])) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                                <a href="<?= base_url('admin/members/edit/' . $member['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
        $('#members-table').DataTable();
    });
</script>
<?= $this->endSection() ?>