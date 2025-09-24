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

<?php
// app/Views/emails/rejection.php
?>
<h2>Pemberitahuan Status Keanggotaan</h2>
<p>Halo <?= esc($nama) ?>,</p>
<p>Terima kasih atas minat Anda untuk bergabung dengan Serikat Pekerja Kampus.</p>
<p>Setelah melakukan review terhadap aplikasi Anda, kami mohon maaf harus menyampaikan bahwa permohonan keanggotaan Anda <strong>belum dapat disetujui</strong> saat ini.</p>
<?php if (!empty($alasan)): ?>
    <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Catatan:</strong></p>
        <p><?= esc($alasan) ?></p>
    </div>
<?php endif; ?>
<p>Jika Anda memiliki pertanyaan atau ingin mendiskusikan hal ini lebih lanjut, silakan hubungi kami di:</p>
<p>Email: <a href="mailto:<?= $contact_email ?>"><?= $contact_email ?></a></p>

<?php
// app/Views/emails/payment-confirmation.php
?>
<h2>Konfirmasi Pembayaran</h2>
<p>Halo <?= esc($nama) ?>,</p>
<p>Pembayaran Anda telah kami terima dan dikonfirmasi.</p>
<div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p><strong>Detail Pembayaran:</strong></p>
    <table style="width: 100%;">
        <tr>
            <td>No. Transaksi:</td>
            <td><strong><?= esc($nomor_transaksi) ?></strong></td>
        </tr>
        <tr>
            <td>Jumlah:</td>
            <td><strong><?= esc($jumlah) ?></strong></td>
        </tr>
        <tr>
            <td>Periode:</td>
            <td><strong><?= esc($periode) ?></strong></td>
        </tr>
        <tr>
            <td>Tanggal Bayar:</td>
            <td><strong><?= esc($tanggal_bayar) ?></strong></td>
        </tr>
    </table>
</div>
<p>Terima kasih atas pembayaran Anda.</p>

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