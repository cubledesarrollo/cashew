<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * Enter description here ...
 * @author Marcos Gabarda
 *
 */
class Migration_Add_info_to_consumer extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('oauth_consumer', array(
            'callback_url' => array(
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => true,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => true,
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
    }
    public function down()
    {
        $this->dbforge->drop_column('oauth_consumer', 'callback_url');
        $this->dbforge->drop_column('oauth_consumer', 'name');
        $this->dbforge->drop_column('oauth_consumer', 'description');
    }
}