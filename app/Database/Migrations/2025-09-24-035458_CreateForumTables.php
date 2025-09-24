<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateForumTables extends Migration
{
    public function up()
    {
        // Tabel forum_categories
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'description'    => ['type' => 'TEXT', 'null' => true],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'order_priority' => ['type' => 'INT', 'default' => 0],
            'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'     => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('forum_categories');

        // Tabel forum_threads
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'user_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'content'       => ['type' => 'TEXT'],
            'is_pinned'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_locked'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'view_count'    => ['type' => 'INT', 'default' => 0],
            'reply_count'   => ['type' => 'INT', 'default' => 0],
            'last_reply_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'last_reply_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'    => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'forum_categories', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('last_reply_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('forum_threads');

        // Tabel forum_replies
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'thread_id'   => ['type' => 'INT', 'unsigned' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'reply_to_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'content'     => ['type' => 'TEXT'],
            'is_edited'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'edited_at'   => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at'  => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'updated_at'  => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('thread_id', 'forum_threads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('reply_to_id', 'forum_replies', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('forum_replies');
    }

    public function down()
    {
        $this->forge->dropTable('forum_replies');
        $this->forge->dropTable('forum_threads');
        $this->forge->dropTable('forum_categories');
    }
}
