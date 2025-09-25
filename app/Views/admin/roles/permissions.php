<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Hak Akses Role: <?= esc($role['role_name']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Hak Akses Role: <span class="text-primary"><?= esc($role['role_name']) ?></span></h1>
            <p>Atur izin untuk setiap menu yang dapat diakses oleh role ini.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?= form_open('admin/roles/update-permissions/' . $role['id']) ?>
        <?= $this->include('partials/flash_messages') ?>

        <?php if (!empty($menus)): ?>
            <?php foreach ($menus as $menu): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="material-icons-outlined me-2"><?= esc($menu['menu_icon']) ?></i>
                            <?= esc($menu['menu_name']) ?>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%;">Sub-Menu</th>
                                        <th class="text-center" style="width: 15%;">
                                            <i class="material-icons-outlined">visibility</i><br>
                                            Lihat (View)
                                        </th>
                                        <th class="text-center" style="width: 15%;">
                                            <i class="material-icons-outlined">add</i><br>
                                            Tambah (Create)
                                        </th>
                                        <th class="text-center" style="width: 15%;">
                                            <i class="material-icons-outlined">edit</i><br>
                                            Ubah (Edit)
                                        </th>
                                        <th class="text-center" style="width: 15%;">
                                            <i class="material-icons-outlined">delete</i><br>
                                            Hapus (Delete)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Menu Induk -->
                                    <tr class="bg-light">
                                        <td class="fw-bold"><?= esc($menu['menu_name']) ?> (Menu Induk)</td>
                                        <?php
                                        $current_permissions = $permissions[$menu['id']] ?? [];
                                        ?>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="permissions[<?= $menu['id'] ?>][can_view]"
                                                    value="1"
                                                    <?= isset($current_permissions['can_view']) && $current_permissions['can_view'] ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="permissions[<?= $menu['id'] ?>][can_add]"
                                                    value="1"
                                                    <?= isset($current_permissions['can_add']) && $current_permissions['can_add'] ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="permissions[<?= $menu['id'] ?>][can_edit]"
                                                    value="1"
                                                    <?= isset($current_permissions['can_edit']) && $current_permissions['can_edit'] ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="permissions[<?= $menu['id'] ?>][can_delete]"
                                                    value="1"
                                                    <?= isset($current_permissions['can_delete']) && $current_permissions['can_delete'] ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Sub-Menu -->
                                    <?php if (!empty($menu['submenus'])): ?>
                                        <?php foreach ($menu['submenus'] as $submenu): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <i class="material-icons-outlined me-2"><?= esc($submenu['menu_icon']) ?></i>
                                                    <?= esc($submenu['menu_name']) ?>
                                                    <small class="text-muted d-block"><?= esc($submenu['menu_url']) ?></small>
                                                </td>
                                                <?php
                                                $current_permissions = $permissions[$submenu['id']] ?? [];
                                                ?>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            name="permissions[<?= $submenu['id'] ?>][can_view]"
                                                            value="1"
                                                            <?= isset($current_permissions['can_view']) && $current_permissions['can_view'] ? 'checked' : '' ?>>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            name="permissions[<?= $submenu['id'] ?>][can_add]"
                                                            value="1"
                                                            <?= isset($current_permissions['can_add']) && $current_permissions['can_add'] ? 'checked' : '' ?>>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            name="permissions[<?= $submenu['id'] ?>][can_edit]"
                                                            value="1"
                                                            <?= isset($current_permissions['can_edit']) && $current_permissions['can_edit'] ? 'checked' : '' ?>>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            name="permissions[<?= $submenu['id'] ?>][can_delete]"
                                                            value="1"
                                                            <?= isset($current_permissions['can_delete']) && $current_permissions['can_delete'] ? 'checked' : '' ?>>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Tidak ada sub-menu.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted mb-0">Belum ada menu yang tersedia untuk diatur hak aksesnya.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="checkAllPermissions()">
                                <i class="material-icons-outlined">check_box</i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="uncheckAllPermissions()">
                                <i class="material-icons-outlined">check_box_outline_blank</i> Hapus Semua
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?= base_url('admin/roles') ?>" class="btn btn-light me-2">
                            <i class="material-icons-outlined">arrow_back</i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined">save</i> Simpan Perubahan Hak Akses
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<script>
    function checkAllPermissions() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function uncheckAllPermissions() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    // Auto check 'view' when other permissions are checked
    document.addEventListener('DOMContentLoaded', function() {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');

        allCheckboxes.forEach(checkbox => {
            if (checkbox.name.includes('[can_add]') ||
                checkbox.name.includes('[can_edit]') ||
                checkbox.name.includes('[can_delete]')) {

                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Auto-check the corresponding 'can_view' checkbox
                        const menuId = this.name.match(/\[(\d+)\]/)[1];
                        const viewCheckbox = document.querySelector(`input[name="permissions[${menuId}][can_view]"]`);
                        if (viewCheckbox) {
                            viewCheckbox.checked = true;
                        }
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection() ?>