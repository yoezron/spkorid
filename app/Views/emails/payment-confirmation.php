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