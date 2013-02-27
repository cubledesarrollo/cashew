<?php

class LabelsTest extends CashewTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->CI->load->model('User');
    }
    
    public function testUserLabels()
    {
        $user = $this->CI->User->register('test@test.com', 'pass');
        $this->assertNull($user->label('foo', 'es_ES'));
        $this->assertEquals('email', 
                $user->label('email', 'foo'));
        $this->assertEquals('password', 
                $user->label('password', 'es_ES'));
        $this->assertEquals('Correo electrónico', 
                $user->label('email', 'es_ES'));
        $this->assertEquals('E-Mail',
                $user->label('email', 'en_US'));
        $this->assertEquals('email',
                $user->attribute('E-Mail'));
        $this->assertEquals('email',
                $user->attribute('Correo electrónico'));
        $this->assertNotEquals('email',
                $user->attribute('Correo'));
        $this->assertNull($user->attribute('Correo'));
    }
}