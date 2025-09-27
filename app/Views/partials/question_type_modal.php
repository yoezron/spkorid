<!-- =================================================================
KODE MODAL DIPERBAIKI
- Menghapus atribut onclick. Event handling akan dilakukan oleh JavaScript
  di view utama.
================================================================== -->
<div class="modal fade" id="questionTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tipe Pertanyaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <?php if (!empty($question_types)) : ?>
                        <?php foreach ($question_types as $type => $details) : ?>
                            <div class="col-md-4">
                                <!-- ATRIBUT ONCLICK DIHAPUS, digantikan event listener -->
                                <div class="type-option h-100" data-type="<?= $type ?>">
                                    <i class="material-icons-outlined"><?= $details['icon'] ?? 'help_outline' ?></i>
                                    <div class="type-label mt-2"><?= $details['label'] ?? 'Tipe Kustom' ?></div>
                                    <p class="type-desc mb-0"><?= $details['description'] ?? 'Deskripsi tidak tersedia.' ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Tidak ada tipe pertanyaan yang tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>