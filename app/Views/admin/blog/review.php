<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Review Artikel
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .article-content {
        line-height: 1.8;
        font-size: 16px;
    }

    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .review-actions .widget-content {
        position: sticky;
        top: 20px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row layout-top-spacing">

    <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget-content widget-content-area br-6 p-4">

            <a href="<?= base_url('admin/blog/pending') ?>" class="btn btn-secondary mb-4">
                <i data-feather="arrow-left"></i> Kembali ke Daftar Review
            </a>

            <h2 class="mb-3"><?= esc($post['title']) ?></h2>

            <div class="mb-4">
                <span class="mr-3"><strong>Penulis:</strong> <?= esc($post['author_name'] ?? 'N/A') ?></span>
                <span><strong>Tanggal Kirim:</strong> <?= date('d M Y, H:i', strtotime($post['created_at'])) ?></span>
            </div>

            <?php if (!empty($post['featured_image']) && file_exists(FCPATH . $post['featured_image'])): ?>
                <div class="mb-4 text-center">
                    <img src="<?= base_url($post['featured_image']) ?>" alt="<?= esc($post['title']) ?>" class="img-fluid rounded">
                </div>
            <?php endif; ?>

            <div class="article-content">
                <?= $post['content'] // Tampilkan HTML asli dari editor 
                ?>
            </div>

        </div>
    </div>

    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing review-actions">
        <div class="widget-content widget-content-area br-6 p-4">
            <h4>Tindakan Review</h4>
            <hr>
            <p>Setelah membaca artikel, silakan pilih tindakan di bawah ini.</p>

            <form action="<?= base_url('admin/blog/approve/' . $post['id']) ?>" method="post" class="mb-3">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="approve_notes">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" id="approve_notes" class="form-control" rows="3" placeholder="Contoh: Artikel bagus, langsung terbitkan."></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-block">
                    <i data-feather="check-circle" class="mr-1"></i> Setujui & Publikasikan
                </button>
            </form>

            <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectionModal">
                <i data-feather="x-circle" class="mr-1"></i> Tolak Artikel
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectionModalLabel">Tolak Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/blog/reject/' . $post['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Anda akan menolak artikel berjudul "<strong><?= esc($post['title']) ?></strong>".</p>
                    <div class="form-group">
                        <label for="rejection_reason">Alasan Penolakan (Wajib diisi)</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" placeholder="Contoh: Perlu perbaikan pada bagian sumber data." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Artikel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>