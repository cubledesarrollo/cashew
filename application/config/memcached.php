<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Tiempo de expiracion de la cache.
 */
$config['memcached_expiration'] = 60;

/**
 * Lista de servidores de Memcached.
 */
$config['memcached_servers'] = array(
	array('host' => 'localhost',
		  'port' => '11211',
	      'priority' => 1)
	);