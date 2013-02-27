<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_users extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
            ),
            'salt' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
            ),
            'state' => array(
                'type' => 'ENUM',
                'constraint' => "'enabled', 'disable', 'unconfirmed'",
            ),
            'type' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1
            ),
            'created_at' => array(
                'type' => 'DATETIME',
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
            ),
            'ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '24',
            ),
            'locale' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}