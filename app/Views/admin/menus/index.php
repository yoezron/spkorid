<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Menu
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col">
        <div class="page-description d-flex align-items-center">
            <h1 class="flex-grow-1">Manajemen Menu</h1>
            <a href="<?= base_url('admin/menus/create') ?>" class="btn btn-primary">
                <i class="material-icons-outlined">add</i>Tambah Menu Induk
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?= $this->include('partials/flash_messages') ?>
                <div class="accordion" id="menuAccordion">
                    <?php if (!empty($menus)): ?>
                        <?php foreach ($menus as $index => $menu): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $menu['id'] ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $menu['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $menu['id'] ?>">
                                        <i class="material-icons-outlined me-2"><?= esc($menu['menu_icon']) ?></i>
                                        <?= esc($menu['menu_name']) ?>
                                        <span class="badge badge-style-light rounded-pill badge-primary ms-2"><?= count($menu['submenus']) ?> Sub-menu</span>
                                    </button>
                                </h2>
                                <div id="collapse<?= $menu['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $menu['id'] ?>" data-bs-parent="#menuAccordion">
                                    <div class="accordion-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <strong>Sub-menu untuk <?= esc($menu['menu_name']) ?></strong>
                                            <div class="btn-group">
                                                <a href="<?= base_url('admin/menus/edit/' . $menu['id']) ?>" class="btn btn-sm btn-outline-primary">Edit Induk</a>
                                                <a href="<?= base_url('admin/menus/create/' . $menu['id']) ?>" class="btn btn-sm btn-outline-success">Tambah Sub-menu</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $menu['id'] ?>">
                                                    Hapus Induk
                                                </button>
                                            </div>
                                        </div>

                                        <?php if (!empty($menu['submenus'])): ?>
                                            <ul class="list-group">
                                                <?php foreach ($menu['submenus'] as $submenu): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <i class="material-icons-outlined me-2 small"><?= esc($submenu['menu_icon']) ?></i>
                                                            <?= esc($submenu['menu_name']) ?> (<code><?= esc($submenu['menu_url']) ?></code>)
                                                        </span>
                                                        <div class="btn-group">
                                                            <a href="<?= base_url('admin/menus/edit/' . $submenu['id']) ?>" class="btn btn-sm btn-light">Edit</a>
                                                            <button type="button" class="btn btn-sm btn-light-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $submenu['id'] ?>">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </li>
                                                    <div class="modal fade" id="deleteModal<?= $submenu['id'] ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Yakin ingin menghapus sub-menu "<?= esc($submenu['menu_name']) ?>"?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <?= form_open('admin/menus/delete/' . $submenu['id']) ?>
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                                                    <?= form_close() ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-center text-muted">Belum ada sub-menu.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="deleteModal<?= $menu['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus menu induk "<?= esc($menu['menu_name']) ?>"? <strong class="text-danger">Semua sub-menu di dalamnya juga akan terhapus.</strong>
                                        </div>
                                        <div class="modal-footer">
                                            <?= form_open('admin/menus/delete/' . $menu['id']) ?>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                            <?= form_close() ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <p>Belum ada menu yang dibuat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>