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