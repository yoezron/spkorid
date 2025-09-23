<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Ubah Password
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="layout-px-spacing">
    <div class="row layout-spacing">
        <div class="col-lg-8 col-md-10 col-sm-12 layout-top-spacing m-auto">
            <div class="widget-content widget-content-area br-6 p-4">
                <h4>Formulir Ubah Password</h4>
                <p>Untuk keamanan, ganti password Anda secara berkala.</p>
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

                <form action="<?= base_url('member/update-password') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" class="form-control" name="current_password" id="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
                        <a href="<?= base_url('member/profile') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>