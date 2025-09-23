<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Login Anggota - Serikat Pekerja Kampus</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/favicon.png') ?>" />

    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="<?= base_url('plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/darktheme.css') ?>" rel="stylesheet" type="text/css" />

    <link href="<?= base_url('assets/css/auth.css') ?>" rel="stylesheet" type="text/css" />
</head>

<body class="login-one">

    <div class="container-fluid login-one-container">
        <div class="p-5">
            <div class="row login-one-container-inner">
                <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                    <img src="<?= base_url('assets/images/backgrounds/sign-in.svg') ?>" class="img-fluid" alt="SPK Illustration">
                    <h1 class="text-white mt-5">Serikat Pekerja Kampus</h1>
                    <p class="text-white">Bersama mewujudkan kesejahteraan & keadilan.</p>
                </div>
                <div class="col-md-6 login-one-right">

                    <div class="login-one-right-inner">
                        <p class="text-center text-muted mt-3 mb-3 font-14">Selamat Datang Kembali</p>
                        <h2 class="text-center mb-5">Login Anggota</h2>

                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger"><?= session('error') ?></div>
                        <?php endif; ?>
                        <?php if (session()->has('success')): ?>
                            <div class="alert alert-success"><?= session('success') ?></div>
                        <?php endif; ?>

                        <form action="<?= base_url('login') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" class="form-control" placeholder="Masukkan email Anda" value="<?= old('email') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input id="password" type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="remember" class="custom-control-input" id="remember-me">
                                    <label class="custom-control-label" for="remember-me">Ingat saya</label>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block" type="submit">Login</button>
                        </form>

                        <div class="text-center mt-4">
                            <p><a href="<?= base_url('forgot-password') ?>" class="text-primary">Lupa Password?</a></p>
                        </div>
                        <hr>
                        <div class="text-center">
                            <p>Belum punya akun? <a href="<?= base_url('register') ?>" class="text-primary">Daftar Sekarang</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>