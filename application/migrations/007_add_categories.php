<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_categories extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'TEXT',
            ),
            'hash_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
            ),
            'category_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true,
                'null' => true
            ),
            'created_at' => array(
                'type' => 'DATETIME',
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('categories', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('categories');
    }
}