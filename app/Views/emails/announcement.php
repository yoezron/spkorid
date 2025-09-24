<?php
// app/Views/emails/announcement.php
?>
<h2>Pengumuman Serikat Pekerja Kampus</h2>
<p>Halo <?= esc($nama) ?>,</p>
<div style="margin: 20px 0;">
    <?= $content ?>
</div>
<hr style="margin: 30px 0; border: none; border-top: 1px solid #e0e0e0;">
<p style="font-size: 12px; color: #888;">
    Anda menerima email ini karena terdaftar sebagai anggota Serikat Pekerja Kampus.
    <a href="<?= $unsubscribe_url ?>">Kelola preferensi email</a>
</p>