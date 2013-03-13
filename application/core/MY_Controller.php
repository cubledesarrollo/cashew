<?php
/**
 *
 * Todos los controladores del API, tanto que necesiten de autentificacion por
 * OAuth como no, deberian extender de este controlador.
 *
 * @author Marcos Gabarda
 *
 */
class CashewAPIController extends CI_Controller
{
    /**
     * Usuario que realiza la peticion.
     * @var User
     */
    protected $current_user;

    protected $data = null;

    private $_supported_formats = array(
                'xml' => 'application/xml',
                'json' => 'application/json',
    );

    private $_response_format = null;

    private $_request_method = null;

    /**
     * Constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->current_user = null;
        $this->load->library('CashewLanguage');
        $this->load->library('CashewOAuth');
        $this->load->helper('xml');

        $this->_read_request_method();
        $this->_read_response_format();
        $this->_read_request_content();
        $this->_read_uri();
    }

    /**
     * Obliga a que la peticion se haya realizado con OAuth.
     */
    protected function _mandatory_oauth()
    {
        if (!$this->cashewoauth->check_request())
        {
            echo $this->cashewoauth->get_last_problem();
            exit();
        }
        else
        {
            $this->current_user = $this->cashewoauth->get_user();
        }
    }

    /**
     * Si se ha realizado con OAuth, se comprueba si es correcto, y se guarda
     * el usuario que ha realizado la peticion.
     */
    protected function _optional_oauth()
    {
        if ($this->cashewoauth->check_request())
        {
            $this->current_user = $this->cashewoauth->get_user();
        }
        else
        {
            /**
             * Si el metodo check_request no detecta una peticion OAuth
             * correcta, cambia la respuesta a 400 Bad Request. Como queremos
             * que los controladores que usen este metodo el OAuth sea
             * opcional, restablecemos la cabecera a 200 OK.
             */
            $this->output->set_status_header('200');
        }
    }

    /**
     * Obtiene el metodo de la request.
     */
    private function _read_request_method()
    {
        $method = strtolower($this->input->server('REQUEST_METHOD'));
        /**
         * Emulacion para cuando no se soporta cambiar el metodo.
         */
        if ( $this->input->get_post('_method'))
        {
            $method =  $this->input->get_post('_method');
        }
        if (in_array($method, array('get', 'delete', 'post', 'put')))
        {
            $this->_request_method = $method;
        }
        else
        {
            $this->_request_method = 'get';
        }
    }

    /**
     *
     * Enter description here ...
     */
    protected function _read_uri()
    {
        $array = $this->uri->uri_to_assoc(2);
        if ($array[$this->uri->segment(2)] !== false)
        {
            $this->data->id = $array[$this->uri->segment(2)];
        }
    }

    /**
     *
     * Enter description here ...
     */
    private function _read_request_content()
    {
        $type = $this->input->get_request_header('Content-type', TRUE);
        $get_content = $_GET;
        $this->data = (array) $this->data;
        foreach ($get_content as $key => $value)
        {
            $this->data[$key] = $this->input->get($key, true);
        }
        switch ($type)
        {
            case 'application/x-www-form-urlencoded':
                $post_content = $_POST;
                foreach ($post_content as $key => $value)
                {
                    $this->data[$key] = $this->input->post($key, true);
                }
                break;
            case 'application/json':
                $request_raw_content = file_get_contents("php://input");
                $json = (array) json_decode($this->security->xss_clean($request_raw_content));
                $this->data = array_merge($this->data, $json);
                break;
            case 'applicaction/xml':
                //TODO IMPORTANTE!!!
                break;
            default:
                break;
        }
        $this->data = (object)$this->data;
    }

    /**
     *
     * Enter description here ...
     */
    private function _read_response_format()
    {
        $type = 'text/html';
        $accept = $this->input->get_request_header('Accept', TRUE);
        if (preg_match('/.*\.(.+)$/', $this->uri->uri_string(), $match))
        {
            $type = $this->_supported_formats[$match[1]];
        }
        else if ($accept !== false)
        {
            $type = preg_split('/,/', $accept);
            $type = $type[0];
        }
        $this->_response_format = $type;
    }

    /**
     *
     * Envio de respuesta del API.
     *
     * @param array $data
     * @param string $http_code
     */
    protected function response($data, $http_code = '200')
    {
        $this->output->set_status_header($http_code);
        switch ($this->_response_format)
        {
            case 'application/xml':
                $keys = array_keys($data);
                $this->output->set_content_type('application/xml')
                             ->set_output("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" . array_to_xml($data[$keys[0]], $keys[0]));
                break;
            case 'application/json':
            default:
                $this->output->set_content_type('application/json')
                ->set_output(json_encode($data));
                break;
        }
    }

