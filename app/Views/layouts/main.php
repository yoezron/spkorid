<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title><?= esc($title ?? 'Dashboard - Serikat Pekerja Kampus') ?></title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/favicon.png') ?>" />

    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="<?= base_url('plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/darktheme.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('plugins/perfect-scrollbar/perfect-scrollbar.css') ?>" rel="stylesheet" type="text/css" />

    <?= $this->renderSection('styles') ?>
</head>

<body class="layout-static"> <?= $this->include('partials/navbar') ?>
    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <?= $this->include('partials/sidebar') ?>
        <div id="content" class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h3><?= esc($title ?? 'Dashboard') ?></h3>
                </div>
            </div>
            <div class="container">
                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
                        <?= $this->include('partials/flash_messages') ?>
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </div>
            <?= $this->include('partials/footer') ?>
        </div>
    </div>
    <script src="<?= base_url('plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('plugins/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/custom.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>