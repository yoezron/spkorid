<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="member-dashboard">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h2>Selamat Datang, <?= session()->get('nama_lengkap') ?>!</h2>
            <p>Nomor Anggota: <strong><?= $member['nomor_anggota'] ?></strong></p>
            <p>Status: <span class="badge badge-<?= $member['status_keanggotaan'] == 'active' ? 'success' : 'warning' ?>">
                    <?= ucfirst($member['status_keanggotaan']) ?>
                </span></p>
        </div>
        <div class="welcome-actions">
            <a href="<?= base_url('member/card') ?>" class="btn btn-primary">
                <i class="fas fa-id-card"></i> Kartu Anggota
            </a>
            <a href="<?= base_url('member/edit-profile') ?>" class="btn btn-secondary">
                <i class="fas fa-user-edit"></i> Edit Profil
            </a>
        </div>
    </div>

    <!-- Member Info Cards -->
    <div class="info-cards">
        <div class="info-card">
            <i class="fas fa-calendar-check"></i>
            <div>
                <h4>Bergabung Sejak</h4>
                <p><?= date('d F Y', strtotime($member['tanggal_bergabung'])) ?></p>
            </div>
        </div>

        <div class="info-card">
            <i class="fas fa-money-bill"></i>
            <div>
                <h4>Iuran Bulan Ini</h4>
                <p class="<?= $payment_status['current_month'] ? 'text-success' : 'text-danger' ?>">
                    <?= $payment_status['current_month'] ? 'Sudah Bayar' : 'Belum Bayar' ?>
                </p>
            </div>
        </div>

        <div class="info-card">
            <i class="fas fa-pen-alt"></i>
            <div>
                <h4>Artikel Saya</h4>
                <p><?= count($my_posts) ?> Artikel</p>
            </div>
        </div>

        <div class="info-card">
            <i class="fas fa-comments"></i>
            <div>
                <h4>Forum Diskusi</h4>
                <p><?= $forum_stats['threads'] ?> Thread, <?= $forum_stats['replies'] ?> Balasan</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Informasi Terbaru -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Informasi Terbaru</h3>
                <a href="<?= base_url('informasi') ?>">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php foreach ($latest_informasi as $info): ?>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="info-content">
                            <h4><?= $info['judul'] ?></h4>
                            <p><?= character_limiter(strip_tags($info['konten']), 100) ?></p>
                            <small><?= date('d/m/Y', strtotime($info['created_at'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($latest_informasi)): ?>
                    <p class="text-muted text-center">Tidak ada informasi terbaru</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Survei Aktif -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Survei Aktif</h3>
                <a href="<?= base_url('member/surveys') ?>">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php foreach ($active_surveys as $survey): ?>
                    <div class="survey-item">
                        <h4><?= $survey['judul'] ?></h4>
                        <p><?= $survey['deskripsi'] ?></p>
                        <div class="survey-meta">
                            <span class="deadline">
                                <i class="fas fa-clock"></i>
                                Deadline: <?= date('d/m/Y', strtotime($survey['end_date'])) ?>
                            </span>
                            <?php if ($survey['is_responded']): ?>
                                <span class="badge badge-success">Sudah Diisi</span>
                            <?php else: ?>
                                <a href="<?= base_url('member/surveys/take/' . $survey['id']) ?>"
                                    class="btn btn-sm btn-primary">Isi Survei</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($active_surveys)): ?>
                    <p class="text-muted text-center">Tidak ada survei aktif</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Forum Diskusi Terbaru -->
        <div class="dashboard-card full-width">
            <div class="card-header">
                <h3>Forum Diskusi Terbaru</h3>
                <a href="<?= base_url('member/forum') ?>">Lihat Forum</a>
            </div>
            <div class="card-body">
                <div class="forum-threads">
                    <?php foreach ($recent_threads as $thread): ?>
                        <div class="thread-item">
                            <img src="<?= $thread['user_foto'] ?? base_url('images/default-avatar.png') ?>"
                                alt="<?= $thread['user_name'] ?>"
                                class="thread-avatar">
                            <div class="thread-content">
                                <h4>
                                    <a href="<?= base_url('member/forum/thread/' . $thread['id']) ?>">
                                        <?= $thread['judul'] ?>
                                    </a>
                                </h4>
                                <div class="thread-meta">
                                    <span><i class="fas fa-user"></i> <?= $thread['user_name'] ?></span>
                                    <span><i class="fas fa-comments"></i> <?= $thread['reply_count'] ?> balasan</span>
                                    <span><i class="fas fa-eye"></i> <?= $thread['views'] ?> views</span>
                                    <span><i class="fas fa-clock"></i> <?= time_ago($thread['created_at']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="forum-actions">
                    <a href="<?= base_url('member/forum/create-thread') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Thread Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Riwayat Pembayaran</h3>
                <a href="<?= base_url('member/payment/history') ?>">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="payment-history">
                    <?php foreach ($payment_history as $payment): ?>
                        <div class="payment-row">
                            <div class="payment-date">
                                <?= date('d/m/Y', strtotime($payment['payment_date'])) ?>
                            </div>
                            <div class="payment-amount">
                                Rp <?= number_format($payment['amount']) ?>
                            </div>
                            <div class="payment-status">
                                <span class="badge badge-<?= $payment['status'] == 'verified' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($payment['status']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($payment_history)): ?>
                        <p class="text-muted text-center">Belum ada riwayat pembayaran</p>
                    <?php endif; ?>
                </div>

                <?php if (!$payment_status['current_month']): ?>
                    <div class="payment-reminder">
                        <p class="text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Iuran bulan ini belum dibayar
                        </p>
                        <a href="<?= base_url('member/payment/create') ?>" class="btn btn-warning btn-sm">
                            Bayar Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- My Articles -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Artikel Saya</h3>
                <a href="<?= base_url('member/posts') ?>">Kelola Artikel</a>
            </div>
            <div class="card-body">
                <?php foreach ($my_posts as $post): ?>
                    <div class="post-item">
                        <h4><?= $post['judul'] ?></h4>
                        <div class="post-meta">
                            <span class="badge badge-<?= $post['status'] == 'published' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($post['status']) ?>
                            </span>
                            <span><i class="fas fa-eye"></i> <?= $post['views'] ?></span>
                            <span><?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($my_posts)): ?>
                    <p class="text-muted text-center">Belum ada artikel</p>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="<?= base_url('member/posts/create') ?>" class="btn btn-primary">
                        <i class="fas fa-pen"></i> Tulis Artikel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Helper function to display "time ago" format
if (!function_exists('time_ago')) {
    function time_ago($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return $diff . ' detik lalu';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' menit lalu';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' jam lalu';
        } elseif ($diff < 2592000) {
            return floor($diff / 86400) . ' hari lalu';
        } elseif ($diff < 31536000) {
            return floor($diff / 2592000) . ' bulan lalu';
        } else {
            return floor($diff / 31536000) . ' tahun lalu';
        }
    }
}
?>
<?= $this->endSection() ?>