<?php
/**
 * Libreria para Twitter Connect.
 *
 * @author Marcos Gabarda
 *
 */
class CashewTwitter
{
    private $CI;
    
    private $consumer_key;
    
    private $consumer_secret;
    
    private $callback;
    
    private $base_url;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->config->load('twitter');
        
        $this->consumer_key = $this->CI->config->item('twitter_consumer_key');
        $this->consumer_secret = $this->CI->config->item('twitter_consumer_secret');
        $this->callback = $this->CI->config->item('twitter_callback');
        $this->base_url = 'https://api.twitter.com';
    }
    
    /**
     * 
     */
    public function request_token()
    {
        try
        {
            $oauth = new OAuth($this->consumer_key, $this->consumer_secret);
            
            $request_token_info = $oauth->getRequestToken(
                    $this->base_url.'/oauth/request_token');
            
            if(!empty($request_token_info))
            {
                $this->CI->session->set_userdata('twitter_oauth_token_secret',
                        $request_token_info['oauth_token_secret']);
                redirect($this->base_url.'/oauth/authorize'.
                        "?oauth_token=".$request_token_info['oauth_token']);
            } else {
                print "Failed fetching access token, response was: " . $oauth->getLastResponse();
            }
        } catch(OAuthException $E) {
            print_r($E);
            echo "Response: ". $E->lastResponse . "\n";
        }
    }
    
    public function access_token($token, $verifier)
    {
        try
        {
            $oauth = new OAuth($this->consumer_key, $this->consumer_secret);
            
            $oauth->setToken($token, 
                    $this->CI->session->userdata('twitter_oauth_token_secret'));
            
            $access_token_info = $oauth->getAccessToken(
                    $this->base_url."/oauth/access_token", '',
                    $verifier);
            
            if(!empty($access_token_info))
            {
            
                $this->CI->session->set_userdata('twitter_oauth', $access_token_info);
            } 
            else
            {
                print "Failed fetching access token, response was: " . $oauth->getLastResponse();
            }
        } catch(OAuthException $E) {
            echo "Response: ". $E->lastResponse . "\n";
        }
    }
    
    public function data()
    {
        return $this->CI->session->userdata('twitter_oauth');
    }
}