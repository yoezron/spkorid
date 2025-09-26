<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice <?= esc($payment['nomor_transaksi']) ?></title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .invoice-header {
            padding: 20px;
            border-bottom: 2px solid #3498db;
        }

        .invoice-header h1 {
            font-size: 24px;
            color: #3498db;
            margin: 0;
        }

        .invoice-header p {
            margin: 0;
            text-align: right;
            font-size: 14px;
        }

        .invoice-info {
            padding: 20px;
            overflow: hidden;
            /* Clear floats */
        }

        .invoice-info .info-left {
            float: left;
            width: 50%;
        }

        .invoice-info .info-right {
            float: right;
            width: 50%;
            text-align: right;
        }

        .invoice-info strong {
            display: block;
            margin-bottom: 5px;
        }

        .invoice-body {
            padding: 20px;
        }

        .invoice-body table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-body th,
        .invoice-body td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .invoice-body th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .invoice-body .text-right {
            text-align: right;
        }

        .total-section {
            padding-top: 10px;
        }

        .total-section td {
            border-top: 2px solid #333;
            font-weight: bold;
        }

        .invoice-footer {
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #eee;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            display: inline-block;
        }

        .status-verified {
            background-color: #28a745;
        }

        .status-pending {
            background-color: #ffc107;
            color: #333;
        }

        .status-rejected {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="invoice-header">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none;">
                        <h1>INVOICE</h1>
                    </td>
                    <td style="border: none; text-align: right;">
                        <strong>Serikat Pekerja Kampus</strong><br>
                        Invoice: #<?= esc($payment['nomor_transaksi']) ?><br>
                        Tanggal: <?= date('d F Y', strtotime($payment['tanggal_pembayaran'])) ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-info">
            <div class="info-left">
                <strong>Ditagihkan Kepada:</strong>

                <?= esc($member['nama_lengkap']) ?><br>
                <?= esc($member['nomor_anggota']) ?><br>
                <?= esc($user['email']) ?>

            </div>
            <div class="info-right">
                <strong>Status:</strong>
                <?php
                $status = $payment['status_pembayaran'];
                $statusClass = 'status-pending';
                if ($status == 'verified') $statusClass = 'status-verified';
                if ($status == 'rejected') $statusClass = 'status-rejected';
                ?>
                <span class="status <?= $statusClass ?>"><?= esc(ucfirst($status)) ?></span>
            </div>
        </div>

        <div class="invoice-body">
            <table>
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Iuran Keanggotaan Serikat Pekerja Kampus <br>
                            <small>Periode: <?= date('F Y', strtotime($payment['tanggal_pembayaran'])) ?></small>
                        </td>
                        <td class="text-right">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-section">
                        <td class="text-right"><strong>Total</strong></td>
                        <td class="text-right"><strong>Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="invoice-footer">
            <p>Terima kasih atas kontribusi Anda. Pembayaran ini akan digunakan untuk mendukung kegiatan dan advokasi serikat.</p>
        </div>
    </div>
</body>

</html>