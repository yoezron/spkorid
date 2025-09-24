<?php

/**
 * Sidebar Navigation Partial - Neptune Theme
 * File: app/Views/partials/sidebar.php
 * 
 * Dynamic sidebar yang sesuai dengan Neptune Light Theme
 */

// Get current user info from session
$session = session();
$roleId = $session->get('role_id');
$userName = $session->get('nama_lengkap') ?? $session->get('username') ?? 'User';
$userEmail = $session->get('email');
$userPhoto = $session->get('foto_path') ?? 'neptune-assets/images/avatars/avatar.png';
$roleName = $session->get('role_name') ?? 'User';

// Load menu helper
helper('menu');

// Get menu from database or fallback
$menuItems = generate_sidebar_menu();
if (empty($menuItems)) {
    $menuItems = get_menu_by_role($roleId);
}

// Get current URL for active menu detection
$currentUrl = current_url();
?>

<!-- Neptune Sidebar -->
<div class="app-sidebar">
    <div class="logo">
        <a href="<?= get_dashboard_url() ?>" class="logo-icon">
            <span class="logo-text">SPK-ORID</span>
        </a>
        <div class="sidebar-user-switcher user-activity-online">
            <a href="<?= base_url('profile') ?>">
                <img src="<?= base_url($userPhoto) ?>" alt="User Avatar">
                <span class="activity-indicator"></span>
                <span class="user-info-text">
                    <?= esc($userName) ?><br>
                    <span class="user-state-info"><?= esc($roleName) ?></span>
                </span>
            </a>
        </div>
    </div>

    <div class="app-menu">
        <ul class="accordion-menu">
            <li class="sidebar-title">
                <span>Menu Utama</span>
            </li>

            <?php if (!empty($menuItems)): ?>
                <?php foreach ($menuItems as $menu): ?>
                    <?php
                    $hasSubmenu = !empty($menu['submenus']);
                    $menuUrl = $hasSubmenu ? '#' : base_url($menu['url']);
                    $isActive = strpos($currentUrl, base_url($menu['url'])) !== false;

                    // Jika punya submenu, cek apakah ada submenu yang aktif
                    if ($hasSubmenu) {
                        foreach ($menu['submenus'] as $submenu) {
                            if (strpos($currentUrl, base_url($submenu['url'])) !== false) {
                                $isActive = true;
                                break;
                            }
                        }
                    }
                    ?>

                    <li class="<?= $isActive ? 'active-page' : '' ?>">
                        <a href="<?= $menuUrl ?>" class="<?= $isActive ? 'active' : '' ?>">
                            <i class="material-icons-two-tone"><?= $menu['icon'] ?? 'circle' ?></i>
                            <?= esc($menu['title']) ?>
                            <?php if ($hasSubmenu): ?>
                                <i class="material-icons has-sub-menu">keyboard_arrow_right</i>
                            <?php endif; ?>
                        </a>

                        <?php if ($hasSubmenu): ?>
                            <ul class="sub-menu">
                                <?php foreach ($menu['submenus'] as $submenu): ?>
                                    <?php
                                    $submenuUrl = base_url($submenu['url']);
                                    $isSubActive = strpos($currentUrl, $submenuUrl) !== false;
                                    ?>
                                    <li class="<?= $isSubActive ? 'active' : '' ?>">
                                        <a href="<?= $submenuUrl ?>" class="<?= $isSubActive ? 'active' : '' ?>">
                                            <?= esc($submenu['title']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>
                    <a href="<?= base_url('dashboard') ?>">
                        <i class="material-icons-two-tone">dashboard</i>
                        Dashboard
                    </a>
                </li>
            <?php endif; ?>

            <li class="sidebar-title">
                <span>Akun</span>
            </li>

            <li>
                <a href="<?= base_url('profile') ?>">
                    <i class="material-icons-two-tone">account_circle</i>
                    Profil Saya
                </a>
            </li>

            <li>
                <a href="<?= base_url('change-password') ?>">
                    <i class="material-icons-two-tone">lock</i>
                    Ubah Password
                </a>
            </li>

            <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                <li>
                    <a href="<?= base_url('notifications') ?>">
                        <i class="material-icons-two-tone">notifications</i>
                        Notifikasi
                        <span class="badge rounded-pill badge-danger float-end"><?= $unreadNotifications ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?= base_url('logout') ?>" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="material-icons-two-tone">logout</i>
                    Keluar
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Initialize Neptune Sidebar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Neptune theme already handles sidebar functionality
        // This is just for custom additions if needed

        // Mark current page as active if not already marked
        const currentPath = window.location.pathname;
        const menuLinks = document.querySelectorAll('.app-menu a[href]');

        menuLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href !== '#' && currentPath.includes(href.replace('<?= base_url() ?>', ''))) {
                link.classList.add('active');
                const parentLi = link.closest('li');
                if (parentLi) {
                    parentLi.classList.add('active-page');

                    // Open parent menu if in submenu
                    const parentMenu = parentLi.closest('.sub-menu');
                    if (parentMenu) {
                        const parentLi = parentMenu.closest('li');
                        if (parentLi) {
                            parentLi.classList.add('open', 'active-page');
                        }
                    }
                }
            }
        });
    });
</script>

<!-- Additional Neptune Sidebar Styles (if needed) -->
<style>
    /* Custom styles for SPK-ORID branding */
    .app-sidebar .logo .logo-text {
        font-weight: 600;
        font-size: 18px;
    }

    .app-sidebar .sidebar-user-switcher {
        margin-top: 20px;
    }

    .app-sidebar .sidebar-title {
        padding: 20px 30px 10px 30px;
        margin: 0;
    }

    .app-sidebar .sidebar-title span {
        color: #a8afc7;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    /* Active menu styling untuk Neptune theme */
    .app-sidebar .accordion-menu li.active-page>a {
        color: #5a8dee;
        position: relative;
    }

    .app-sidebar .accordion-menu li.active-page>a:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #5a8dee;
        border-radius: 0 3px 3px 0;
    }

    /* Hover effect */
    .app-sidebar .accordion-menu li a:hover {
        color: #5a8dee;
    }

    /* Badge styling */
    .badge.badge-danger {
        background-color: #ea5455;
    }

    /* Mobile responsive - Neptune handles this but adding backup */
    @media (max-width: 1199px) {
        .app-sidebar {
            transform: translateX(-280px);
            transition: transform 0.3s ease;
        }

        .app.sidebar-mobile-open .app-sidebar {
            transform: translateX(0);
        }
    }
</style>