<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Libreria para la internacionalizacion con gettext.
 */
class CashewLanguage
{
    private $CI;

    private $locale;

    private $installed_locales = array();

    /**
     * Inicializa la configuracion de gettext. En primer lugar se evalua un
     * parametro que se puede pasar por GET para forzar el idioma, en segundo
     * lugar, se comprueba si el usuario esta registrado, y si lo esta, se
     * configura el idioma asociado.
     *
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model('User');
        $this->CI->load->config('cashew');
        $this->CI->load->library('session');
        $this->CI->load->library('CashewAuth');

        // Configuracion de los idiomas instalados.
        $this->installed_locales = array(
                "es"    => array("name" => _('Español'),
                                 "locale" => 'es_ES'),
                "es_ES" => array("name" => _('Español'),
                                 "locale" => 'es_ES'),
                "en"    => array("name" => _('Ingles'),
                                 "locale" => 'en_US'),
                "en_US" => array("name" => _('Ingles'),
                                 "locale" => 'en_US')
                );
        /**
         * Preferencia de selección de idioma:
         * 
         * 1. Variable GET 'l'.
         * 2. Variable de sesión 'l'.
         * 3. Valor por defecto de la aplicación.
         * 
         */
        
        if ($this->CI->input->get('l', true) !== false)
        {
            $this->locale = $this->CI->input->get('l', true);
            $this->set_session_locale($this->locale);
        }
        else if ($this->get_session_locale() !== false)
        {
            $this->locale = $this->get_session_locale();
        }
        else
        {
            $this->locale = $this->CI->config->item('cashew_default_language');
            $this->set_session_locale($this->locale);
        }
        
        /**
         * Configuramos Gettext.
         */
        if (isset($this->installed_locales[$this->locale]))
        {
            $domain = "messages";
            $locale = $this->installed_locales[$this->locale]['locale'].'.UTF-8';
            setlocale(LC_MESSAGES, $locale);
            bindtextdomain($domain, APPPATH."language/locales");
            bind_textdomain_codeset($domain, 'UTF-8');
        }

    }
    
    /**
     * 
     * @param string $locale
     */
    public function set_session_locale($locale)
    {
        $this->CI->session->set_userdata('locale', $locale);
    }

    /**
     * 
     */
    public function get_session_locale()
    {
        return $this->CI->session->userdata('locale');
    }
}