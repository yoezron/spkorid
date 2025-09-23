<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $this->renderSection('meta_description') ?>">
    <title><?= $title ?? 'Serikat Pekerja Kampus' ?></title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= base_url('css/main.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?= $this->renderSection('styles') ?>
</head>

<body class="<?= session()->get('logged_in') ? 'logged-in' : 'guest' ?>">

    <?php if (session()->get('logged_in')): ?>
        <!-- Sidebar -->
        <?= $this->include('partials/sidebar') ?>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper">
            <!-- Top Navbar -->
            <?= $this->include('partials/navbar') ?>

            <!-- Content -->
            <main class="content-area">
                <?= $this->include('partials/breadcrumb') ?>
                <?= $this->include('partials/flash_messages') ?>
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Footer -->
            <?= $this->include('partials/footer') ?>
        </div>
    <?php else: ?>
        <!-- Guest Layout -->
        <?= $this->include('partials/guest_header') ?>
        <main class="guest-content">
            <?= $this->include('partials/flash_messages') ?>
            <?= $this->renderSection('content') ?>
        </main>
        <?= $this->include('partials/guest_footer') ?>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="<?= base_url('js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>