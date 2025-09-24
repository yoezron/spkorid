<?php
// app/Views/emails/password-reset.php
?>
<h2>Reset Password</h2>
<p>Halo <?= esc($nama) ?>,</p>
<p>Kami menerima permintaan untuk mereset password akun Anda.</p>
<p>Klik tombol di bawah ini untuk membuat password baru:</p>
<p style="text-align: center;">
    <a href="<?= $reset_url ?>" class="button">Reset Password</a>
</p>
<p>Atau salin URL berikut:</p>
<p style="background: #f4f4f4; padding: 10px; word-break: break-all;">
    <?= $reset_url ?>
</p>
<p><strong>Link ini akan kadaluarsa dalam <?= $expire_time ?>.</strong></p>
<p>Jika Anda tidak meminta reset password, abaikan email ini dan password Anda akan tetap aman.</p>