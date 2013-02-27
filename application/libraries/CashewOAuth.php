<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Libreria para gestionar OAuth.
 */
class CashewOAuth 
{
    /**
     * Instancia de CodeIgniter.
     */
    private $CI;
    
    /**
     * Proveedor de OAuth
     * @var OAuthProvider
     */
    public $provider;
    
    /**
     * Consumidor que realiza la llamada.
     * @var Consumer
     */
    public $consumer;
    
    /**
     * Usuario que autoriza el token.
     * @var User
     */
    public $user;
    
    /**
     * True en caso de que haya habido algun error.
     * @var boolean
     */
    private $oauth_error;
    
    /**
     * Registra el ultimo problema que se ha producido con OAuth.
     * @var string
     */
    private $oauth_last_problem;
    
    /**
     * Constructor. 
     */
    function __construct()
    {
        $this->CI =& get_instance();
        $this->provider = new OAuthProvider();
        $this->provider->consumerHandler(array($this, 'check_consumer'));
        $this->provider->timestampNonceHandler(array($this, 'check_nonce'));
        $this->provider->tokenHandler(array($this, 'check_token'));
        $this->provider->setRequestTokenPath('/oauth/request_token');
        
        $this->CI->load->model('User');
        $this->CI->load->model('oauth/Token');
        $this->CI->load->model('oauth/Consumer');
        $this->CI->load->helper('url');
        
        $this->authentification_url = base_url("/oauth/login/");
    }
    
    /**
     * 
     * Metodo que se utiliza para comprobar si el consumer key. Si existe, 
     * se guarda el consumer secret en el provider.
     * 
     * @param OAuthProvider $provider
     */
    public function check_consumer($provider)
    {
        $consumer = $this->CI->Consumer->get_by('consumer_key', $provider->consumer_key);
        $this->consumer = $consumer;
        if(!is_null($consumer))
        {
            if(!$consumer->active)
            {
                return OAUTH_CONSUMER_KEY_REFUSED;
            }
            else
            {
                $provider->consumer_secret = $consumer->consumer_secret;
                return OAUTH_OK;
            }
        }
        return OAUTH_CONSUMER_KEY_UNKNOWN;
    }
    
    /**
     * Compruena el nonce y el timestamp de la peticion.
     * @param OAuthProvider $provider
     * @return string
     */
    public function check_nonce($provider)
    {
        if($this->provider->timestamp < time() - 5*60)
        {
            return OAUTH_BAD_TIMESTAMP;
        }
        else if($this->consumer->has_nonce($provider->nonce, $this->provider->timestamp))
        {
            return OAUTH_BAD_NONCE;
        }
        else
        {
            $this->consumer->add_nonce($this->provider->nonce);
            return OAUTH_OK;
        }
    }
    
    /**
     * Comprueba que el token sea valido.
     * @param OAuthProvider $provider
     * @return string
     */
    public function check_token($provider)
    {
        $token = $this->CI->Token->get_by('token', $provider->token);
        if(is_null($token))
        {
            return OAUTH_TOKEN_REJECTED;
        }
        // TODO Gestionar los estados del token. Por defecto no puede ser ni 1 ni 2.
        /*else if($token->is_access() && $token->state == 1)
        {
            return OAUTH_TOKEN_REVOKED;
        }
        else if($token->is_request() && $token->state == 2)
        {
            return OAUTH_TOKEN_USED;
        }*/
        else if($token->is_request() && $token->verifier != $provider->verifier)
        {
            return OAUTH_VERIFIER_INVALID;
        }
        $provider->token_secret = $token->token_secret;
        $this->user = $this->CI->User->get($token->user_id);
        return OAUTH_OK;
    }
    
    /**
     * Metodo que que tiene que usar desde los controladores que requieran 
     * la autentificacion por OAuth para funcionar.
     * @return boolean
     */
    public function check_request()
    {
        try
        {
            $this->provider->checkOAuthRequest();
        }
        catch(OAuthException $E)
        {
            $this->oauth_last_problem = OAuthProvider::reportProblem($E);
            $this->oauth_error = true;
            return false;
        }
        return true;
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function set_request_token_query()
    {
        $this->provider->isRequestTokenEndpoint(true);
        $this->provider->addRequiredParameter("oauth_callback");
    }
    
    /**
     * 
     * Enter description here ...
     * @return string
     */
    public function generate_request_token()
    {
        $consumer = $this->CI->Consumer->get_by('consumer_key', $this->provider->consumer_key);
        $token = new Token();
        $token->consumer_id = $consumer->id;
        $token->type = 1;
        $token->token = sha1(OAuthProvider::generateToken(20,true));
        $token->token_secret = sha1(OAuthProvider::generateToken(20,true));
        
        if (empty($this->provider->callback))
        {
            $token->callback_url = $consumer->callback_url;
        }
        else
        {
            $token->callback_url = $this->provider->callback;
        }
        
        $token->save();
        return "authentification_url=".$this->authentification_url.
               "&oauth_token=".$token->token."&oauth_token_secret=".
                $token->token_secret."&oauth_callback_confirmed=true";
    }
    
    public function generate_access_token()
    {
        /**
         * Generar nuevos tokens.
         */
        $access_token = sha1(OAuthProvider::generateToken(20,true));
        $secret = sha1(OAuthProvider::generateToken(20,true));
        
        /**
         * Cambiar a token de acceso.
         */
        $token = $this->CI->Token->get_by('token', $this->provider->token);
        $token->type = 2;
        $token->verifier = '';
        $token->callback_url = '';
        $token->token = $access_token;
        $token->token_secret = $secret;
        $token->save();

        return "oauth_token=".$access_token."&oauth_token_secret=".$secret;
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $token
     */
    public function generate_verifier($token)
    {
        $verifier = sha1(OAuthProvider::generateToken(20,true));
        return $verifier;
    }
    
    /**
     * 
     * Enter description here ...
     * @return User
     */
    public function get_user()
    {
        return $this->user;
    }
    
    /**
     * 
     * Enter description here ...
     * @return string
     */
    public function get_last_problem()
    {
        return $this->oauth_last_problem;
    }
}

/* End of file EC_OAuth.php */