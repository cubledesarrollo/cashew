<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Libreria para gestionar Memcached.
 */
class CashewCache 
{

    private $CI;

    private $activate = false;
    
    private $memcached = null;
    
    private $expiration = 0;

    function __construct()
    {
        $this->CI = & get_instance();

        if (class_exists('Memcached'))
        {

            $this->CI->config->load('memcached');
            $this->expiration = $this->CI->config->item('memcached_expiration');
            $servers = $this->CI->config->item('memcached_servers');
            
            $this->memcached = new Memcached();
            
            foreach ($servers as $server)
            {
                $this->activate = $this->activate || 
                $this->memcached->addServer($server['host'], $server['port'], 
                        $server['priority']);
            }
        }
    }
    
    public function is_activate()
    {
        return $this->activate;
    }
    
    public function set($key, $var)
    {
        if (!$this->activate) return false;
        return $this->memcached->set($key, $var, $this->expiration);
    }
    
    public function get($key)
    {
        if (!$this->activate) return false;
        return $this->memcached->get($key);
    }
    
    public function delete($key)
    {
        if (!$this->activate) return false;
        return $this->memcached->delete($key);
    }
    
    public function stats()
    {
        if (!$this->activate) return false;
        return $this->memcached->getStats();
    }
}