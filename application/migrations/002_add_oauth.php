<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_oauth extends CI_Migration
{
    public function up()
    {
        /**
         * Consumer.
         */
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'consumer_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
            ),
            'consumer_secret' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('oauth_consumer', true);
        /**
         * Consumer Nonce.
         */
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'consumer_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'timestamp' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'nonce' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('oauth_consumer_nonce', true);
        /**
         * Token
         */
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'type' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'consumer_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'token' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
            ),
            'token_secret' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
            ),
            'state' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
           'callback_url' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
            ),
           'verifier' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('oauth_token', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('oauth_consumer');
        $this->dbforge->drop_table('oauth_consumer_nonce');
        $this->dbforge->drop_table('oauth_token');
    }
}