<?php

class UserTest extends CashewTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->CI->load->library('CashewAuth');
        $this->CI->load->model('User');
        $user1 = $this->CI->User->register('test1@test.com', 'pass');
        $user1->state = 'enabled';
        $user1->save();
        $user2 = $this->CI->User->register('test2@test.com', 'pass');
        $user2->sate = 'disabled';
        $user2->save();
    }
    /**
     * 
     * TEST - Comprobar que un usuario se puede registrar con exito.
     */
    public function testRegisterUser()
    {
        $user = $this->CI->User->register('test3@test.com', 'pass');
        $this->CI->config->load('cashew');
        $admin_enabled = $this->CI->config->item('cashew_admin_enabled');
        if ($admin_enabled)
        {
            $this->assertCount(4, $this->CI->User->all());
        }
        else
        {
            $this->assertCount(3, $this->CI->User->all());
        }
    }
   
    public function testDeleteUser()
    {
        $user = $this->CI->User->get(2);
        $user->delete();
        $this->CI->config->load('cashew');
        $admin_enabled = $this->CI->config->item('cashew_admin_enabled');
        if ($admin_enabled)
        {
            $this->assertCount(2, $this->CI->User->all());
        }
        else
        {
            $this->assertCount(1, $this->CI->User->all());
        }
        
    }
    
    public function testNotRegister()
    {
        $user = $this->CI->User->register('test1@test.com', 'pass');
        $this->assertNull($user);
    }
    
    public function testLoginUser()
    {
        $user = $this->CI->User->get_by('email', 'test1@test.com');
        $user1 = $this->CI->User->authenticate('test1@test.com', 'pass');
        $this->assertNotNull($user1);
        $this->assertNotEquals($user->salt, $user1->salt);
        $user2 = $this->CI->User->authenticate('test3@test.com', 'pass');
        $this->assertNull($user2);
        $this->CI->cashewauth->login($user1);
        $this->assertNotNull($this->CI->cashewauth->current_user());
    }
    
    public function testLogoutUser()
    {
        $this->CI->cashewauth->logout();
        $this->assertFalse($this->CI->cashewauth->is_authenticated());
        $this->assertNull($this->CI->cashewauth->current_user());
    }
    
    public function testUpdateUser()
    {
        $user = $this->CI->User->get(1);
        $this->assertNotNull($user);
        $old = $user->email;
        $user->email = "test2@test.com";
        $user->save();
        $this->assertNotEquals($user->email, $old);
    }
    
    public function testCookies()
    {
        $user = $this->CI->User->get(1);
        $this->assertNotNull($user);
        $user_tmp = 
            $this->CI->cashewauth->cookie_login($user->id.'-'.$user->salt);
        $this->assertNotNull($user_tmp);
        $this->assertEquals($user->id, $user_tmp->id);
        $this->assertNotNull($this->CI->cashewauth->current_user());
    }
}