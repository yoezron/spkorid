<?php
helper('photo');
// Ambil data user dari session untuk digunakan di navbar
$nama_lengkap = session()->get('nama_lengkap') ?? 'Anggota';
$email = session()->get('email') ?? 'email@example.com';
$defaultPhoto = 'neptune-assets/images/avatars/avatar.png';
$fotoPath = session()->get('foto_path');

$navbarPhotoUrl = resolve_user_photo_url($fotoPath, $defaultPhoto);
?>

<div class="app-header">
    <nav class="navbar navbar-light navbar-expand-lg">
        <div class="container-fluid">
            <div class="navbar-nav" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link hide-sidebar-toggle-button" href="#"><i class="material-icons">first_page</i></a>
                    </li>
                    <li class="nav-item hidden-on-mobile">
                        <a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item hidden-on-mobile">
                        <a class="nav-link" href="<?= base_url('member/profile') ?>">Profil Saya</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex">
                <ul class="navbar-nav">

                    <!-- Tombol Search -->
                    <li class="nav-item">
                        <a class="nav-link toggle-search" href="#"><i class="material-icons">search</i></a>
                    </li>

                    <!-- Dropdown Notifikasi -->
                    <li class="nav-item hidden-on-mobile">
                        <a class="nav-link nav-notifications-toggle" id="notificationsDropDown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="material-icons-outlined">notifications</i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropDown">
                            <h6 class="dropdown-header">Notifikasi</h6>
                            <div class="notifications-dropdown-list">
                                <!-- Contoh Notifikasi 1 -->
                                <a href="#">
                                    <div class="notifications-dropdown-item">
                                        <div class="notifications-dropdown-item-image">
                                            <span class="notifications-badge bg-info text-white">
                                                <i class="material-icons-outlined">campaign</i>
                                            </span>
                                        </div>
                                        <div class="notifications-dropdown-item-text">
                                            <p class="bold-text">Selamat datang di Sistem Keanggotaan!</p>
                                            <small>Baru saja</small>
                                        </div>
                                    </div>
                                </a>
                                <!-- Contoh Notifikasi 2 -->
                                <a href="#">
                                    <div class="notifications-dropdown-item">
                                        <div class="notifications-dropdown-item-image">
                                            <span class="notifications-badge bg-danger text-white">
                                                <i class="material-icons-outlined">bolt</i>
                                            </span>
                                        </div>
                                        <div class="notifications-dropdown-item-text">
                                            <p class="bold-text">Pembaruan sistem akan segera dilakukan.</p>
                                            <small>15 menit yang lalu</small>
                                        </div>
                                    </div>
                                </a>
                                <!-- Contoh Notifikasi 3 -->
                                <a href="#">
                                    <div class="notifications-dropdown-item">
                                        <div class="notifications-dropdown-item-image">
                                            <span class="notifications-badge bg-success text-white">
                                                <i class="material-icons-outlined">payment</i>
                                            </span>
                                        </div>
                                        <div class="notifications-dropdown-item-text">
                                            <p>Iuran bulan ini telah terverifikasi.</p>
                                            <small>2 jam yang lalu</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- Dropdown Profil Pengguna -->
                    <li class="nav-item">
                        <a class="nav-link" id="profileDropDown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= esc($navbarPhotoUrl) ?>" alt="profile image" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="profileDropDown">
                            <div class="dropdown-header">
                                <h6><?= esc($nama_lengkap) ?></h6>
                                <span><?= esc($email) ?></span>
                            </div>
                            <a class="dropdown-item" href="<?= base_url('member/profile') ?>">
                                <i class="material-icons-outlined">person_outline</i> Profil
                            </a>
                            <a class="dropdown-item" href="<?= base_url('member/profile/edit') ?>">
                                <i class="material-icons-outlined">edit</i> Edit Profil
                            </a>
                            <a class="dropdown-item" href="<?= base_url('member/change-password') ?>">
                                <i class="material-icons-outlined">lock</i> Ubah Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                <i class="material-icons-outlined">logout</i> Keluar
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>