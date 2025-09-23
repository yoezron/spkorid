<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Serikat Pekerja Kampus</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="<?= base_url('plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet" type="text/css" />

    <style>
        body {
            background-color: #f1f2f3;
        }

        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .1);
        }

        .hero-section {
            background: linear-gradient(135deg, #4361ee 0%, #303F9F 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
        }

        .hero-section p {
            font-size: 1.25rem;
            max-width: 600px;
            margin: 15px auto 30px auto;
        }

        .section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-weight: 600;
        }

        .feature-card {
            background: #fff;
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px -10px #888ea8;
        }

        .feature-card svg {
            width: 50px;
            height: 50px;
            color: #4361ee;
            margin-bottom: 20px;
        }

        .blog-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .blog-card:hover {
            transform: translateY(-5px);
        }

        .blog-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .blog-card-content {
            padding: 20px;
        }

        .blog-card-content h5 a {
            color: #3b3f5c;
            font-weight: 700;
        }

        .footer {
            background-color: #1a2942;
            color: white;
            padding: 40px 0;
        }
    </style>
</head>

<body>

    <header class="header-container">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="<?= base_url() ?>"><b>SPK</b></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('blog') ?>">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Kontak</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-outline-primary ml-2" href="<?= base_url('login') ?>">Login</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-primary ml-2" href="<?= base_url('register') ?>">Daftar</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <section class="hero-section">
        <div class="container">
            <h1>Bersama Lebih Kuat</h1>
            <p>Mewujudkan kesejahteraan dan keadilan bagi seluruh pekerja di lingkungan kampus melalui solidaritas dan perjuangan bersama.</p>
            <a href="<?= base_url('register') ?>" class="btn btn-lg btn-light">Bergabung Sekarang</a>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2 class="section-title">Kenapa Bergabung?</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <h5>Advokasi & Perlindungan</h5>
                        <p>Kami memberikan bantuan hukum dan perlindungan bagi anggota yang menghadapi masalah ketenagakerjaan.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <h5>Solidaritas & Jaringan</h5>
                        <p>Bangun koneksi dengan sesama pekerja kampus, berbagi pengalaman, dan saling menguatkan.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                        <h5>Peningkatan Kesejahteraan</h5>
                        <p>Kami berjuang untuk upah yang adil, kondisi kerja yang layak, dan jaminan sosial bagi semua.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background-color: #fff;">
        <div class="container">
            <h2 class="section-title">Bacaan & Berita Terbaru</h2>
            <div class="row">
                <?php if (!empty($latest_posts)): ?>
                    <?php foreach ($latest_posts as $post): ?>
                        <div class="col-md-4 mb-4">
                            <div class="blog-card">
                                <img src="<?= base_url($post['featured_image'] ?? 'assets/images/widgets/widget.png') ?>" alt="<?= esc($post['title']) ?>">
                                <div class="blog-card-content">
                                    <span class="badge badge-primary mb-2"><?= esc($post['category']) ?></span>
                                    <h5><a href="<?= base_url('blog/view/' . $post['slug']) ?>"><?= esc($post['title']) ?></a></h5>
                                    <p><?= esc(character_limiter($post['excerpt'], 100)) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center col-12">Belum ada artikel terbaru.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Serikat Pekerja Kampus. Seluruh Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script src="<?= base_url('plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace()
    </script>
</body>

</html>