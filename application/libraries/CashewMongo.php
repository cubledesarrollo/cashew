<?php
/**
 * Librería para el acceso a base de datos MongoDB. Extiende la clase Mongo 
 * que proporcina el paquete "mongo" de PECL. Para instalarlo:
 * 
 * $ sudo pecl install mongo
 * 
 * @author Cuble Desarrollo S.L.
 *
 */
if (class_exists('Mongo'))
{
    class CashewMongo extends Mongo
    {
        public $db;
        
        function __construct()
        {
            $CI = & get_instance();
            $CI->load->config('mongo');
            
            $server = $CI->config->item('mongo_server');
            $database = $CI->config->item('mongo_database');
            
            if ($server)
            {
                parent::__construct($server);
            }
            else
            {
                parent::__construct();
            }
            
            $this->db = $this->$database;
        }
    }
}
else
{
    // Throw error
    throw new Exception('Mongo support must be installed (sudo pecl install mongo)');
    die();
}