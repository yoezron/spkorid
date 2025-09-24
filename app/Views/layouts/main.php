<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive Admin Dashboard Template">
    <meta name="keywords" content="admin,dashboard">
    <meta name="author" content="stacks">

    <title>Neptune | <?= $this->renderSection('title') ?></title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">

    <link href="<?= base_url('neptune-assets/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('neptune-assets/plugins/perfectscroll/perfect-scrollbar.css') ?>" rel="stylesheet">
    <link href="<?= base_url('neptune-assets/plugins/pace/pace.css') ?>" rel="stylesheet">


    <link href="<?= base_url('neptune-assets/css/main.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('neptune-assets/css/custom.css') ?>" rel="stylesheet">

    <?= $this->renderSection('pageStyles') ?>

    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('neptune-assets/images/neptune.png') ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('neptune-assets/images/neptune.png') ?>" />

</head>

<body>
    <div class="app align-content-stretch d-flex flex-wrap">
        <div class="app-sidebar">
            <div class="logo">
                <a href="index.html" class="logo-icon"><span class="logo-text">Neptune</span></a>
                <div class="sidebar-user-switcher user-activity-online">
                    <a href="#">
                        <img src="<?= base_url('neptune-assets/images/avatars/avatar.png') ?>">
                        <span class="activity-indicator"></span>
                        <span class="user-info-text">Chloe<br><span class="user-state-info">On a call</span></span>
                    </a>
                </div>
            </div>
            <div class="app-menu">
                <ul class="accordion-menu">
                    <li class="sidebar-title">
                        Apps
                    </li>
                    <li class="active-page">
                        <a href="index.html" class="active"><i class="material-icons-two-tone">dashboard</i>Dashboard</a>
                    </li>
                    <li>
                        <a href="mailbox.html"><i class="material-icons-two-tone">inbox</i>Mailbox<span class="badge rounded-pill badge-danger float-end">87</span></a>
                    </li>
                    <li>
                        <a href="file-manager.html"><i class="material-icons-two-tone">cloud_queue</i>File Manager</a>
                    </li>
                    <li>
                        <a href="calendar.html"><i class="material-icons-two-tone">calendar_today</i>Calendar<span class="badge rounded-pill badge-success float-end">14</span></a>
                    </li>
                    <li>
                        <a href="todo.html"><i class="material-icons-two-tone">done</i>Todo</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="app-container">
            <div class="search">
                <form>
                    <input class="form-control" type="text" placeholder="Type here..." aria-label="Search">
                </form>
                <a href="#" class="toggle-search"><i class="material-icons">close</i></a>
            </div>
            <div class="app-header">
                <nav class="navbar navbar-light navbar-expand-lg">
                    <div class="container-fluid">
                        <div class="navbar-nav" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link hide-sidebar-toggle-button" href="#"><i class="material-icons">first_page</i></a>
                                </li>
                            </ul>

                        </div>
                        <div class="d-flex">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link toggle-search" href="#"><i class="material-icons">search</i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="app-content">
                <div class="content-wrapper">
                    <div class="container">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('neptune-assets/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/plugins/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/plugins/perfectscroll/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/plugins/pace/pace.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/plugins/apexcharts/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/js/main.min.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/js/custom.js') ?>"></script>
    <script src="<?= base_url('neptune-assets/js/pages/dashboard.js') ?>"></script>

    <?= $this->renderSection('pageScripts') ?>
</body>

</html>