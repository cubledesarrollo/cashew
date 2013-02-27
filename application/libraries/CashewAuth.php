<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * Libreria de autentificacion.
 *
 */
class CashewAuth
{
   /**
    * 
    * @var CI_Controller
    * 
    */
    private $CI;
    
    /**
     * 
     * @var string
     */
    private $session_name = 'user_id';
    
    /**
     * 
     * @var string
     */
    private $cookie_name = 'user_salt';
    
    /**
     * Constructor
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model('User');
        $this->CI->load->library('session');
    }
    
    /**
     * Registra en la sesion al usuario acutal.
     * @param User $user
     */
    public function login($user)
    {
        $this->CI->session->set_userdata($this->session_name, $user->id);
    }
    
    /**
     * Des-identifica al usuario del sistema.
     */
    public function logout()
    {
        $this->CI->session->unset_userdata($this->session_name);
        $this->CI->session->sess_destroy();
    }
    
    /**
     * Comprueba si hay un usuario esta identificado en el sistema.
     * @return boolean
     */
    public function is_authenticated()
    {
        if ($this->CI->session->userdata($this->session_name) === false)
        {
            return false;
        }
        return true;
    }
    
    /**
     * Obtiene el usuario actual.
     * @return User
     */
    public function current_user()
    {
        if ($this->is_authenticated())
        {
            return $this->CI->User->get(
                    $this->CI->session->userdata($this->session_name));
        }
        return null;
    }
    
    /**
     * Accede al sitema usando la cookie. El parametro cookie se utiliza en 
     * labores de testeo.
     * 
     * @param string $cookie
     * @return User|null
     */
    public function cookie_login($cookie=null)
    {
        if (is_null($cookie))
        {
            $cookie = $this->CI->input->cookie($this->cookie_name, true);
        }
        if (preg_match('/(.+)-(.+)/', $cookie, $matches) != 0)
        {
            $user = $this->CI->User->salt_authenticate($matches[1], 
                    $matches[2]);
            if (!is_null($user))
            {
                $this->login($user);
                return $user;
            }
        }
        return null;
    }
    
    /**
     * Crea una cookie válidad para que se recuerde el acceso del usuario al 
     * sistema.
     * 
     * @param User $user
     */
    public function cookie_save(User $user)
    {
        $this->CI->config->load('cashew');
        $app = $this->config->item('cashew_app_name');
        $cookie = array(
                'name'   => $this->cookie_name,
                'value'  => $user->id.'-'.$user->salt,
                'expire' => '86500',
                'path'   => '/',
                'prefix' => $app.'_',
                'secure' => TRUE
        );
        $this->CI->input->set_cookie($cookie);
    }
}

