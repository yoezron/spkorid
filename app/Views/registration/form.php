<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }

        .registration-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .required-field {
            color: red;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .step.active .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .step-title {
            font-size: 14px;
            color: #6c757d;
        }

        .step.active .step-title {
            color: #667eea;
            font-weight: 600;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }

        .step.active:not(:last-child)::after {
            background: linear-gradient(90deg, #667eea 0%, #e9ecef 100%);
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
        }

        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upload-area:hover {
            border-color: #667eea;
            background-color: #f8f9fa;
        }

        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }

        .strength-weak {
            background-color: #dc3545;
            width: 33%;
        }

        .strength-medium {
            background-color: #ffc107;
            width: 66%;
        }

        .strength-strong {
            background-color: #28a745;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container registration-container">
        <div class="card">
            <div class="card-header text-center">
                <h3 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    Formulir Registrasi Anggota
                </h3>
                <p class="mb-0 mt-2">Serikat Pekerja Kampus Indonesia</p>
            </div>
            <div class="card-body p-4">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-title">Data Pribadi</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-title">Data Kepegawaian</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-title">Data Akademik</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-title">Upload Dokumen</div>
                    </div>
                </div>

                <!-- Display Errors -->
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Terdapat kesalahan dalam formulir:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form id="registrationForm" action="<?= base_url('register') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Section 1: Data Pribadi -->
                    <div class="form-section active" id="section1">
                        <h5 class="mb-4"><i class="fas fa-user me-2"></i>Data Pribadi</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="required-field">*</span></label>
                                <input type="text" class="form-control" name="nama_lengkap"
                                    value="<?= old('nama_lengkap') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="required-field">*</span></label>
                                <input type="email" class="form-control" name="email"
                                    value="<?= old('email') ?>" required>
                                <small class="text-muted">Email ini akan digunakan untuk login</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="required-field">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password"
                                        id="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength"></div>
                                <small class="text-muted">Min. 8 karakter, kombinasi huruf besar, kecil, dan angka</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi Password <span class="required-field">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_confirm"
                                        id="password_confirm" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jenis Kelamin <span class="required-field">*</span></label>
                                <select class="form-select" name="jenis_kelamin" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" <?= old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nomor WhatsApp Aktif <span class="required-field">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" class="form-control" name="nomor_whatsapp"
                                        placeholder="8123456789" value="<?= old('nomor_whatsapp') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap <span class="required-field">*</span></label>
                            <textarea class="form-control" name="alamat_lengkap" rows="3" required><?= old('alamat_lengkap') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi <span class="required-field">*</span></label>
                                <select class="form-select select2" name="provinsi_id" id="provinsi_id" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                    <?php foreach ($provinsi as $prov): ?>
                                        <option value="<?= $prov['id'] ?>" <?= old('provinsi_id') == $prov['id'] ? 'selected' : '' ?>>
                                            <?= esc($prov['nama_provinsi']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota/Kabupaten <span class="required-field">*</span></label>
                                <select class="form-select select2" name="kota_id" id="kota_id" required>
                                    <option value="">-- Pilih Kota/Kabupaten --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Media Sosial</label>
                            <input type="text" class="form-control" name="media_sosial"
                                placeholder="Instagram: @username, Twitter: @username, dll"
                                value="<?= old('media_sosial') ?>">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="nextSection(2)">
                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Section 2: Data Kepegawaian -->
                    <div class="form-section" id="section2">
                        <h5 class="mb-4"><i class="fas fa-briefcase me-2"></i>Data Kepegawaian</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Kepegawaian <span class="required-field">*</span></label>
                                <select class="form-select select2" name="status_kepegawaian_id" required>
                                    <option value="">-- Pilih Status --</option>
                                    <?php foreach ($status_kepegawaian as $status): ?>
                                        <option value="<?= $status['id'] ?>" <?= old('status_kepegawaian_id') == $status['id'] ? 'selected' : '' ?>>
                                            <?= esc($status['nama_status']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIDN/NIP</label>
                                <input type="text" class="form-control" name="nidn_nip"
                                    value="<?= old('nidn_nip') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pemberi Gaji <span class="required-field">*</span></label>
                                <select class="form-select select2" name="pemberi_gaji_id" required>
                                    <option value="">-- Pilih Pemberi Gaji --</option>
                                    <?php foreach ($pemberi_gaji as $pg): ?>
                                        <option value="<?= $pg['id'] ?>" <?= old('pemberi_gaji_id') == $pg['id'] ? 'selected' : '' ?>>
                                            <?= esc($pg['nama_pemberi']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Range Gaji <span class="required-field">*</span></label>
                                <select class="form-select select2" name="range_gaji_id" required>
                                    <option value="">-- Pilih Range Gaji --</option>
                                    <?php foreach ($range_gaji as $rg): ?>
                                        <option value="<?= $rg['id'] ?>" <?= old('range_gaji_id') == $rg['id'] ? 'selected' : '' ?>>
                                            <?= esc($rg['range_gaji']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gaji Pokok (Bukan Take Home Pay) <span class="required-field">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="gaji_pokok"
                                        value="<?= old('gaji_pokok') ?>" min="0" required>
                                </div>
                                <small class="text-muted">Data ini dijamin kerahasiaannya</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevSection(1)">
                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextSection(3)">
                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Section 3: Data Akademik -->
                    <div class="form-section" id="section3">
                        <h5 class="mb-4"><i class="fas fa-graduation-cap me-2"></i>Data Akademik</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Perguruan Tinggi <span class="required-field">*</span></label>
                                <select class="form-select select2" name="jenis_pt_id" id="jenis_pt_id" required>
                                    <option value="">-- Pilih Jenis PT --</option>
                                    <?php foreach ($jenis_pt as $jpt): ?>
                                        <option value="<?= $jpt['id'] ?>" <?= old('jenis_pt_id') == $jpt['id'] ? 'selected' : '' ?>>
                                            <?= esc($jpt['nama_jenis']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Asal Kampus <span class="required-field">*</span></label>
                                <select class="form-select select2" name="kampus_id" id="kampus_id" required>
                                    <option value="">-- Pilih Kampus --</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Program Studi <span class="required-field">*</span></label>
                                <select class="form-select select2" name="prodi_id" id="prodi_id" required>
                                    <option value="">-- Pilih Program Studi --</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bidang Keahlian/Expertise <span class="required-field">*</span></label>
                                <input type="text" class="form-control" name="bidang_keahlian"
                                    placeholder="Contoh: Manajemen Pendidikan, Teknik Informatika, dll"
                                    value="<?= old('bidang_keahlian') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motivasi Berserikat <span class="required-field">*</span></label>
                            <textarea class="form-control" name="motivasi_berserikat" rows="4"
                                placeholder="Jelaskan motivasi Anda bergabung dengan Serikat Pekerja Kampus"
                                required><?= old('motivasi_berserikat') ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevSection(2)">
                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextSection(4)">
                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Section 4: Upload Dokumen -->
                    <div class="form-section" id="section4">
                        <h5 class="mb-4"><i class="fas fa-file-upload me-2"></i>Upload Dokumen</h5>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Foto Diri <span class="required-field">*</span></label>
                                <div class="upload-area" onclick="document.getElementById('foto').click()">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <p class="mb-2">Klik untuk upload foto</p>
                                    <small class="text-muted">Format: JPG, JPEG, PNG (Max. 2MB)</small>
                                    <input type="file" id="foto" name="foto" accept="image/*"
                                        style="display: none;" required>
                                </div>
                                <img id="fotoPreview" class="preview-image d-none" alt="Preview Foto">
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Bukti Pembayaran Iuran Pertama <span class="required-field">*</span></label>
                                <div class="upload-area" onclick="document.getElementById('bukti_pembayaran').click()">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <p class="mb-2">Klik untuk upload bukti pembayaran</p>
                                    <small class="text-muted">Format: JPG, JPEG, PNG, PDF (Max. 5MB)</small>
                                    <input type="file" id="bukti_pembayaran" name="bukti_pembayaran"
                                        accept="image/*,application/pdf" style="display: none;" required>
                                </div>
                                <img id="buktiPreview" class="preview-image d-none" alt="Preview Bukti">
                                <div id="pdfPreview" class="d-none mt-2">
                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                    <p class="mt-2 mb-0">File PDF telah dipilih</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Pembayaran</h6>
                            <p class="mb-2">Iuran pertama sebesar <strong>Rp 100.000</strong> dapat ditransfer ke:</p>
                            <ul class="mb-0">
                                <li>Bank Mandiri: 1234567890 a.n. SPK Indonesia</li>
                                <li>Bank BCA: 0987654321 a.n. SPK Indonesia</li>
                            </ul>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreement" required>
                            <label class="form-check-label" for="agreement">
                                Saya menyatakan bahwa semua data yang saya isi adalah benar dan dapat dipertanggungjawabkan.
                                Saya juga setuju dengan <a href="#" target="_blank">syarat dan ketentuan</a> keanggotaan SPK.
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="prevSection(3)">
                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                            </button>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-check-circle me-2"></i> Daftar Sekarang
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-white mt-4">
            <p>&copy; 2024 Serikat Pekerja Kampus Indonesia. All rights reserved.</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Load cities when province changes
            $('#provinsi_id').change(function() {
                const provinsiId = $(this).val();
                $('#kota_id').html('<option value="">Loading...</option>');

                if (provinsiId) {
                    $.ajax({
                        url: '<?= base_url('register/get-cities') ?>/' + provinsiId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let options = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                            data.forEach(function(kota) {
                                options += `<option value="${kota.id}">${kota.nama_kota}</option>`;
                            });
                            $('#kota_id').html(options);
                        }
                    });
                } else {
                    $('#kota_id').html('<option value="">-- Pilih Kota/Kabupaten --</option>');
                }
            });

            // Load kampus when jenis PT changes
            $('#jenis_pt_id').change(function() {
                const jenisPtId = $(this).val();
                $('#kampus_id').html('<option value="">Loading...</option>');

                if (jenisPtId) {
                    $.ajax({
                        url: '<?= base_url('register/get-kampus') ?>/' + jenisPtId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let options = '<option value="">-- Pilih Kampus --</option>';
                            data.forEach(function(kampus) {
                                options += `<option value="${kampus.id}">${kampus.nama_kampus}</option>`;
                            });
                            $('#kampus_id').html(options);
                        }
                    });
                } else {
                    $('#kampus_id').html('<option value="">-- Pilih Kampus --</option>');
                }
            });

            // Load prodi when kampus changes
            $('#kampus_id').change(function() {
                const kampusId = $(this).val();
                $('#prodi_id').html('<option value="">Loading...</option>');

                if (kampusId) {
                    $.ajax({
                        url: '<?= base_url('register/get-prodi') ?>/' + kampusId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let options = '<option value="">-- Pilih Program Studi --</option>';
                            data.forEach(function(prodi) {
                                options += `<option value="${prodi.id}">${prodi.nama_prodi} (${prodi.jenjang})</option>`;
                            });
                            $('#prodi_id').html(options);
                        }
                    });
                } else {
                    $('#prodi_id').html('<option value="">-- Pilih Program Studi --</option>');
                }
            });

            // Toggle password visibility
            $('#togglePassword').click(function() {
                const type = $('#password').attr('type') === 'password' ? 'text' : 'password';
                $('#password').attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            $('#togglePasswordConfirm').click(function() {
                const type = $('#password_confirm').attr('type') === 'password' ? 'text' : 'password';
                $('#password_confirm').attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            // Password strength indicator
            $('#password').on('input', function() {
                const password = $(this).val();
                const strength = checkPasswordStrength(password);
                const strengthBar = $('#passwordStrength');

                strengthBar.removeClass('strength-weak strength-medium strength-strong');

                if (password.length > 0) {
                    if (strength < 3) {
                        strengthBar.addClass('strength-weak');
                    } else if (strength < 4) {
                        strengthBar.addClass('strength-medium');
                    } else {
                        strengthBar.addClass('strength-strong');
                    }
                }
            });

            // File preview
            $('#foto').change(function() {
                previewImage(this, '#fotoPreview');
            });

            $('#bukti_pembayaran').change(function() {
                const file = this.files[0];
                if (file) {
                    if (file.type === 'application/pdf') {
                        $('#buktiPreview').addClass('d-none');
                        $('#pdfPreview').removeClass('d-none');
                    } else {
                        $('#pdfPreview').addClass('d-none');
                        previewImage(this, '#buktiPreview');
                    }
                }
            });

            // Form submission with loading
            $('#registrationForm').submit(function(e) {
                e.preventDefault();

                // Validate all sections
                if (!validateAllSections()) {
                    return false;
                }

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                this.submit();
            });
        });

        // Section navigation
        function nextSection(section) {
            if (validateCurrentSection()) {
                $('.form-section').removeClass('active');
                $('#section' + section).addClass('active');
                updateStepIndicator(section);
                window.scrollTo(0, 0);
            }
        }

        function prevSection(section) {
            $('.form-section').removeClass('active');
            $('#section' + section).addClass('active');
            updateStepIndicator(section);
            window.scrollTo(0, 0);
        }

        function updateStepIndicator(activeStep) {
            $('.step').removeClass('active');
            for (let i = 1; i <= activeStep; i++) {
                $(`.step[data-step="${i}"]`).addClass('active');
            }
        }

        // Validation functions
        function validateCurrentSection() {
            const activeSection = $('.form-section.active');
            const requiredFields = activeSection.find('[required]');
            let isValid = true;

            requiredFields.each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Mohon lengkapi semua field yang wajib diisi!'
                });
            }

            return isValid;
        }

        function validateAllSections() {
            const requiredFields = $('[required]');
            let isValid = true;

            requiredFields.each(function() {
                if (!$(this).val() && !$(this).is(':checkbox')) {
                    isValid = false;
                }
                if ($(this).is(':checkbox') && !$(this).is(':checked')) {
                    isValid = false;
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Form Belum Lengkap',
                    text: 'Mohon lengkapi semua field yang wajib diisi di semua tahap!'
                });
            }

            return isValid;
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            return strength;
        }

        // Image preview
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(previewId).attr('src', e.target.result).removeClass('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>