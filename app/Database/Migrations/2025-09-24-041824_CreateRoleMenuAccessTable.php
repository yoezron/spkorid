<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoleMenuAccessTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'role_id'    => ['type' => 'INT', 'unsigned' => true],
            'menu_id'    => ['type' => 'INT', 'unsigned' => true],
            'can_view'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'can_add'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'can_edit'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'can_delete' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['role_id', 'menu_id']);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('menu_id', 'menus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('role_menu_access');
    }

    public function down()
    {
        $this->forge->dropTable('role_menu_access');
    }
}
