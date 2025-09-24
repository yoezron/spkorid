<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Buat Pembayaran Iuran
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Pembayaran Iuran</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Pembayaran</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?= form_open_multipart('member/payment/store') ?>

                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah Pembayaran</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control <?= (validation_show_error('jumlah')) ? 'is-invalid' : '' ?>" id="jumlah" name="jumlah" value="<?= old('jumlah') ?>" placeholder="Contoh: 50000" required>
                        <?php if (validation_show_error('jumlah')): ?>
                            <div class="invalid-feedback">
                                <?= validation_show_error('jumlah') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-text">Masukkan nominal tanpa titik atau koma.</div>
                </div>

                <div class="mb-3">
                    <label for="tanggal_pembayaran" class="form-label">Tanggal Pembayaran</label>
                    <input type="date" class="form-control <?= (validation_show_error('tanggal_pembayaran')) ? 'is-invalid' : '' ?>" id="tanggal_pembayaran" name="tanggal_pembayaran" value="<?= old('tanggal_pembayaran', date('Y-m-d')) ?>" required>
                    <?php if (validation_show_error('tanggal_pembayaran')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('tanggal_pembayaran') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="bukti_pembayaran" class="form-label">Unggah Bukti Pembayaran</label>
                    <input class="form-control <?= (validation_show_error('bukti_pembayaran')) ? 'is-invalid' : '' ?>" type="file" id="bukti_pembayaran" name="bukti_pembayaran" required>
                    <?php if (validation_show_error('bukti_pembayaran')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('bukti_pembayaran') ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-text">File yang diizinkan: .jpg, .jpeg, .png, .pdf. Maksimal 2MB.</div>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="3"><?= old('catatan') ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Kirim Bukti Pembayaran</button>
                    <a href="<?= base_url('member/payment/history') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Petunjuk Pembayaran</h5>
                <p>Silakan lakukan transfer ke rekening berikut:</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Bank:</strong> Bank Central Asia (BCA)</li>
                    <li class="list-group-item"><strong>No. Rekening:</strong> 1234567890</li>
                    <li class="list-group-item"><strong>Atas Nama:</strong> Serikat Pekerja Kampus</li>
                </ul>
                <p class="mt-3">Setelah melakukan transfer, mohon isi formulir di samping dan unggah bukti transfer Anda untuk proses verifikasi.</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>