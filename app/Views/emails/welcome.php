<?php
// app/Views/emails/welcome.php
?>
<h2>Selamat Bergabung!</h2>
<p>Halo <?= esc($nama) ?>,</p>
<p>Selamat! Anda telah resmi menjadi anggota Serikat Pekerja Kampus.</p>
<div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #3498db; margin: 20px 0;">
    <p><strong>Informasi Keanggotaan:</strong></p>
    <p>Nomor Anggota: <strong><?= esc($nomor_anggota) ?></strong></p>
    <p>Status: <strong>Aktif</strong></p>
</div>
<p>Anda sekarang dapat:</p>
<ul>
    <li>Mengakses dashboard anggota</li>
    <li>Mengunduh kartu anggota</li>
    <li>Berpartisipasi dalam forum diskusi</li>
    <li>Mengikuti survei anggota</li>
    <li>Mendapatkan informasi terbaru dari serikat</li>
</ul>
<p style="text-align: center;">
    <a href="<?= $login_url ?>" class="button">Login ke Dashboard</a>
</p>