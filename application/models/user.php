<?php
/**
 * Modelo para un usuario.
 * 
 * @author Marcos Gabarda
 *
 */
class User extends CashewModel
{
    
    /**
     * 
     * @var string
     */
    private $hash_function = 'sha256';
    
    /**
     * Constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->helper('string');
        $this->config->load('cashew');
    }
    
    /**
     * (non-PHPdoc)
     * @see CashewModel::_meta()
     */
    public function _meta()
    {
        $this->create_label('email', 'Correo electrónico', 'es_ES');
        $this->create_label('email', 'E-Mail', 'en_US');
    }
    
    /**
     * Calcula el codigo de validacion de usuario.
     * 
     * @return string
     */
    function generate_validation_code()
    {
        return hash_hmac($this->hash_function, $this->email,
                $this->created_at.hash($this->hash_function, $this->password));
    }
    
    /**
     * 
     * @param string $email
     * @param string $password
     * @return NULL|User
     */
    public function authenticate($email, $password)
    {
        $hashed_password = hash($this->hash_function, $password);
        $users = $this->User->filter(array('email' => $email,
                                           'password' => $hashed_password));
        if (count($users) == 1 && 
                $users[0]->state == 'enabled')
        {
            // Regenerar el salt
            $users[0]->salt = random_string('alnum', 64);
            $users[0]->save();
            return $users[0];
        }
        return null;
    }
    
    /**
     * 
     * @param int $id
     * @param string $salt
     * @return User|NULL
     */
    public function salt_authenticate($id, $salt)
    {
        $users = $this->User->filter(array('id' => $id, 
                'salt' => $salt));
        if (count($users) == 1 && 
            $users[0]->state == 'enabled')
        {
            return $users[0];
        }
        return null;
    }
    
    /**
     * 
     * @param string $email
     * @param string $password
     * @return NULL|User
     */
    public function register($email, $password)
    {
        if (!is_null($this->get_by('email', $email)))
        {
            return null;
        }
        $user = new User();
        $user->email = $email;
        $user->password = hash($this->hash_function,$password);
        $user->salt = random_string('alnum', 64);
        $user->state = 'enabled';
        if ($this->config->item('cashew_user_validate_enabled') && 
                ENVIRONMENT != 'testing' && 
                $email != $this->config->item('cashew_admin_user'))
        {
            $user->state = 'unconfirmed';
            
            // Enviar correo de confirmación
            $CI = & get_instance();
            $CI->load->library('email');
            $CI->load->config('email');
            $CI->email->from($this->config->item('smtp_user'),
                    $this->config->item('cashew_app_name'));
            $CI->email->to($user->email);
            $CI->email->subject(sprintf(_('Valida tu cuenta de %s'), 
                    $this->config->item('cashew_app_name')));
            $code = $user->generate_validation_code();
            $data = array ('code' => $code);
            $message = $this->load->view('templates/email/verification', 
                    $data, true);
            $CI->email->message($message);
            $CI->email->send();
        }
        $user->ip = $this->input->server('REMOTE_ADDR');
        $user->save();
        return $user;
    }
    
    /**
     * True si el usuario es administrador.
     * 
     * @return boolean
     */
    public function is_admin()
    {
        return $this->type == 0;
    }
    
    /**
     *
     * @param srting $new_password
     */
    public function update_password($new_password)
    {
        $this->password = hash($this->hash_function,$new_password);
        $this->salt = random_string('alnum', 64);
        $this->save();
    }
}