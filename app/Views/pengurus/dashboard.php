<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="pengurus-dashboard">
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card orange">
            <div class="card-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="card-content">
                <h3><?= $member_stats['pending'] ?></h3>
                <p>Calon Anggota</p>
                <a href="<?= base_url('pengurus/members/pending') ?>" class="card-link">
                    Verifikasi <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="summary-card blue">
            <div class="card-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="card-content">
                <h3><?= count($pending_posts) ?></h3>
                <p>Artikel Pending</p>
                <a href="<?= base_url('pengurus/blog/pending') ?>" class="card-link">
                    Review <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="summary-card red">
            <div class="card-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="card-content">
                <h3><?= count($open_pengaduan) ?></h3>
                <p>Pengaduan Baru</p>
                <a href="<?= base_url('pengurus/pengaduan') ?>" class="card-link">
                    Tangani <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="summary-card green">
            <div class="card-icon">
                <i class="fas fa-poll"></i>
            </div>
            <div class="card-content">
                <h3><?= count($active_surveys) ?></h3>
                <p>Survei Aktif</p>
                <a href="<?= base_url('pengurus/surveys') ?>" class="card-link">
                    Kelola <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Members Table -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Calon Anggota Menunggu Verifikasi</h3>
            <a href="<?= base_url('pengurus/members/pending') ?>" class="btn btn-sm btn-primary">
                Lihat Semua
            </a>
        </div>
        <div class="section-body">
            <?php if (!empty($pending_members)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Kampus</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($pending_members, 0, 5) as $member): ?>
                                <tr>
                                    <td>
                                        <img src="<?= $member['foto'] ?? base_url('images/default-avatar.png') ?>"
                                            alt="<?= $member['nama_lengkap'] ?>"
                                            class="table-avatar">
                                    </td>
                                    <td><?= $member['nama_lengkap'] ?></td>
                                    <td><?= $member['email'] ?></td>
                                    <td><?= $member['nama_kampus'] ?? '-' ?></td>
                                    <td><?= date('d/m/Y', strtotime($member['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('pengurus/members/view/' . $member['id']) ?>"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">Tidak ada calon anggota yang menunggu verifikasi</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Articles -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Artikel Menunggu Review</h3>
            <a href="<?= base_url('pengurus/blog/pending') ?>" class="btn btn-sm btn-primary">
                Lihat Semua
            </a>
        </div>
        <div class="section-body">
            <?php if (!empty($pending_posts)): ?>
                <div class="article-list">
                    <?php foreach (array_slice($pending_posts, 0, 3) as $post): ?>
                        <div class="article-item">
                            <div class="article-content">
                                <h4><?= $post['judul'] ?></h4>
                                <p><?= character_limiter(strip_tags($post['konten']), 150) ?></p>
                                <div class="article-meta">
                                    <span><i class="fas fa-user"></i> <?= $post['author_name'] ?></span>
                                    <span><i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></span>
                                </div>
                            </div>
                            <div class="article-actions">
                                <a href="<?= base_url('pengurus/blog/review/' . $post['id']) ?>"
                                    class="btn btn-sm btn-primary">Review</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">Tidak ada artikel yang menunggu review</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Open Complaints -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Pengaduan Terbaru</h3>
            <a href="<?= base_url('pengurus/pengaduan') ?>" class="btn btn-sm btn-primary">
                Lihat Semua
            </a>
        </div>
        <div class="section-body">
            <?php if (!empty($open_pengaduan)): ?>
                <div class="complaint-list">
                    <?php foreach (array_slice($open_pengaduan, 0, 5) as $pengaduan): ?>
                        <div class="complaint-item">
                            <div class="complaint-header">
                                <h5><?= $pengaduan['subject'] ?></h5>
                                <span class="badge badge-<?= $pengaduan['status'] == 'new' ? 'danger' : 'warning' ?>">
                                    <?= ucfirst($pengaduan['status']) ?>
                                </span>
                            </div>
                            <p class="complaint-sender">
                                <i class="fas fa-user"></i> <?= $pengaduan['nama'] ?> - <?= $pengaduan['email'] ?>
                            </p>
                            <p class="complaint-preview"><?= character_limiter($pengaduan['pesan'], 100) ?></p>
                            <div class="complaint-footer">
                                <small><?= date('d/m/Y H:i', strtotime($pengaduan['created_at'])) ?></small>
                                <a href="<?= base_url('pengurus/pengaduan/view/' . $pengaduan['id']) ?>"
                                    class="btn btn-sm btn-outline-primary">Tangani</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">Tidak ada pengaduan baru</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Tools -->
    <div class="quick-tools">
        <h3>Quick Tools</h3>
        <div class="tools-grid">
            <a href="<?= base_url('pengurus/informasi/create') ?>" class="tool-card">
                <i class="fas fa-bullhorn"></i>
                <span>Kirim Informasi</span>
            </a>
            <a href="<?= base_url('pengurus/surveys/create') ?>" class="tool-card">
                <i class="fas fa-poll"></i>
                <span>Buat Survei</span>
            </a>
            <a href="<?= base_url('pengurus/blog/create') ?>" class="tool-card">
                <i class="fas fa-pen"></i>
                <span>Tulis Artikel</span>
            </a>
            <a href="<?= base_url('pengurus/members/export') ?>" class="tool-card">
                <i class="fas fa-file-excel"></i>
                <span>Export Data</span>
            </a>
            <a href="<?= base_url('pengurus/reports/salary') ?>" class="tool-card">
                <i class="fas fa-chart-bar"></i>
                <span>Data Survei Upah</span>
            </a>
            <a href="<?= base_url('pengurus/forum/moderate') ?>" class="tool-card">
                <i class="fas fa-comments"></i>
                <span>Moderasi Forum</span>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Auto-refresh for pending items
    setInterval(function() {
        checkPendingItems();
    }, 60000); // Check every minute

    function checkPendingItems() {
        fetch('<?= base_url('pengurus/api/pending-count') ?>')
            .then(response => response.json())
            .then(data => {
                updateBadges(data);
            });
    }

    function updateBadges(data) {
        // Update badge counts dynamically
        if (data.pending_members > 0) {
            showNotification('Ada ' + data.pending_members + ' calon anggota baru menunggu verifikasi');
        }
    }