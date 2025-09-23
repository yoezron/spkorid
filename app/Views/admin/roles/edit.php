<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Role: <?= esc($role['role_name']) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/forms/switches.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Formulir Edit Role</h4>
                <p>Ubah detail untuk role "<?= esc(ucwords(str_replace('_', ' ', $role['role_name']))) ?>"</p>
                <hr>

                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <form action="<?= base_url('admin/roles/update/' . $role['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="role_name">Nama Role</label>
                        <input type="text" class="form-control" id="role_name" name="role_name" value="<?= old('role_name', $role['role_name']) ?>" required>
                        <small>Gunakan format `snake_case` (huruf kecil dan garis bawah).</small>
                    </div>

                    <div class="form-group">
                        <label for="role_description">Deskripsi Role</label>
                        <textarea class="form-control" id="role_description" name="role_description" rows="3" required><?= old('role_description', $role['role_description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div>
                            <label class="switch s-icons s-outline s-outline-success">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" <?= (old('is_active', $role['is_active'])) ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                            <span class="ml-2">Aktif</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="<?= base_url('admin/roles') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>