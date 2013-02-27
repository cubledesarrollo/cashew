<?php
/**
 * Libreria para gestionar ficheros SQL que se tengan que cargar en la base de
 * datos.
 *
 * @author Marcos Gabarda
 *
 */
class CashewAsserts
{
    private $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
    }
    
    /**
     * Carga todos los ficheros que haya en la carpeta 'application/asserts/'.
     * @param string $assert
     */
    function load_sql($assert=null)
    {
        $asserts_dir = APPPATH.'/asserts/';
        if (is_null($assert))
        {
            $this->CI->load->helper('file');
            $asserts = (get_filenames($asserts_dir));
            foreach($asserts as $assert)
            {
                $info = pathinfo($asserts_dir.$assert);
                if ($info['extension'] == 'sql')
                {
                    $assert_sql = read_file($asserts_dir.$assert);
                    $assert_sql = preg_replace('/\/\*(.*)\*\//', '', $assert_sql);
                    $assert_sql = preg_replace('/\/\*(.*)\*\/;/', '', $assert_sql);
                    $assert_sql = preg_replace('/-- (.*)\n/', '', $assert_sql);
                    $assert_array = explode(';', $assert_sql);
                    foreach($assert_array as $query)
                    {
                        $query = trim($query);
                        if(!empty($query))
                        {
                            $this->CI->db->query($query);
                        }
                    }
                }
            }
        }
        else
        {
            $assert_sql = read_file($asserts_dir.$assert);
            $assert_sql = preg_replace('/\/\*(.*)\*\//', '', $assert_sql);
            $assert_sql = preg_replace('/\/\*(.*)\*\/;/', '', $assert_sql);
            $assert_sql = preg_replace('/-- (.*)\n/', '', $assert_sql);
            $assert_array = explode(';', $assert_sql);
            foreach($assert_array as $query)
            {
                $query = trim($query);
                if(!empty($query))
                {
                    $this->CI->db->query($query);
                }
            }
        }
    }
    
    /**
     * Carga los modelos por defecto del sistema.
     */
    public function load_models()
    {
        // Usuario de administracion
        $this->CI->config->load('cashew');
        $this->CI->load->model('User');
        $admin_enabled = $this->CI->config->item('cashew_admin_enabled');
        if ($admin_enabled)
        {
            $email = $this->CI->config->item('cashew_admin_user');
            $password = $this->CI->config->item('cashew_admin_password');
            if (is_null($this->CI->User->get_by('email', $email)))
            {
                $user = $this->CI->User->register($email, $password);
                $user->type = 0;
                $user->state = 'enabled';
                $user->save();
            }
        }
    }
}