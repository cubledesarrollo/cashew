<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Cuble Desarrollo
 *
 */
class Admin extends CashewController
{
    function __construct()
    {
        parent::__construct();
        // Reseteo de estilos, Less y JavaScript.
        $this->css = array();
        $this->less = array();
        $this->js = array();
        // Cargar estilos de administración.
        $this->add_css("admin/bootstrap.min.css");
        $this->add_css("admin/bootstrap-responsive.min.css");
        $this->add_css("admin/unicorn.login.css");
        // Carga de JS de administración.
        $this->add_js("admin/jquery.min.js", true);
        $this->add_js("admin/unicorn.login.js", true);
    }
    
    public function index()
    {
        $this->add_section('content', 'admin/login');
        $this->render_page('admin/login');
    }
}