<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Serikat Pekerja Kampus">
    <meta name="keywords" content="admin,dashboard,serikat pekerja">
    <meta name="author" content="stacks">

    <title>SPK | <?= $this->renderSection('title') ?></title>

    <!-- STYLES -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">

    <link href="<?= base_url('neptune-assets/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('neptune-assets/plugins/perfectscroll/perfect-scrollbar.css') ?>" rel="stylesheet">
    <link href="<?= base_url('neptune-assets/plugins/pace/pace.css') ?>" rel="stylesheet">

    <!-- THEME STYLES -->
    <link href="<?= base_url('neptune-assets/css/main.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('neptune-assets/css/custom.css') ?>" rel="stylesheet">

    <!-- PAGE-SPECIFIC STYLES -->
    <?= $this->renderSection('pageStyles') ?>

    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('neptune-assets/images/neptune.png') ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('neptune-assets/images/neptune.png') ?>" />
</head>

<body>
    <div class="app align-content-stretch d-flex flex-wrap">

        <!-- Load Sidebar -->
        <?= $this->include('partials/sidebar') ?>

        <div class="app-container">

            <!-- Load Navbar -->
            <?= $this->include('partials/navbar') ?>

            <!-- Main Content Area -->
            <div class="app-content">
                <div class="content-wrapper">
                    <div class="container-fluid">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPTS -->
    <script src="<?= base_url('neptune-assets/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/plugins/perfectscroll/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/plugins/pace/pace.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/js/main.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/js/custom.js') ?>"></script>

    <!-- PAGE-SPECIFIC SCRIPTS -->
    <?= $this->renderSection('pageScripts') ?>
</body>

</html>