    public function _remap($method, $params = array())
    {
        switch ($this->_request_method)
        {
            case 'get':
                if (isset($this->data->id))
                {
                    if (method_exists($this, '_show'))
                    {
                        return call_user_func_array(array($this, '_show'), $params);
                    }
                }
                else
                {
                    if (method_exists($this, '_list'))
                    {
                        return call_user_func_array(array($this, '_list'), $params);
                    }
                }
                break;
            case 'post':
                if (method_exists($this, '_create'))
                {
                    return call_user_func_array(array($this, '_create'), $params);
                }
            case 'put':
                if (method_exists($this, '_update'))
                {
                    return call_user_func_array(array($this, '_update'), $params);
                }
                break;
            case 'delete':
                if (method_exists($this, '_delete'))
                {
                    return call_user_func_array(array($this, '_delete'), $params);
                }
                break;
            default:
                if (method_exists($this, $method))
                {
                    return call_user_func_array(array($this, $method), $params);
                }
                else
                {
                    $this->response(array('error' => 'Action not allowed'), '405');
                }
                break;
        }
    }
}

class CashewCLIController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (! $this->input->is_cli_request())
        {
            exit('No CLI access allowed');
        }
    }
}

/**
 *
 * Extension del controlador por defecto que añade soporte para plantillas.
 * Ejemplo de uso:
 *
 * class Dummy extends EC_Controller
 * {
 *     public function index()
 *     {
 *         $this->add_section('identificador_en_el_template', 'nombre_de_la_pagina');
 *         $this->render_page(); // Renderiza el template por defecto.
 *     }
 * }
 *
 * @author Marcos Gabarda
 *
 */
class CashewController extends CI_Controller
{
    /**
     *
     * Usuario actualmente indetificado.
     * @var User
     */
    protected $current_user;

    /**
     *
     * Titulo de la pagina.
     * @var string
     */
    protected $title;

    /**
     *
     * Identificador que se usa en el menu para saber la pagina activa.
     * @var array
     */
    protected $active = array();

    /**
     *
     * Lista de css que se quieren añadir en el template.
     * @var array
     */
    protected $css = array();

    /**
     * Lista de ficheros less que se quieren añadir en el template.
     *@var array
     */
    protected $less = array();

    /**
     *
     * Lista de js que se quieren añadir en el template.
     * @var array
     */
    protected $js = array();
    protected $js_footer = array();

    protected $meta = array();

    private $contents = array();

    private $responsive = false;
    
    private $test_ab    = false;
    
    /**
     * 
     */
    function __construct()
    {
        parent::__construct();
        $this->config->load('cashew');
        $this->load->library('CashewLanguage');
        $this->load->library('CashewAuth');
        $this->load->helper('url');
        $this->load->helper('template');
        $this->load->helper('form');
        $this->load->helper('xml');

        switch (ENVIRONMENT)
        {
            case 'development':
            case 'testing':
                // Cargamos LESS
                $this->add_js('less/less.min.js');
                
                // Añadimos JQuery y otros JavaScripts
                $this->add_js('jquery/jquery.js');
                $this->add_js('bootstrap/bootstrap-alert.js');
                
                // Cargamos el LESS de desarrollo
                $this->add_less('style.less');
                
                // Font-Awesome
                $this->add_css('font-awesome/font-awesome.css');
                
                break;
            case 'production':
                // Añadimos JQuery y otros JavaScripts
                $this->add_js('jquery/jquery.min.js');
                $this->add_js('bootstrap/bootstrap-alert.js');
                
                // Cargamos el CSS de produccion.
                $this->add_css('style.css');
                
                // Font-Awesome
                $this->add_css('font-awesome/font-awesome.min.css');
                
            default:
                break;
        }
        $this->add_css('default.css');
        
        // Comprobamos si existe session
        if (!session_id())
        {
            session_start();
        }
        
        // Si existe curren_url se la pasamos al before, que es quien 
        // guardara el referer.
        if(isset($_SESSION['current_url']))
        {
            $_SESSION['before_url'] = $_SESSION['current_url'];
        } 
        
        // Guardamos la url actual para que en la próxima página sepa de donde 
        // viene. 
        $_SESSION['current_url'] = current_url();
        
        
        //comprobamos si esta activa test ab
        if($this->config->item('cashew_test_ab'))
        {
            // devuelve false sino es testab, si es devuelve int corresponde a 
            // la version a mostrar
            $this->test_ab = is_test();
        }
    }

    /**
     * Usamos el _remap para crear automaticamente las llamadas
     * correspondientes al los metodos de CRUD.
     *
     * @param string $method
     * @param string $params
     */
    public function _remap($method, $params = array())
    {
        // NEW
        if ($method == 'new')
        {
            $method = '_new';
        }
        // CREATE
        else if ($method == 'index' && $this->request_method() == 'post')
        {
            $method = '_create';
        }
        else if (is_numeric($method) && $this->request_method() == 'post' &&
                count($params) == 0)
        {
            $params[0] = $method;
        	$method = '_create';
        }
        // SHOW
        else if (is_numeric($method) && count($params) == 0)
        {
            $params[0] = $method;
            $method = '_show';
        }
        else if (is_numeric($method) && count($params) == 1 &&
                $params[0] == 'edit')
        {
            // EDIT
            if ($this->request_method() == 'get')
            {
                $params[0] = $method;
                $method = '_edit';
            }
            // UPDATE
            else if ($this->request_method() == 'post')
            {
                $params[0] = $method;
                $method = '_update';
            }
        }
        // DELETE
        else if (is_numeric($method) && count($params) == 1 &&
                $params[0] == 'delete')
        {
            $params[0] = $method;
            $method = '_delete';
        }

        if (method_exists($this, $method))
        {
            return call_user_func_array(array($this, $method), $params);
        }
        show_404();
    }

