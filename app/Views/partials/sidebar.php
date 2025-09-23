<div class="sidebar-wrapper sidebar-theme">
    <nav id="sidebar">
        <div class="navbar-nav theme-brand flex-row  text-center">
            <div class="nav-logo">
                <div class="nav-item theme-logo">
                    <a href="<?= base_url('dashboard') ?>">
                        <img src="<?= base_url('assets/img/logo.svg') ?>" class="navbar-logo" alt="logo">
                    </a>
                </div>
                <div class="nav-item theme-text">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link"> SPK </a>
                </div>
            </div>
            <div class="nav-item sidebar-toggle">
                <div class="btn-toggle sidebarCollapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left">
                        <polyline points="11 17 6 12 11 7"></polyline>
                        <polyline points="18 17 13 12 18 7"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <ul class="list-unstyled menu-categories" id="accordionExample">
            <?php
            // Mengambil role_id dari session
            $roleId = session()->get('role_id');

            // Inisialisasi MenuModel
            $menuModel = new \App\Models\MenuModel();

            // Membangun menu berdasarkan role
            $menus = $menuModel->buildMenuTree($roleId);

            // Fungsi untuk menentukan apakah menu atau submenu aktif
            function is_active($menu_url)
            {
                $current_url = current_url(true);
                return strpos($current_url->getPath(), $menu_url) !== false;
            }

            foreach ($menus as $menu):
                // Cek apakah menu utama atau salah satu submenu-nya aktif
                $isActive = is_active($menu['menu_url']);
                $hasActiveSubmenu = false;
                if (!empty($menu['submenu'])) {
                    foreach ($menu['submenu'] as $sub) {
                        if (is_active($sub['menu_url'])) {
                            $hasActiveSubmenu = true;
                            break;
                        }
                    }
                }
            ?>
                <li class="menu <?= (!empty($menu['submenu']) && $hasActiveSubmenu) || $isActive ? 'active' : '' ?>">
                    <a href="<?= !empty($menu['submenu']) ? '#' . esc($menu['menu_name'], 'attr') : base_url($menu['menu_url']) ?>"
                        <?= !empty($menu['submenu']) ? 'data-toggle="collapse" aria-expanded="' . ($hasActiveSubmenu ? 'true' : 'false') . '"' : '' ?>
                        class="dropdown-toggle">
                        <div>
                            <i data-feather="<?= esc($menu['menu_icon']) ?>"></i>
                            <span><?= esc($menu['menu_name']) ?></span>
                        </div>
                        <?php if (!empty($menu['submenu'])): ?>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </a>

                    <?php if (!empty($menu['submenu'])): ?>
                        <ul class="collapse submenu list-unstyled <?= $hasActiveSubmenu ? 'show' : '' ?>"
                            id="<?= esc($menu['menu_name'], 'attr') ?>"
                            data-parent="#accordionExample">
                            <?php foreach ($menu['submenu'] as $sub): ?>
                                <li class="<?= is_active($sub['menu_url']) ? 'active' : '' ?>">
                                    <a href="<?= base_url($sub['menu_url']) ?>"> <?= esc($sub['menu_name']) ?> </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>