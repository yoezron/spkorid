<div class="app-sidebar">
    <div class="logo">
        <a href="<?= get_dashboard_url() ?>" class="logo-icon"><span class="logo-text">Neptune</span></a>
        <div class="sidebar-user-switcher user-activity-online">
            <a href="#">
                <img src="<?= base_url('neptune-assets/images/avatars/avatar.png') ?>">
                <span class="activity-indicator"></span>
                <span class="user-info-text"><?= esc(session()->get('nama_lengkap') ?? 'Guest') ?><br><span class="user-state-info"><?= esc(session()->get('role_name') ?? 'User') ?></span></span>
            </a>
        </div>
    </div>
    <div class="app-menu">
        <?php
        // Memuat helper jika belum dimuat
        helper('menu');

        // Merender menu dinamis dari helper Anda
        echo render_sidebar_menu();
        ?>
    </div>
</div>