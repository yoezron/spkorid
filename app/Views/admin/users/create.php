<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah User Baru
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('plugins/select2/select2.min.css') ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/forms/switches.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-6">
            <div class="p-4">
                <h4>Formulir User Baru</h4>
                <p>Isi detail di bawah ini untuk membuat akun user baru.</p>
                <hr>

                <?php // Menampilkan error validasi jika ada 
                ?>
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <form action="<?= base_url('admin/users/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="member_id">Tautkan ke Anggota</label>
                        <select class="form-control select2" id="member_id" name="member_id" required>
                            <option value="">Pilih Anggota yang Belum Memiliki Akun</option>
                            <?php if (!empty($members)): ?>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?= $member['id'] ?>" <?= old('member_id') == $member['id'] ? 'selected' : '' ?>>
                                        <?= esc($member['nama_lengkap']) ?> (<?= esc($member['nomor_anggota']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small>Hanya anggota yang belum memiliki akun user yang akan tampil di sini.</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="role_id">Role</label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                        <?= esc(ucwords(str_replace('_', ' ', $role['role_name']))) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status Akun</label>
                        <div>
                            <label class="switch s-icons s-outline s-outline-success">
                                <input type="checkbox" name="is_active" value="1" <?= old('is_active') ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                            <span class="ml-2">Aktif</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Buat User</button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/select2.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Cari berdasarkan nama atau nomor anggota",
            allowClear: true
        });
    });
</script>
<?= $this->endSection() ?>