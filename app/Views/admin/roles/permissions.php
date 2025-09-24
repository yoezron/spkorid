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

        <?php foreach ($menus as $menu): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?= esc($menu['menu_name']) ?></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">Sub-Menu</th>
                                    <th class="text-center">Lihat (View)</th>
                                    <th class="text-center">Tambah (Create)</th>
                                    <th class="text-center">Ubah (Edit)</th>
                                    <th class="text-center">Hapus (Delete)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($menu['submenus'])): ?>
                                    <?php foreach ($menu['submenus'] as $submenu): ?>
                                        <tr>
                                            <td><?= esc($submenu['menu_name']) ?></td>
                                            <?php
                                            $current_permissions = $permissions[$submenu['id']] ?? [];
                                            ?>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="checkbox" name="permissions[<?= $submenu['id'] ?>][can_view]" value="1" <?= isset($current_permissions['can_view']) && $current_permissions['can_view'] ? 'checked' : '' ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="checkbox" name="permissions[<?= $submenu['id'] ?>][can_create]" value="1" <?= isset($current_permissions['can_create']) && $current_permissions['can_create'] ? 'checked' : '' ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="checkbox" name="permissions[<?= $submenu['id'] ?>][can_edit]" value="1" <?= isset($current_permissions['can_edit']) && $current_permissions['can_edit'] ? 'checked' : '' ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="checkbox" name="permissions[<?= $submenu['id'] ?>][can_delete]" value="1" <?= isset($current_permissions['can_delete']) && $current_permissions['can_delete'] ? 'checked' : '' ?>>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada sub-menu.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="card">
            <div class="card-body text-end">
                <a href="<?= base_url('admin/roles') ?>" class="btn btn-light">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan Hak Akses</button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>