<?php
require_once (APPPATH.'third_party/facebook.php');

/**
 * Libreria para Facebook Connect.
 * 
 * @author Marcos Gabarda
 *
 */
class CashewFacebook
{
    private $CI;
    
    private $facebook;
    
    private $app_id;
    
    private $app_secret;
    
    private $callback;
    
    private $me;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->config->load('facebook');
        
        $config = array();
        $this->app_id = $config['appId'] = $this->CI->config->item(
                'facebook_app_id');
        $this->app_secret = $config['secret'] = $this->CI->config->item(
                'facebook_app_secret');
        $this->callback = $this->CI->config->item('facebook_callback');
        
        $this->facebook = new Facebook($config);
    }
    
    public function init()
    {
        ?>
        <div id="fb-root"></div>
        <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '<?php echo $this->app_id ?>',
                status     : true,
                cookie     : true,
                xfbml      : true,
                oauth      : true,
            });
            FB.Event.subscribe('auth.login', function(response) {
                console.log('auth.login');
                console.log(response);
                window.location.href = '<?php echo site_url($this->callback.'/login?back='.urlencode(current_url())) ?>';
            });
            FB.Event.subscribe('auth.logout', function(response) {
                console.log('auth.logout');
                console.log(response);
                window.location.href = '<?php echo site_url($this->callback.'/logout?back='.urlencode(current_url())) ?>';
            });
            FB.getLoginStatus(function(response) {
                console.log('getLoginStatus');
                console.log(response);
                if (response.status === 'connected')
                {
                    $('.fb_button').click(function()
                    {
                        window.location.href = '<?php echo site_url($this->callback.'/login?back='.urlencode(current_url())) ?>';
                    });
                }
            });
        };
        (function(d){
            var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {
                return;
            }
            js = d.createElement('script'); js.id = id; js.async = true;
            js.src = "//connect.facebook.net/en_US/all.js";
            d.getElementsByTagName('head')[0].appendChild(js);
        }(document));
        </script>
        <?php
    }
    
    /**
     * 
     * Ejemplo de $this->me:
     * 
     *   array(15) { 
     *         ["id"]=> string(9) "529579832" 
     *         ["name"]=> string(19) "Marcos Gabarda Inat" 
     *         ["first_name"]=> string(6) "Marcos" 
     *         ["last_name"]=> string(12) "Gabarda Inat" 
     *         ["link"]=> string(37) "http://www.facebook.com/marcosgabarda" 
     *         ["username"]=> string(13) "marcosgabarda" 
     *         ["hometown"]=> array(2) { ["id"]=> string(15) "105500002816247" 
     *                                   ["name"]=> string(15) "Valencia, Spain" } 
     *         ["location"]=> array(2) { ["id"]=> string(15) "105500002816247" 
     *                                   ["name"]=> string(15) "Valencia, Spain" } 
     *         ["gender"]=> string(4) "male" 
     *         ["email"]=> string(23) "marcosgabarda@gmail.com" 
     *         ["timezone"]=> int(1) 
     *         ["locale"]=> string(5) "es_ES" 
     *         ["languages"]=> array(3) { [0]=> array(2) { ["id"]=> string(15) "108177092548456" 
     *                                                     ["name"]=> string(8) "Español" } 
     *                                    [1]=> array(2) { ["id"]=> string(15) "108411772516752" 
     *                                                     ["name"]=> string(9) "Valencian" } 
     *                                    [2]=> array(2) { ["id"]=> string(15) "110867825605119" 
     *                                                     ["name"]=> string(7) "English" } } 
     *         ["verified"]=> bool(true) 
     *         ["updated_time"]=> string(24) "2012-03-02T21:55:37+0000" }
     * 
     * @return mixed|NULL
     */
    public function me()
    {
        if ($this->facebook->getUser())
        {
            if (is_null($this->me))
            {
                try
                {
                    $this->me = $this->facebook->api('/me');
                }
                catch(FacebookApiException $e)
                {
                    return null;
                }
            }
            return $this->me;
        }
        return null;
    }
    
    /**
     * 
     */
    public function logout()
    {
        $params = array('next' => site_url('logout'));
        return $this->facebook->getLogoutUrl($params); 
    }
}