<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * Libreria de autentificacion.
 *
 */
class CashewMultipleAuth
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
    private $session_name = 'users';
    
    /**
     * 
     * @var string
     */
    private $cookie_name = 'users_salt';
    
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
     * Añade al usuario $user a la session actual.
     * @param User $user
     */
    public function login($user)
    {
        // 1. Si no hay sesion creada, se crea el array de usuarios con $user
        // como unico elemento.
        if ($this->CI->session->userdata($this->session_name) === false)
        {
            $users = array($user->id => $user);
            $this->CI->session->set_userdata($this->session_name, 
                    serialize($users));
        }
        // 2. Si ya exite, se extrae el array y se añade a $user.
        else
        {
            $users = 
                unserialize($this->CI->session->userdata($this->session_name));
            $users[$user->id] = $user;
            $this->CI->session->set_userdata($this->session_name, 
                    serialize($users));
        }
    }
    
    /**
     * Des-identifica al usuario $user del sistema, o a todos los usuarios si 
     * no se indica ninguno.
     */
    public function logout($user = null)
    {
        if (is_null($user))
        {
            $this->CI->session->unset_userdata($this->session_name);
            $this->CI->session->sess_destroy();
        }
        else
        {
            $users =
                unserialize($this->CI->session->userdata($this->session_name));
            unset($users[$user->id]);
            if (count($users) == 0)
            {
                $this->CI->session->unset_userdata($this->session_name);
                $this->CI->session->sess_destroy();
            }
            else
            {
                $this->CI->session->set_userdata($this->session_name,
                        serialize($users));
            }
        }
    }

    /**
     * Comprueba si hay algún usuario esta identificado en el sistema.
     * @return boolean
     */
    public function is_authenticated()
    {
        if ($this->CI->session->userdata($this->session_name) === false)
        {
            return false;
        }
        $users =
            unserialize($this->CI->session->userdata($this->session_name));
        return !(count($users) == 0);
    }
    
    /**
     * Obtiene el usuario actual.
     * @return User
     */
    public function current_users()
    {
        if ($this->is_authenticated())
        {
            $users =
                unserialize($this->CI->session->userdata($this->session_name));
            return $users;
        }
        return array();
    }
    
    // TODO Se ha eliminado el soporte de autentifiación por cookies cuando 
    // se usa el login con multiples cuentas, 
}

