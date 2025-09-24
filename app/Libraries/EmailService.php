<?php
// app/Libraries/EmailService.php
namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    protected $mail;
    protected $config;
    protected $viewPath = APPPATH . 'Views/emails/';

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->config = config('Email');
        $this->setupMailer();
    }

    /**
     * Setup PHPMailer configuration
     */
    protected function setupMailer(): void
    {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host = env('email.SMTPHost', 'smtp.gmail.com');
            $this->mail->SMTPAuth = true;
            $this->mail->Username = env('email.SMTPUser');
            $this->mail->Password = env('email.SMTPPass');
            $this->mail->SMTPSecure = env('email.SMTPCrypto', PHPMailer::ENCRYPTION_STARTTLS);
            $this->mail->Port = env('email.SMTPPort', 587);
            $this->mail->CharSet = 'UTF-8';

            // Default sender
            $this->mail->setFrom(
                env('email.fromEmail', 'noreply@spk.org'),
                env('email.fromName', 'Serikat Pekerja Kampus')
            );
        } catch (Exception $e) {
            log_message('error', 'Email setup error: ' . $e->getMessage());
        }
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail(array $userData, string $token): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($userData['email'], $userData['nama_lengkap']);

            $this->mail->isHTML(true);
            $this->mail->Subject = 'Verifikasi Email - Serikat Pekerja Kampus';

            $verificationUrl = base_url("verify-email/{$token}");

            $body = $this->loadEmailTemplate('verification', [
                'nama' => $userData['nama_lengkap'],
                'verification_url' => $verificationUrl,
                'expire_time' => '24 jam'
            ]);

            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);

            return $this->mail->send();
        } catch (Exception $e) {
            log_message('error', 'Verification email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email after successful registration
     */
    public function sendWelcomeEmail(array $memberData): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($memberData['email'], $memberData['nama_lengkap']);

            $this->mail->isHTML(true);
            $this->mail->Subject = 'Selamat Bergabung di Serikat Pekerja Kampus';

            $body = $this->loadEmailTemplate('welcome', [
                'nama' => $memberData['nama_lengkap'],
                'nomor_anggota' => $memberData['nomor_anggota'] ?? 'Pending',
                'login_url' => base_url('login')
            ]);

            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);

            return $this->mail->send();
        } catch (Exception $e) {
            log_message('error', 'Welcome email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(array $userData, string $token): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($userData['email']);

            $this->mail->isHTML(true);
            $this->mail->Subject = 'Reset Password - Serikat Pekerja Kampus';

            $resetUrl = base_url("reset-password/{$token}");

            $body = $this->loadEmailTemplate('password-reset', [
                'nama' => $userData['nama_lengkap'] ?? $userData['email'],
                'reset_url' => $resetUrl,
                'expire_time' => '2 jam'
            ]);

            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);

            return $this->mail->send();
        } catch (Exception $e) {
            log_message('error', 'Password reset email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send membership approved notification
     */
    public function sendApprovalEmail(array $memberData): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($memberData['email'], $memberData['nama_lengkap']);

            $this->mail->isHTML(true);
            $this->mail->Subject = 'Keanggotaan Anda Telah Disetujui';

            $body = $this->loadEmailTemplate('approval', [
                'nama' => $memberData['nama_lengkap'],
                'nomor_anggota' => $memberData['nomor_anggota'],
                'tanggal_bergabung' => date('d F Y', strtotime($memberData['tanggal_bergabung'])),
                'login_url' => base_url('login')
            ]);

            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);

            return $this->mail->send();
        } catch (Exception $e) {
            log_message('error', 'Approval email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send rejection notification
     */
    public function sendRejectionEmail(array $memberData, string $reason): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($memberData['email'], $memberData['nama_lengkap']);

            $this->mail->isHTML(true);
            $this->mail->Subject = 'Pemberitahuan Status Keanggotaan';

            $body = $this->loadEmailTemplate('rejection', [
                'nama' => $memberData['nama_lengkap'],
                'alasan' => $reason,
                'contact_email' => env('email.supportEmail', 'support@spk.org')
            ]);

            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);

            return $this->mail->send();
        } catch (Exception $e) {
            log_message('error', 'Rejection email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation(array $paymentData): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($paymentData['email'], $paymentData['nama']);

            $this->mail->isHTML(true);
            $this->mail->Subject = 'Konfirmasi Pembayaran - Serikat Pekerja Kampus';

            $body = $this->loadEmailTemplate('payment-confirmation', [
                'nama' => $paymentData['nama'],
                'nomor_transaksi' => $paymentData['nomor_transaksi'],
                'jumlah' => 'Rp ' . number_format($paymentData['jumlah'], 0, ',', '.'),
                'periode' => $paymentData['periode'],
                'tanggal_bayar' => date('d F Y', strtotime($paymentData['tanggal_bayar']))
            ]);

            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);

            return $this->mail->send();
        } catch (Exception $e) {
            log_message('error', 'Payment confirmation email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send announcement to members
     */
    public function sendAnnouncement(array $recipients, string $subject, string $message): array
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        foreach ($recipients as $recipient) {
            try {
                $this->mail->clearAddresses();
                $this->mail->addAddress($recipient['email'], $recipient['nama_lengkap']);

                $this->mail->isHTML(true);
                $this->mail->Subject = $subject;

                $body = $this->loadEmailTemplate('announcement', [
                    'nama' => $recipient['nama_lengkap'],
                    'content' => $message,
                    'unsubscribe_url' => base_url('member/email-preferences')
                ]);

                $this->mail->Body = $body;
                $this->mail->AltBody = strip_tags($body);

                if ($this->mail->send()) {
                    $results['success'][] = $recipient['email'];
                } else {
                    $results['failed'][] = $recipient['email'];
                }

                // Add delay to avoid rate limiting
                usleep(500000); // 0.5 second delay

            } catch (Exception $e) {
                log_message('error', 'Announcement email error for ' . $recipient['email'] . ': ' . $e->getMessage());
                $results['failed'][] = $recipient['email'];
            }
        }

        return $results;
    }

    /**
     * Send custom email
     */
    public function sendCustomEmail(string $to, string $subject, string $message, array $attachments = []): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();

            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;
            $this->mail->AltBody = strip_tags($message);

            // Add attachments if any
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $this->mail->addAttachment($attachment);
                }
            }

            return $this->mail->send();
        } catch (Exception $e) {
            log_message('error', 'Custom email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Load email template
     */
    protected function loadEmailTemplate(string $template, array $data = []): string
    {
        $templatePath = $this->viewPath . $template . '.php';

        // Check if template exists
        if (!file_exists($templatePath)) {
            // Return basic template if specific template not found
            return $this->getDefaultTemplate($data);
        }

        // Load template with data
        extract($data);
        ob_start();
        include $templatePath;
        $content = ob_get_clean();

        // Wrap in email layout
        return $this->wrapInLayout($content, $data);
    }

    /**
     * Wrap content in email layout
     */
    protected function wrapInLayout(string $content, array $data): string
    {
        $layout = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serikat Pekerja Kampus</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container { 
            max-width: 600px; 
            margin: 20px auto; 
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header { 
            background: #2c3e50; 
            color: #fff; 
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content { 
            padding: 30px;
        }
        .footer { 
            background: #ecf0f1; 
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Serikat Pekerja Kampus</h1>
        </div>
        <div class="content">
            ' . $content . '
        </div>
        <div class="footer">
            <p>&copy; ' . date('Y') . ' Serikat Pekerja Kampus. All rights reserved.</p>
            <p>Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.</p>
        </div>
    </div>
</body>
</html>';

        return $layout;
    }

    /**
     * Get default template
     */
    protected function getDefaultTemplate(array $data): string
    {
        $nama = $data['nama'] ?? 'User';
        $content = $data['content'] ?? 'No content provided';

        return "
            <h2>Halo, {$nama}</h2>
            <p>{$content}</p>
            <p>Terima kasih,<br>Tim Serikat Pekerja Kampus</p>
        ";
    }
}
