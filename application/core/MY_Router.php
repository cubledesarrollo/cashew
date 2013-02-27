<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sobreescritura de metodo de enrutamiento de CodeIgniter.
 * 
 * @author Roberto Rubio
 *
 */
class MY_Router extends CI_Router
{
    /**
     * 
     * @var boolean
     */
    var $urli18n = false;

    /**
     * 
     * @var array
     */
    var $alias;
    
    /**
     * 
     */
    function __construct()
    {
        parent::__construct();
        //existe en el fichero
        if(is_file(APPPATH.'config/urli18n.php'))
        {
            include(APPPATH.'config/urli18n.php');
            $this->urli18n = $config['urli18n'];
            $this->alias =  $alias;
        }
        //$this->lang =& load_class('CashewLanguage');
    }
    
    /**
     * (non-PHPdoc)
     * @see CI_Router::_validate_request()
     */
    function _validate_request($segments)
    {   
        //Comprobamos si esta activo el urli18n
        if($this->urli18n)
        {
            $segments = $this->alias_by_segment($segments);
        }
        return parent::_validate_request($segments);
    }


    public function alias_by_segment($segments)
    {   
        //suponemos que es español
        if(!count($segments)>0)
        {
             return $segments;
        }   
     
        if(!isset($this->alias[$segments[0]]))
        {
            return $segments;
        }
        
        //extraemos controller 
        $controller  =   $this->alias[$segments[0]]['name'];
        //extraemos el lenguaje
        $lang = $this->alias[$segments[0]]['lang'];
        //guardamos lang en session
        if (!session_id())
        {
            session_start();
        }
        $_SESSION['l'] = $lang;
        
        //comprobamos si no existe el metodo para el controlador
        if(!isset($this->alias[$segments[0]]['method'][$segments[1]]))
        {
            return $segments;
        }
          
        //introducimos el method
        $method = $this->alias[$segments[0]]['method'][$segments[1]];

        $segments[0] = $controller;
        $segments[1] = $method;
        return $segments;
        
    }
}