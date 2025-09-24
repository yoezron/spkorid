<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= isset($menu) ? 'Edit Menu' : 'Tambah Menu Baru' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1><?= isset($menu) ? 'Edit Menu: ' . esc($menu['menu_name']) : 'Tambah Menu Baru' ?></h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Formulir Menu</h5>
            </div>
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>

                <?php if (isset($menu)): ?>
                    <?= form_open('admin/menus/update/' . $menu['id']) ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php else: ?>
                    <?= form_open('admin/menus/store') ?>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Jenis Menu (Induk atau Sub-menu)</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">-- Jadikan Menu Induk --</option>
                        <?php foreach ($parent_menus as $parent): ?>
                            <option value="<?= $parent['id'] ?>" <?= set_select('parent_id', $parent['id'], (isset($menu) && $menu['parent_id'] == $parent['id']) || (isset($parent_id) && $parent_id == $parent['id'])) ?>>
                                Sub-menu dari: <?= esc($parent['menu_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="menu_name" class="form-label">Nama Menu</label>
                    <input type="text" class="form-control <?= (validation_show_error('menu_name')) ? 'is-invalid' : '' ?>" id="menu_name" name="menu_name" value="<?= old('menu_name', $menu['menu_name'] ?? '') ?>" required>
                    <?php if (validation_show_error('menu_name')): ?>
                        <div class="invalid-feedback"><?= validation_show_error('menu_name') ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="menu_url" class="form-label">URL Menu</label>
                    <input type="text" class="form-control <?= (validation_show_error('menu_url')) ? 'is-invalid' : '' ?>" id="menu_url" name="menu_url" value="<?= old('menu_url', $menu['menu_url'] ?? '') ?>" placeholder="Contoh: /admin/users">
                    <div class="form-text">Biarkan kosong (#) jika ini adalah menu induk yang hanya berisi sub-menu.</div>
                    <?php if (validation_show_error('menu_url')): ?>
                        <div class="invalid-feedback"><?= validation_show_error('menu_url') ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="menu_icon" class="form-label">Ikon Menu</label>
                    <input type="text" class="form-control" id="menu_icon" name="menu_icon" value="<?= old('menu_icon', $menu['menu_icon'] ?? '') ?>" placeholder="Contoh: dashboard">
                    <div class="form-text">Gunakan nama dari <a href="https://fonts.google.com/icons?selected=Material+Icons+Outlined" target="_blank">Material Icons</a>.</div>
                </div>

                <div class="mb-3">
                    <label for="order_priority" class="form-label">Urutan</label>
                    <input type="number" class="form-control" id="order_priority" name="order_priority" value="<?= old('order_priority', $menu['order_priority'] ?? '0') ?>" style="width: 100px;">
                    <div class="form-text">Angka lebih kecil akan ditampilkan lebih dulu.</div>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" <?= (isset($menu) && $menu['is_active']) || !isset($menu) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">
                        Aktifkan Menu
                    </label>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Menu</button>
                    <a href="<?= base_url('admin/menus') ?>" class="btn btn-light">Batal</a>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>