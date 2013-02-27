<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Twitter extends CashewController {

    function __construct()
    {
        parent::__construct();
        $this->load->library('CashewTwitter');
        $this->load->library('CashewAuth');
        $this->load->model('User');
    }

    public function login()
    {
        $this->cashewtwitter->request_token();
    }

    public function callback()
    {
        $token = $this->input->get('oauth_token', true);
        $verifier = $this->input->get('oauth_verifier', true);
        $this->cashewtwitter->access_token($token, $verifier);
        $data = $this->cashewtwitter->data();
        if ($data)
        {
            $user = $this->User->get_by('twitter', $data['user_id']);
            if (is_null($user))
            {
                redirect(site_url('/connect/twitter/register'));
            }
            $this->cashewauth->login($user);
            redirect(site_url('/home'));
        }
        else
        {
            redirect(site_url(''));
        }
    }

    public function register()
    {
        if ($twitter_data = $this->cashewtwitter->data())
        {
            $data = array('screen_name' => $twitter_data['screen_name']);
            $this->add_section('content', 'connect/register', $data);
            $this->render_page();
        }
        else
        {
            redirect(site_url(''));
        }
    }
    public function signup()
    {
        if ($this->request_method() == 'post')
        {
            $twitter_data = $this->cashewtwitter->data();
            $email = $this->input->post('email', true);
            $user = $this->User->register($email, random_string('alnum'));
            if (is_null($user))
            {
                $this->error(_('Correo electrónico o nombre de usuario en uso'));
                redirect(site_url('login'));
            }
            $user->twitter = $twitter_data['user_id'];
            $user->save();
            $this->success(_('Te has registrado con éxito.'));
            $this->cashewauth->login($user);
            redirect(site_url('/home'));
        }
    }
}