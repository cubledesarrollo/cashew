<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migrate extends CashewCLIController
{
    public function index()
    {
        $this->load->library('migration');
        if ( ! $this->migration->current())
        {
            die($this->migration->error_string().PHP_EOL);
        }
        echo 'Migrated!'.PHP_EOL;
    }
}
