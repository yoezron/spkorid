<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <img src="<?= base_url('images/logo-spk.png') ?>" alt="SPK Logo" class="auth-logo">
            <h2>Login SPK</h2>
            <p>Masuk ke akun Serikat Pekerja Kampus Anda</p>
        </div>

        <form action="<?= base_url('login') ?>" method="POST" class="auth-form">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="<?= old('email') ?>"
                        placeholder="Masukkan email Anda"
                        required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required>
                    <button type="button" class="btn-toggle-password" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" id="remember" name="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>

        <div class="auth-footer">
            <p>Belum punya akun? <a href="<?= base_url('register') ?>">Daftar sekarang</a></p>
            <p><a href="<?= base_url('forgot-password') ?>">Lupa password?</a></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>