<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="<?= base_url('images/logo-spk.png') ?>" alt="SPK Logo" class="sidebar-logo">
        <h3 class="sidebar-title">SPK</h3>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <?php
            $roleId = session()->get('role_id');
            $menuModel = new \App\Models\MenuModel();
            $menus = $menuModel->buildMenuTree($roleId);
            ?>

            <?php foreach ($menus as $menu): ?>
                <li class="nav-item <?= !empty($menu['submenu']) ? 'has-submenu' : '' ?>">
                    <?php if (!empty($menu['submenu'])): ?>
                        <a href="javascript:void(0)" class="nav-link" onclick="toggleSubmenu(this)">
                            <i class="<?= $menu['menu_icon'] ?>"></i>
                            <span><?= $menu['menu_name'] ?></span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <?php foreach ($menu['submenu'] as $sub): ?>
                                <li>
                                    <a href="<?= base_url($sub['menu_url']) ?>"
                                        class="<?= current_url() == base_url($sub['menu_url']) ? 'active' : '' ?>">
                                        <i class="<?= $sub['menu_icon'] ?>"></i>
                                        <span><?= $sub['menu_name'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <a href="<?= base_url($menu['menu_url']) ?>"
                            class="nav-link <?= current_url() == base_url($menu['menu_url']) ? 'active' : '' ?>">
                            <i class="<?= $menu['menu_icon'] ?>"></i>
                            <span><?= $menu['menu_name'] ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <img src="<?= session()->get('foto_path') ?? base_url('images/default-avatar.png') ?>"
                alt="User" class="user-avatar">
            <div class="user-details">
                <p class="user-name"><?= session()->get('nama_lengkap') ?></p>
                <p class="user-role"><?= session()->get('role_name') ?></p>
            </div>
        </div>
    </div>
</aside>