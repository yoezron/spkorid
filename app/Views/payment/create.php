<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Lakukan Pembayaran
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/file-upload/file-upload-with-preview.min.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('plugins/flatpickr/flatpickr.css') ?>" rel="stylesheet" type="text/css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row layout-spacing">
        <div class="col-lg-8 col-md-10 col-sm-12 layout-top-spacing m-auto">
            <div class="widget-content widget-content-area br-6 p-4">
                <h4>Formulir Pembayaran Iuran</h4>
                <p>Silakan isi detail pembayaran dan unggah bukti transfer Anda. Pembayaran akan diverifikasi oleh pengurus dalam 1-2 hari kerja.</p>
                <hr>

                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <form action="<?= base_url('member/payment/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="jenis_pembayaran">Jenis Pembayaran</label>
                            <select class="form-control" name="jenis_pembayaran" id="jenis_pembayaran" required>
                                <option value="iuran_bulanan">Iuran Bulanan</option>
                                <option value="iuran_tahunan">Iuran Tahunan</option>
                                <option value="sumbangan">Sumbangan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="jumlah">Jumlah Pembayaran (Rp)</label>
                            <input type="number" class="form-control" name="jumlah" id="jumlah" placeholder="Contoh: 50000" value="<?= old('jumlah') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="periode_bulan">Untuk Periode Bulan</label>
                            <select class="form-control" name="periode_bulan" id="periode_bulan">
                                <?php for ($m = 1; $m <= 12; ++$m): ?>
                                    <option value="<?= $m ?>" <?= (old('periode_bulan', date('m')) == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="periode_tahun">Untuk Periode Tahun</label>
                            <select class="form-control" name="periode_tahun" id="periode_tahun">
                                <?php for ($y = date('Y'); $y >= date('Y') - 5; --$y): ?>
                                    <option value="<?= $y ?>" <?= (old('periode_tahun', date('Y')) == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_pembayaran">Tanggal Pembayaran / Transfer</label>
                        <input id="tanggal_pembayaran" name="tanggal_pembayaran" class="form-control flatpickr" type="text" placeholder="Pilih tanggal.." value="<?= old('tanggal_pembayaran', date('Y-m-d')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Unggah Bukti Pembayaran</label>
                        <div class="custom-file-container" data-upload-id="proofOfPayment">
                            <label>Pilih File <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                            <label class="custom-file-container__custom-file">
                                <input type="file" name="bukti_pembayaran" class="custom-file-container__custom-file__custom-file-input" accept="image/*" required>
                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                            </label>
                            <div class="custom-file-container__image-preview"></div>
                        </div>
                        <small>Format file: JPG, PNG, atau GIF. Ukuran maksimal: 10MB.</small>
                    </div>


                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Kirim Bukti Pembayaran</button>
                        <a href="<?= base_url('member/payment/history') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/file-upload/file-upload-with-preview.min.js') ?>"></script>
<script src="<?= base_url('plugins/flatpickr/flatpickr.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi upload gambar
        new FileUploadWithPreview('proofOfPayment');

        // Inisialisasi date picker
        flatpickr("#tanggal_pembayaran", {
            defaultDate: "today"
        });
    });
</script>
<?= $this->endSection() ?>