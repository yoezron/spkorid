<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run()
    {
        // Insert Forum Categories
        $categories = [
            [
                'name' => 'Diskusi Umum',
                'slug' => 'diskusi-umum',
                'description' => 'Diskusi umum seputar serikat pekerja kampus',
                'icon' => 'comments',
                'color' => 'primary',
                'order_position' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Hak dan Kewajiban',
                'slug' => 'hak-kewajiban',
                'description' => 'Diskusi mengenai hak dan kewajiban anggota serikat',
                'icon' => 'balance-scale',
                'color' => 'success',
                'order_position' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kesejahteraan',
                'slug' => 'kesejahteraan',
                'description' => 'Diskusi tentang kesejahteraan pekerja kampus',
                'icon' => 'hand-holding-heart',
                'color' => 'info',
                'order_position' => 3,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kebijakan Kampus',
                'slug' => 'kebijakan-kampus',
                'description' => 'Pembahasan kebijakan-kebijakan kampus yang mempengaruhi pekerja',
                'icon' => 'university',
                'color' => 'warning',
                'order_position' => 4,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Advokasi',
                'slug' => 'advokasi',
                'description' => 'Diskusi dan bantuan advokasi untuk anggota',
                'icon' => 'gavel',
                'color' => 'danger',
                'order_position' => 5,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Pengembangan Karir',
                'slug' => 'pengembangan-karir',
                'description' => 'Tips dan diskusi pengembangan karir di lingkungan kampus',
                'icon' => 'chart-line',
                'color' => 'secondary',
                'order_position' => 6,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Event dan Kegiatan',
                'slug' => 'event-kegiatan',
                'description' => 'Informasi dan diskusi event serta kegiatan serikat',
                'icon' => 'calendar-alt',
                'color' => 'purple',
                'order_position' => 7,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Saran dan Kritik',
                'slug' => 'saran-kritik',
                'description' => 'Sampaikan saran dan kritik untuk kemajuan serikat',
                'icon' => 'lightbulb',
                'color' => 'teal',
                'order_position' => 8,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('forum_categories')->insertBatch($categories);

        // Insert sample threads (optional - only if you want sample data)
        $threads = [
            [
                'category_id' => 1,
                'user_id' => 1, // Assuming user with ID 1 exists
                'title' => 'Selamat Datang di Forum SPK!',
                'slug' => 'selamat-datang-forum-spk-' . time(),
                'content' => 'Selamat datang di forum diskusi Serikat Pekerja Kampus! 

Forum ini adalah tempat kita berbagi informasi, berdiskusi, dan saling mendukung sebagai sesama pekerja kampus. 

Mari kita jaga forum ini tetap kondusif dan bermanfaat untuk semua anggota.

Silakan perkenalkan diri Anda di thread ini!',
                'views' => 0,
                'is_pinned' => 1,
                'is_featured' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 2,
                'user_id' => 1,
                'title' => 'Panduan Hak Cuti Tahunan Pekerja Kampus',
                'slug' => 'panduan-hak-cuti-tahunan-' . (time() + 1),
                'content' => 'Berikut adalah panduan mengenai hak cuti tahunan untuk pekerja kampus:

1. Setiap pekerja berhak atas cuti tahunan minimal 12 hari kerja per tahun
2. Cuti dapat diambil setelah masa kerja 12 bulan
3. Pengajuan cuti harus dilakukan minimal 2 minggu sebelumnya
4. Cuti yang tidak diambil dapat diakumulasikan maksimal 2 tahun

Silakan diskusikan pengalaman Anda mengenai pengambilan cuti di kampus masing-masing.',
                'views' => 0,
                'is_pinned' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 3,
                'user_id' => 1,
                'title' => 'Survey Kepuasan Gaji Pekerja Kampus 2025',
                'slug' => 'survey-kepuasan-gaji-2025-' . (time() + 2),
                'content' => 'Serikat akan melakukan survey kepuasan gaji untuk seluruh anggota.

Tujuan survey:
- Memetakan kondisi kesejahteraan anggota
- Sebagai dasar advokasi ke pihak kampus
- Membuat rekomendasi perbaikan sistem penggajian

Survey akan dilaksanakan mulai minggu depan. Mohon partisipasi seluruh anggota.',
                'views' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Only insert sample threads if needed
        // Comment out if you don't want sample data
        // $this->db->table('forum_threads')->insertBatch($threads);

        echo "Forum seeder completed successfully.\n";
    }
}
