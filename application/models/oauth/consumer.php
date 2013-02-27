<?php
class Consumer extends CashewModel
{
    protected function _meta()
    {
        $this->load->model('oauth/Consumer_nonce');
        $this->table = 'oauth_consumer';
    }
    
    public function has_nonce($nonce, $timestamp)
    {
        $consumer_nonce = $this->Consumer_nonce->filter(array('consumer_id' => $this->id, 
                'nonce' => $nonce, 'timestamp' => $timestamp));
        if (count($consumer_nonce) != 0)
        {
            return true;
        }
        return false;
    }
    
    public function add_nonce($nonce)
    {
        $consumer_nonce = new Consumer_nonce();
        $consumer_nonce->consumer_id = $this->id;
        $consumer_nonce->nonce =  $nonce;
        $consumer_nonce->timestamp = time();
        $consumer_nonce->save();
    }
    
    /**
     * 
     * Genera un nuevo consumidor, con todos sus tokens
     * a partir del usuario pasado por parametro. 
     * @param Users $user
     */
    public function generate($user)
    {
        $consumer = new Consumer();
        $consumer->user_id = $user->id;
        $consumer->consumer_key = sha1(OAuthProvider::generateToken(20, true));
        $consumer->consumer_secret = sha1(OAuthProvider::generateToken(20, true));
        $consumer->active = true;
        return $consumer; 
    }
}