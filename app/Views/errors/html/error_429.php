<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Terlalu Banyak Permintaan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 500px;
            width: 90%;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .error-message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .timer {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .timer-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .timer-countdown {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }

        .btn-back {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
        }

        .btn-back:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
        }

        .icon-warning {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-warning svg {
            width: 40px;
            height: 40px;
            fill: #ef4444;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon-warning">
            <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
            </svg>
        </div>

        <div class="error-code">429</div>
        <h1 class="error-title">Terlalu Banyak Permintaan</h1>

        <p class="error-message">
            <?= esc($message ?? 'Anda telah melakukan terlalu banyak percobaan. Untuk keamanan sistem, akses Anda telah dibatasi sementara.') ?>
        </p>

        <?php if (isset($retry_after)): ?>
            <div class="timer">
                <div class="timer-label">Anda dapat mencoba lagi dalam:</div>
                <div class="timer-countdown" id="countdown">
                    <span id="minutes"><?= $retry_after ?></span> menit
                </div>
            </div>
        <?php endif; ?>

        <a href="<?= base_url() ?>" class="btn-back">Kembali ke Beranda</a>
    </div>

    <?php if (isset($retry_after)): ?>
        <script>
            // Countdown timer
            let timeLeft = <?= $retry_after ?> * 60; // Convert to seconds

            function updateCountdown() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;

                const display = minutes > 0 ?
                    `${minutes} menit ${seconds} detik` :
                    `${seconds} detik`;

                document.getElementById('countdown').textContent = display;

                if (timeLeft > 0) {
                    timeLeft--;
                    setTimeout(updateCountdown, 1000);
                } else {
                    document.getElementById('countdown').textContent = 'Selesai - Silakan refresh halaman';
                }
            }

            updateCountdown();
        </script>
    <?php endif; ?>
</body>

</html>