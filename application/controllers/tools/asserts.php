<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asserts extends CashewCLIController
{
    public function index()
    {
        /**
         * Cargar ASSERTS
         */
        $this->load->library('CashewAsserts');
        $this->cashewasserts->load_sql();
        /**
         * Cargar datos por defecto si estos no existen.
         */
        $this->cashewasserts->load_models();
        
        echo 'Initialized!'.PHP_EOL;
    }
}
