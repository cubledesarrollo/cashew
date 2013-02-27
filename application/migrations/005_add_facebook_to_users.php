<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author Marcos Gabarda
 *
 */
class Migration_Add_facebook_to_users extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('users', array(
            'facebook' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ),
        ));
    }
    public function down()
    {
        $this->dbforge->drop_column('users', 'facebook');
    }
}