    /**
     * Usada dentro de la acción de un controlador hace que sea obligatorio 
     * estar autenticado para acceder a esa acción.
     * 
     * @param string $redirect
     * @param string $message
     */
    protected function authentication_required($redirect='/login', $message='')
    {
        if (!$this->cashewauth->is_authenticated())
        {
            if (!empty($message))
            {
                $this->warning($message);
            }
            $this->session->set_flashdata('back', current_url());
            redirect($redirect);
        }
        else
        {
            $this->current_user = $this->cashewauth->current_user();
        }
    }
    
    /**
     * Usada dentro de la acción de un controlador hace que sea obligatorio no 
     * estar autenticado para acceder a esa acción.
     * 
     * @param string $redirect
     * @param string $message
     */
    protected function no_authentication_required($redirect='/', $message='')
    {
        if ($this->cashewauth->is_authenticated())
        {
            if (!empty($message))
            {
                $this->warning($message);
            }
            redirect($redirect);
        }
    }

    /**
     * Impide el acceso a un controlador de usuarios que no sean 
     * administradores.

     * @param string $message
     */
    protected function admin_authentication_required($message='')
    {
        $this->authentication_required();
        if ($this->current_user->type != 0)
        {
            if (!empty($message))
            {
                $this->warning($message);
            }
            redirect(before_url());
        }
    }
    
   /**
    *
    * Devuelve la variable REQUEST_METHOD.
    * @return string
    */
    protected function request_method()
    {
        return strtolower($this->input->server('REQUEST_METHOD'));
    }
    
    /**
     *
     * @param boolean $status
     */
    protected function responsive($status)
    {
        $this->responsive = $status;
    }

    /**
     *
     * Carga el contenido de la pagina. Por defecto se le añade los datos
     * asociados a la autentificacion de usuario.
     * @param string $page
     */
    protected function add_section($section, $page, $content = array())
    {
        $data = array('is_authenticated' => $this->cashewauth->is_authenticated(),
                      'current_user' => $this->cashewauth->current_user(),
                      'app' => $this->config->item('cashew_app_name'));
        $data = array_merge($data, $content);
        //Si no ess test  
        if(!$this->test_ab)
        {
            $this->contents[$section] = $this->load->view('pages/'.$page, $data, true);
        }
        else
        {
            $data = array_merge($data, array('version'=>$this->test_ab));
            $this->contents[$section] = $this->load->view('test_ab/'.$page.'_v'.$this->test_ab, $data, true);
        }
    }

    /**
     *
     * Añade un fichero CSS al template.
     * @param string $css_file
     */
    protected function add_css($css_file)
    {
        $this->css[] = $css_file;
    }
    
    /**
     *
     * Añade un fichero Less al template.
     * @param string $less_file
     */
    protected function add_less($less_file)
    {
        $this->less[] = $less_file;
    }

    /**
     *
     * Añade un fichero JavaScript al template.
     * @param string $js_file
     * @param boolean $footer: Agrega el JS al pie de la página.
     */
    protected function add_js($js_file, $footer=false)
    {
        if ($footer)
        {
            $this->js_footer[] =  $js_file;
        }
        else
        {
            $this->js[] =  $js_file;
        }
    }

   /**
    *
    * Enter description here ...
    * @param string $message
    */
    protected function warning($message)
    {
        $this->session->set_flashdata('warning', $message);
    }

    /**
     *
     * Enter description here ...
     * @param string $message
     */
    protected function error($message)
    {
        $this->session->set_flashdata('error', $message);
    }

    /**
     *
     * Enter description here ...
     * @param string $message
     */
    protected function success($message)
    {
        $this->session->set_flashdata('success', $message);
    }

    /**
     *
     * Une el template con el contenido.
     *
     */
    protected function render_page($template = 'main')
    {
        $data = array('app' => $this->config->item('cashew_app_name'),
                      'title' => $this->title,
                      'css' => $this->css,
                      'js' => $this->js,
                      'js_footer' => $this->js_footer,
                      'less' => $this->less,
                      'meta' => $this->meta,
                      'is_authenticated' => $this->cashewauth->is_authenticated(),
                      'current_user' => $this->cashewauth->current_user());
        $data = array_merge($data, $this->contents);
        $this->load->view('templates/'.$template, $data);
          
    }

    /**
     * @param Array $data
     */
    protected function render_xml($data)
    {
        $keys = array_keys($data);
        $this->output->set_content_type('application/xml')
                     ->set_output("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" . array_to_xml($data[$keys[0]], $keys[0]));
    }

    protected function render_json($data)
    {
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($data));
    }
}
