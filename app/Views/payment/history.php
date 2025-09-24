<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Riwayat Iuran
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description d-flex align-items-center">
            <h1 class="flex-grow-1">Riwayat Iuran</h1>
            <a href="<?= base_url('member/payment/create') ?>" class="btn btn-primary">
                <i class="material-icons-outlined">add</i>Buat Pembayaran
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nomor Invoice</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Tanggal Pembayaran</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($payments)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <th scope="row"><?= $i++ ?></th>
                                        <td><?= esc($payment['invoice_number']) ?></td>
                                        <td>Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                        <td><?= date('d F Y', strtotime($payment['tanggal_pembayaran'])) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'badge-secondary';
                                            if ($payment['status_pembayaran'] == 'verified') {
                                                $statusClass = 'badge-success';
                                            } elseif ($payment['status_pembayaran'] == 'pending') {
                                                $statusClass = 'badge-warning';
                                            } elseif ($payment['status_pembayaran'] == 'rejected') {
                                                $statusClass = 'badge-danger';
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= esc(ucfirst($payment['status_pembayaran'])) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('member/payment/invoice/' . $payment['id']) ?>" class="btn btn-sm btn-info">Lihat Invoice</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <p class="mt-4">Anda belum memiliki riwayat pembayaran.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <?= $pager->links('default', 'bootstrap_5') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>