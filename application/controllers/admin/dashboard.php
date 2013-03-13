<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Cuble Desarrollo
 *
 */
class Dashboard extends CashewController
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
        $this->add_css("admin/unicorn.main.css");
        $this->add_css("admin/unicorn.grey.css");
        // Carga de JS de administración.
        $this->add_js("admin/jquery.min.js", true);
        $this->add_js("admin/jquery-ui.custom.js", true);
        $this->add_js("admin/bootstrap.min.js", true);
        $this->add_js("admin/unicorn.js", true);
    }
    
    public function index()
    {
        $this->add_section('content', 'admin/dashboard');
        $this->render_page('admin/dashboard');
    }
}