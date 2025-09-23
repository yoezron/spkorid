<div class="header-container">
    <header class="header navbar navbar-expand-sm">
        <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
            <i class="las la-bars"></i>
        </a>
        <div class="nav-logo align-self-center">
            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
                <img alt="logo" src="<?= base_url('assets/images/neptune.png') ?>">
                <span class="navbar-brand-name">SPK</span>
            </a>
        </div>
        <ul class="navbar-item flex-row nav-dropdowns ml-auto">
            <li class="nav-item dropdown user-profile-dropdown">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?= base_url(session()->get('foto_path') ?? 'assets/images/avatars/avatar.png') ?>" alt="avatar">
                </a>
                <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                    <div class="user-profile-section">
                        <div class="media mx-auto">
                            <img src="<?= base_url(session()->get('foto_path') ?? 'assets/images/avatars/avatar.png') ?>" class="img-fluid mr-2" alt="avatar">
                            <div class="media-body">
                                <h5><?= esc(session()->get('nama_lengkap')) ?></h5>
                                <p><?= esc(ucwords(str_replace('_', ' ', session()->get('role_name')))) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?= base_url('member/profile') ?>">
                            <i class="las la-user"></i> <span>Profil Saya</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?= base_url('logout') ?>">
                            <i class="las la-sign-out-alt"></i> <span>Logout</span>
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </header>
</div>