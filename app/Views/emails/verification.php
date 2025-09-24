<?php
// app/Views/emails/verification.php
?>
<h2>Verifikasi Email Anda</h2>
<p>Halo <?= esc($nama) ?>,</p>
<p>Terima kasih telah mendaftar sebagai anggota Serikat Pekerja Kampus.</p>
<p>Untuk menyelesaikan proses pendaftaran, silakan verifikasi email Anda dengan mengklik tombol di bawah ini:</p>
<p style="text-align: center;">
    <a href="<?= $verification_url ?>" class="button" style="display: inline-block; padding: 12px 30px; background: #3498db; color: #fff; text-decoration: none; border-radius: 5px;">
        Verifikasi Email
    </a>
</p>
<p>Atau salin dan tempel URL berikut ke browser Anda:</p>
<p style="background: #f4f4f4; padding: 10px; word-break: break-all;">
    <?= $verification_url ?>
</p>
<p><strong>Link ini akan kadaluarsa dalam <?= $expire_time ?>.</strong></p>
<p>Jika Anda tidak mendaftar di website kami, abaikan email ini.</p>