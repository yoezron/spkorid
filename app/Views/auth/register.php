<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="registration-container">
    <div class="registration-card">
        <div class="registration-header">
            <h2>Pendaftaran Anggota SPK</h2>
            <p>Lengkapi formulir berikut untuk mendaftar sebagai anggota</p>
        </div>

        <form action="<?= base_url('register') ?>" method="POST" enctype="multipart/form-data" class="registration-form">
            <?= csrf_field() ?>

            <!-- Progress Bar -->
            <div class="progress-bar">
                <div class="progress-step active" data-step="1">
                    <span>1</span>
                    <p>Data Pribadi</p>
                </div>
                <div class="progress-step" data-step="2">
                    <span>2</span>
                    <p>Data Kepegawaian</p>
                </div>
                <div class="progress-step" data-step="3">
                    <span>3</span>
                    <p>Data Akademik</p>
                </div>
                <div class="progress-step" data-step="4">
                    <span>4</span>
                    <p>Dokumen</p>
                </div>
            </div>

            <!-- Step 1: Data Pribadi -->
            <div class="form-step active" data-step="1">
                <h3>Data Pribadi</h3>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama_lengkap">Nama Lengkap *</label>
                        <input type="text"
                            id="nama_lengkap"
                            name="nama_lengkap"
                            class="form-control"
                            value="<?= old('nama_lengkap') ?>"
                            required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="jenis_kelamin">Jenis Kelamin *</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" <?= old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email Valid *</label>
                        <input type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="<?= old('email') ?>"
                            required>
                        <small class="form-text">Email akan digunakan untuk verifikasi</small>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="nomor_whatsapp">Nomor WhatsApp Aktif *</label>
                        <input type="tel"
                            id="nomor_whatsapp"
                            name="nomor_whatsapp"
                            class="form-control"
                            value="<?= old('nomor_whatsapp') ?>"
                            placeholder="08xxxxxxxxxx"
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="password">Password *</label>
                        <input type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            minlength="8"
                            required>
                        <small class="form-text">Minimal 8 karakter</small>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="password_confirm">Verifikasi Password *</label>
                        <input type="password"
                            id="password_confirm"
                            name="password_confirm"
                            class="form-control"
                            minlength="8"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat_lengkap">Alamat Lengkap *</label>
                    <textarea id="alamat_lengkap"
                        name="alamat_lengkap"
                        class="form-control"
                        rows="3"
                        required><?= old('alamat_lengkap') ?></textarea>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn btn-primary btn-next">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Data Kepegawaian -->
            <div class="form-step" data-step="2">
                <h3>Data Kepegawaian</h3>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="status_kepegawaian_id">Status Kepegawaian *</label>
                        <select id="status_kepegawaian_id" name="status_kepegawaian_id" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            <?php if (isset($status_kepegawaian)): ?>
                                <?php foreach ($status_kepegawaian as $status): ?>
                                    <option value="<?= $status['id'] ?>"
                                        <?= old('status_kepegawaian_id') == $status['id'] ? 'selected' : '' ?>>
                                        <?= $status['nama_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="pemberi_gaji_id">Pemberi Gaji *</label>
                        <select id="pemberi_gaji_id" name="pemberi_gaji_id" class="form-control" required>
                            <option value="">-- Pilih Pemberi Gaji --</option>
                            <?php if (isset($pemberi_gaji)): ?>
                                <?php foreach ($pemberi_gaji as $pg): ?>
                                    <option value="<?= $pg['id'] ?>"
                                        <?= old('pemberi_gaji_id') == $pg['id'] ? 'selected' : '' ?>>
                                        <?= $pg['nama_pemberi'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="range_gaji_id">Range Gaji *</label>
                        <select id="range_gaji_id" name="range_gaji_id" class="form-control" required>
                            <option value="">-- Pilih Range Gaji --</option>
                            <?php if (isset($range_gaji)): ?>
                                <?php foreach ($range_gaji as $rg): ?>
                                    <option value="<?= $rg['id'] ?>"
                                        <?= old('range_gaji_id') == $rg['id'] ? 'selected' : '' ?>>
                                        <?= $rg['range_text'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="gaji_pokok">Gaji Pokok (Bukan Take Home Pay) *</label>
                        <input type="number"
                            id="gaji_pokok"
                            name="gaji_pokok"
                            class="form-control"
                            value="<?= old('gaji_pokok') ?>"
                            placeholder="Contoh: 5000000"
                            required>
                        <small class="form-text">Masukkan angka tanpa titik atau koma</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="wilayah_id">Wilayah *</label>
                        <select id="wilayah_id" name="wilayah_id" class="form-control" required>
                            <option value="">-- Pilih Wilayah --</option>
                            <?php if (isset($wilayah)): ?>
                                <?php foreach ($wilayah as $w): ?>
                                    <option value="<?= $w['id'] ?>"
                                        <?= old('wilayah_id') == $w['id'] ? 'selected' : '' ?>>
                                        <?= $w['nama_wilayah'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="nidn_nip">NIDN/NIP</label>
                        <input type="text"
                            id="nidn_nip"
                            name="nidn_nip"
                            class="form-control"
                            value="<?= old('nidn_nip') ?>">
                        <small class="form-text">Kosongkan jika tidak ada</small>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn btn-secondary btn-prev">
                        <i class="fas fa-arrow-left"></i> Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary btn-next">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Data Akademik -->
            <div class="form-step" data-step="3">
                <h3>Data Akademik</h3>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="jenis_pt_id">Jenis Perguruan Tinggi *</label>
                        <select id="jenis_pt_id" name="jenis_pt_id" class="form-control" required>
                            <option value="">-- Pilih Jenis PT --</option>
                            <?php if (isset($jenis_pt)): ?>
                                <?php foreach ($jenis_pt as $jpt): ?>
                                    <option value="<?= $jpt['id'] ?>"
                                        <?= old('jenis_pt_id') == $jpt['id'] ? 'selected' : '' ?>>
                                        <?= $jpt['nama_jenis'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="kampus_id">Asal Kampus *</label>
                        <select id="kampus_id" name="kampus_id" class="form-control" required>
                            <option value="">-- Pilih Kampus --</option>
                            <?php if (isset($kampus)): ?>
                                <?php foreach ($kampus as $k): ?>
                                    <option value="<?= $k['id'] ?>"
                                        <?= old('kampus_id') == $k['id'] ? 'selected' : '' ?>>
                                        <?= $k['nama_kampus'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="prodi_id">Asal Program Studi *</label>
                    <select id="prodi_id" name="prodi_id" class="form-control" required>
                        <option value="">-- Pilih Program Studi --</option>
                        <?php if (isset($prodi)): ?>
                            <?php foreach ($prodi as $p): ?>
                                <option value="<?= $p['id'] ?>"
                                    <?= old('prodi_id') == $p['id'] ? 'selected' : '' ?>>
                                    <?= $p['nama_prodi'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="bidang_keahlian">Bidang Keahlian/Expertise *</label>
                    <textarea id="bidang_keahlian"
                        name="bidang_keahlian"
                        class="form-control"
                        rows="3"
                        placeholder="Jelaskan bidang keahlian Anda"
                        required><?= old('bidang_keahlian') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="motivasi_berserikat">Motivasi Berserikat *</label>
                    <textarea id="motivasi_berserikat"
                        name="motivasi_berserikat"
                        class="form-control"
                        rows="4"
                        placeholder="Jelaskan motivasi Anda bergabung dengan SPK"
                        required><?= old('motivasi_berserikat') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="media_sosial">Alamat Media Sosial</label>
                    <textarea id="media_sosial"
                        name="media_sosial"
                        class="form-control"
                        rows="2"
                        placeholder="Instagram: @username, LinkedIn: nama, dll"><?= old('media_sosial') ?></textarea>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn btn-secondary btn-prev">
                        <i class="fas fa-arrow-left"></i> Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary btn-next">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 4: Upload Dokumen -->
            <div class="form-step" data-step="4">
                <h3>Upload Dokumen</h3>

                <div class="form-group">
                    <label for="foto">Upload Foto *</label>
                    <div class="custom-file">
                        <input type="file"
                            id="foto"
                            name="foto"
                            class="custom-file-input"
                            accept="image/*"
                            required>
                        <label class="custom-file-label" for="foto">Pilih file...</label>
                    </div>
                    <small class="form-text">Format: JPG, PNG, maksimal 2MB</small>
                    <div id="foto-preview" class="image-preview mt-2"></div>
                </div>

                <div class="form-group">
                    <label for="bukti_pembayaran">Upload Bukti Pembayaran Iuran Pertama *</label>
                    <div class="custom-file">
                        <input type="file"
                            id="bukti_pembayaran"
                            name="bukti_pembayaran"
                            class="custom-file-input"
                            accept="image/*,application/pdf"
                            required>
                        <label class="custom-file-label" for="bukti_pembayaran">Pilih file...</label>
                    </div>
                    <small class="form-text">Format: JPG, PNG, PDF, maksimal 5MB</small>
                    <div id="bukti-preview" class="image-preview mt-2"></div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                            class="custom-control-input"
                            id="agree_terms"
                            name="agree_terms"
                            required>
                        <label class="custom-control-label" for="agree_terms">
                            Saya menyetujui <a href="<?= base_url('ad-art') ?>" target="_blank">AD/ART</a>
                            dan ketentuan keanggotaan SPK
                        </label>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn btn-secondary btn-prev">
                        <i class="fas fa-arrow-left"></i> Sebelumnya
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Daftar Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Multi-step form navigation
    document.addEventListener('DOMContentLoaded', function() {
        const steps = document.querySelectorAll('.form-step');
        const progressSteps = document.querySelectorAll('.progress-step');
        let currentStep = 1;

        // Next button handlers
        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', function() {
                if (validateStep(currentStep)) {
                    currentStep++;
                    showStep(currentStep);
                }
            });
        });

        // Previous button handlers
        document.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', function() {
                currentStep--;
                showStep(currentStep);
            });
        });

        function showStep(step) {
            steps.forEach(s => s.classList.remove('active'));
            progressSteps.forEach(p => p.classList.remove('active'));

            document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');

            for (let i = 1; i <= step; i++) {
                document.querySelector(`.progress-step[data-step="${i}"]`).classList.add('active');
            }
        }

        function validateStep(step) {
            const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
            const inputs = currentStepElement.querySelectorAll('[required]');

            for (let input of inputs) {
                if (!input.value) {
                    input.classList.add('is-invalid');
                    input.focus();
                    return false;
                }
                input.classList.remove('is-invalid');
            }
            return true;
        }

        // File input preview
        document.getElementById('foto').addEventListener('change', function(e) {
            previewImage(e.target, 'foto-preview');
        });

        document.getElementById('bukti_pembayaran').addEventListener('change', function(e) {
            previewImage(e.target, 'bukti-preview');
        });

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `<p class="text-muted">File: ${file.name}</p>`;
            }
        }

        // Dynamic dropdowns
        document.getElementById('jenis_pt_id').addEventListener('change', function() {
            loadKampus(this.value);
        });

        document.getElementById('kampus_id').addEventListener('change', function() {
            loadProdi(this.value);
        });

        function loadKampus(jenisPtId) {
            fetch(`<?= base_url('register/get-kampus') ?>/${jenisPtId}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('kampus_id');
                    select.innerHTML = '<option value="">-- Pilih Kampus --</option>';
                    data.forEach(item => {
                        select.innerHTML += `<option value="${item.id}">${item.nama_kampus}</option>`;
                    });
                });
        }

        function loadProdi(kampusId) {
            fetch(`<?= base_url('register/get-prodi') ?>/${kampusId}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('prodi_id');
                    select.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
                    data.forEach(item => {
                        select.innerHTML += `<option value="${item.id}">${item.nama_prodi}</option>`;
                    });
                });
        }
    });
</script>
<?= $this->endSection() ?>