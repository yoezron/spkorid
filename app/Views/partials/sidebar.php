<div class="sidebar-wrapper sidebar-theme">
    <nav id="sidebar">
        <ul class="list-unstyled menu-categories" id="accordionExample">
            <li class="menu-title">Menu Utama</li>

            <?php
            // Fungsi helper untuk memeriksa apakah menu aktif.
            // Bisa tetap di sini atau dipindahkan ke file helper jika lebih rapi.
            if (!function_exists('is_active')) {
                function is_active($menu_url)
                {
                    if (empty($menu_url) || $menu_url == '#') return false;
                    $current_url = current_url(true);
                    // Hapus index.php dari path jika ada
                    $currentPath = str_replace('index.php/', '', $current_url->getPath());
                    return strpos($currentPath, ltrim($menu_url, '/')) !== false;
                }
            }

            // 1. Cek apakah variabel $sidebar_menus sudah ada (disediakan oleh BaseController)
            if (isset($sidebar_menus)) :
                foreach ($sidebar_menus as $menu) :
                    $hasActiveSubmenu = false;
                    if (!empty($menu['submenu'])) {
                        foreach ($menu['submenu'] as $sub) {
                            if (is_active($sub['menu_url'])) {
                                $hasActiveSubmenu = true;
                                break;
                            }
                        }
                    }
                    $isActive = $hasActiveSubmenu || is_active($menu['menu_url']);
            ?>
                    <li class="menu <?= $isActive ? 'active' : '' ?>">
                        <a href="<?= !empty($menu['submenu']) ? '#' . esc(str_replace(' ', '', $menu['menu_name'])) : base_url($menu['menu_url']) ?>" <?= !empty($menu['submenu']) ? 'data-toggle="collapse" aria-expanded="' . ($hasActiveSubmenu ? 'true' : 'false') . '"' : '' ?> class="dropdown-toggle">
                            <div>
                                <i class="las la-<?= esc($menu['menu_icon']) ?>"></i>
                                <span><?= esc($menu['menu_name']) ?></span>
                            </div>
                            <?php if (!empty($menu['submenu'])) : ?>
                                <div>
                                    <i class="las la-angle-right sidemenu-right-icon"></i>
                                </div>
                            <?php endif; ?>
                        </a>

                        <?php if (!empty($menu['submenu'])) : ?>
                            <ul class="collapse submenu list-unstyled <?= $hasActiveSubmenu ? 'show' : '' ?>" id="<?= esc(str_replace(' ', '', $menu['menu_name'])) ?>" data-parent="#accordionExample">
                                <?php foreach ($menu['submenu'] as $sub) : ?>
                                    <li class="<?= is_active($sub['menu_url']) ? 'active' : '' ?>">
                                        <a href="<?= base_url($sub['menu_url']) ?>"> <?= esc($sub['menu_name']) ?> </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
            <?php
                endforeach;
            endif;
            ?>
        </ul>
    </nav>
</div>