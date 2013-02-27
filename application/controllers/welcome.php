<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @urli18n es Bienvenido
 * @urli18n en Welcome
 */
class Welcome extends CashewController
{
    /**
     * @urli18n es indice
     * @urli18n en index
     */
    public function index()
    {
        $this->add_section('content', 'welcome');
        $this->render_page();
    }

    /**
     * @urli18n es prueba
     * @urli18n en demo
     */
    public function demo()
    {
        $this->add_section('content', 'welcome');
        $this->render_page();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */