<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fb extends CashewController {

    function __construct()
    {
        parent::__construct();
        $this->load->library('CashewFacebook');
        $this->load->library('CashewAuth');
        $this->load->model('User');
    }

    public function login()
    {
        $back = $this->input->get('back', true);
        $me = $this->cashewfacebook->me();
        if (!is_null($me))
        {
            $user = $this->User->get_by('facebook', $me['id']);
            if (is_null($user))
            {
                $user = $this->User->register($me['email'],
                        random_string());
                if (is_null($user))
                {
                    $this->error(_('Correo electrónico o nombre de usuario en uso'));
                    redirect($back);
                }
                $user->facebook = $me['id'];
                $user->save();
            }
            $this->cashewauth->login($user);
            redirect($back);
        }
    }

    public function logout()
    {
        $back = $this->input->get('back', true);
        if ($back !== false)
        {
            redirect($back);
        }
        else
        {
            redirect(site_url(''));
        }
    }
}