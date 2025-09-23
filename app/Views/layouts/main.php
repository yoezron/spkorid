<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title><?= esc($title ?? 'Dashboard - Serikat Pekerja Kampus') ?></title>

    <link rel="icon" type="image/x-icon" href="<?= base_url('images/favicon.ico') ?>">

    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet" type="text/css" />
    <?= $this->renderSection('styles') ?>
</head>

<body class="layout-boxed">

    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <?php if (session()->get('logged_in')): ?>

            <?= $this->include('partials/sidebar') ?>
            <div id="content" class="main-content">
                <div class="layout-px-spacing">
                    <div class="page-header">
                        <div class="page-title">
                            <h3><?= esc($title ?? 'Dashboard') ?></h3>
                        </div>
                    </div>

                    <?= $this->include('partials/flash_messages') ?>

                    <?= $this->renderSection('content') ?>
                </div>

                <?= $this->include('partials/footer') ?>
            </div>
        <?php else: ?>

            <?= $this->include('partials/guest_header') ?>
            <div class="guest-content">
                <?= $this->include('partials/flash_messages') ?>
                <?= $this->renderSection('content') ?>
            </div>
            <?= $this->include('partials/guest_footer') ?>

        <?php endif; ?>

    </div>
    <script src="<?= base_url('assets/js/libs/jquery-3.1.1.min.js') ?>"></script>
    <script src="<?= base_url('bootstrap/js/popper.min.js') ?>"></script>
    <script src="<?= base_url('bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('plugins/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <script>
        $(document).ready(function() {
            App.init();
        });
    </script>
    <script src="<?= base_url('assets/js/custom.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>