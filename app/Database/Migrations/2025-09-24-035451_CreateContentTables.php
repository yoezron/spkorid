<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContentTables extends Migration
{
    public function up()
    {
        // Tabel blog_posts
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'author_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'title'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'excerpt'        => ['type' => 'TEXT', 'null' => true],
            'content'        => ['type' => 'TEXT'],
            'featured_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'category'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tags'           => ['type' => 'TEXT', 'null' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['draft', 'pending_review', 'published', 'rejected'], 'default' => 'draft'],
            'view_count'     => ['type' => 'INT', 'default' => 0],
            'reviewed_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'reviewed_at'    => ['type' => 'TIMESTAMP', 'null' => true],
            'review_notes'   => ['type' => 'TEXT', 'null' => true],
            'published_at'   => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at'     => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'     => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('author_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('reviewed_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('blog_posts');

        // Tabel cms_pages, documents, etc.
        // ... (Tambahkan tabel konten lainnya di sini) ...
    }

    public function down()
    {
        $this->forge->dropTable('blog_posts');
        // ... (Drop tabel konten lainnya) ...
    }
}
