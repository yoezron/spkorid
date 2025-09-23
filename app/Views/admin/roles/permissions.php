<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Hak Akses Role: <?= esc($role['role_name']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/css/forms/switches.css') ?>" rel="stylesheet" type="text/css" />
<style>
    .permission-table th,
    .permission-table td {
        vertical-align: middle;
    }

    .permission-table .submenu-row td {
        padding-left: 40px;
        background-color: #f9f9f9;
    }

    .permission-table .menu-group-header {
        background-color: #eaf1ff;
        font-weight: bold;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Kelola Hak Akses untuk Role "<?= esc(ucwords(str_replace('_', ' ', $role['role_name']))) ?>"</h4>
                <p>Centang hak akses yang ingin Anda berikan untuk setiap menu.</p>
                <hr>

                <form action="<?= base_url('admin/roles/update-permissions/' . $role['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover permission-table">
                            <thead>
                                <tr>
                                    <th>Nama Menu</th>
                                    <th class="text-center">Lihat</th>
                                    <th class="text-center">Tambah</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Helper untuk mencari permission yang sudah ada
                                function find_permission($permissions, $menu_id)
                                {
                                    foreach ($permissions as $p) {
                                        if ($p['menu_id'] == $menu_id) {
                                            return $p;
                                        }
                                    }
                                    return null;
                                }

                                foreach ($all_menus as $menu):
                                    $permission = find_permission($role_permissions, $menu['id']);
                                ?>
                                    <tr class="menu-group-header">
                                        <td><strong><?= esc($menu['menu_name']) ?></strong></td>
                                        <td class="text-center">
                                            <label class="switch s-icons s-outline s-outline-success">
                                                <input type="checkbox" name="permissions[<?= $menu['id'] ?>][can_view]" value="1" <?= ($permission && $permission['can_view']) ? 'checked' : '' ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="switch s-icons s-outline s-outline-success">
                                                <input type="checkbox" name="permissions[<?= $menu['id'] ?>][can_add]" value="1" <?= ($permission && $permission['can_add']) ? 'checked' : '' ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="switch s-icons s-outline s-outline-success">
                                                <input type="checkbox" name="permissions[<?= $menu['id'] ?>][can_edit]" value="1" <?= ($permission && $permission['can_edit']) ? 'checked' : '' ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="switch s-icons s-outline s-outline-success">
                                                <input type="checkbox" name="permissions[<?= $menu['id'] ?>][can_delete]" value="1" <?= ($permission && $permission['can_delete']) ? 'checked' : '' ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php if (!empty($menu['submenu'])): ?>
                                        <?php foreach ($menu['submenu'] as $submenu):
                                            $sub_permission = find_permission($role_permissions, $submenu['id']);
                                        ?>
                                            <tr class="submenu-row">
                                                <td><i data-feather="corner-down-right" class="mr-2"></i> <?= esc($submenu['menu_name']) ?></td>
                                                <td class="text-center">
                                                    <label class="switch s-icons s-outline s-outline-success">
                                                        <input type="checkbox" name="permissions[<?= $submenu['id'] ?>][can_view]" value="1" <?= ($sub_permission && $sub_permission['can_view']) ? 'checked' : '' ?>>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </td>
                                                <td class="text-center">
                                                    <label class="switch s-icons s-outline s-outline-success">
                                                        <input type="checkbox" name="permissions[<?= $submenu['id'] ?>][can_add]" value="1" <?= ($sub_permission && $sub_permission['can_add']) ? 'checked' : '' ?>>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </td>
                                                <td class="text-center">
                                                    <label class="switch s-icons s-outline s-outline-success">
                                                        <input type="checkbox" name="permissions[<?= $submenu['id'] ?>][can_edit]" value="1" <?= ($sub_permission && $sub_permission['can_edit']) ? 'checked' : '' ?>>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </td>
                                                <td class="text-center">
                                                    <label class="switch s-icons s-outline s-outline-success">
                                                        <input type="checkbox" name="permissions[<?= $submenu['id'] ?>][can_delete]" value="1" <?= ($sub_permission && $sub_permission['can_delete']) ? 'checked' : '' ?>>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Hak Akses</button>
                        <a href="<?= base_url('admin/roles') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>