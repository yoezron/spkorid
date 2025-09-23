<nav class="navbar">
    <div class="navbar-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title"><?= $title ?? 'Dashboard' ?></h1>
    </div>

    <div class="navbar-right">
        <!-- Notifications -->
        <div class="navbar-item dropdown">
            <button class="navbar-btn" onclick="toggleDropdown('notifications')">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </button>
            <div class="dropdown-menu" id="notifications-dropdown">
                <div class="dropdown-header">Notifikasi</div>
                <div class="dropdown-body">
                    <a href="#" class="notification-item">
                        <i class="fas fa-info-circle text-info"></i>
                        <div>
                            <p>Pengumuman rapat bulanan</p>
                            <small>2 jam yang lalu</small>
                        </div>
                    </a>
                </div>
                <div class="dropdown-footer">
                    <a href="<?= base_url('notifications') ?>">Lihat Semua</a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="navbar-item dropdown">
            <button class="navbar-btn user-menu" onclick="toggleDropdown('user')">
                <img src="<?= session()->get('foto_path') ?? base_url('images/default-avatar.png') ?>"
                    alt="User" class="navbar-avatar">
                <span><?= session()->get('nama_lengkap') ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu" id="user-dropdown">
                <a href="<?= base_url('profile') ?>" class="dropdown-item">
                    <i class="fas fa-user"></i> Profil Saya
                </a>
                <a href="<?= base_url('change-password') ?>" class="dropdown-item">
                    <i class="fas fa-key"></i> Ubah Password
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('logout') ?>" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>