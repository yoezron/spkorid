<?php
// app/Views/emails/approval.php
?>
<h2>Keanggotaan Anda Telah Disetujui!</h2>
<p>Halo <?= esc($nama) ?>,</p>
<p>Dengan senang hati kami informasikan bahwa permohonan keanggotaan Anda telah <strong>DISETUJUI</strong>.</p>
<div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="margin: 0;"><strong>Detail Keanggotaan:</strong></p>
    <p>Nomor Anggota: <strong><?= esc($nomor_anggota) ?></strong></p>
    <p>Tanggal Bergabung: <strong><?= esc($tanggal_bergabung) ?></strong></p>
    <p style="margin: 0;">Status: <strong style="color: #28a745;">AKTIF</strong></p>
</div>
<p>Anda sekarang memiliki akses penuh ke semua fitur anggota.</p>
<p style="text-align: center;">
    <a href="<?= $login_url ?>" class="button" style="background: #28a745;">Masuk ke Portal Anggota</a>
</p>