<?php

class RoutesTest extends CashewTestCase
{
    
    public function setUp()
    {
        parent::setUp();
        include_once(BASEPATH.'core/Router.php');
        include_once(APPPATH.'core/MY_Router.php');
    }
    
    public function testNoExistingRoutes()
    {
        $my_routes = new MY_Router();
        
        $segments = array('bar', 'foo');
        $ret = $my_routes->alias_by_segment($segments);
        $this->assertCount(2, $ret);
        $this->assertEquals($ret[0], 'bar');
        $this->assertEquals($ret[1], 'foo');
        
    }
    
    public function testExistingRoutes()
    {
        $my_routes = new MY_Router();
        $segments = array('bienvenido', 'indice');

        $ret = $my_routes->alias_by_segment($segments);
        $this->assertCount(2, $ret);
        
        $this->assertEquals($ret[0], 'welcome');
        $this->assertEquals($ret[1], 'index');
        
        $segments = array('welcome', 'indice');

        $ret = $my_routes->alias_by_segment($segments);
        $this->assertCount(2, $ret);
        
        $this->assertEquals($ret[0], 'welcome');
        $this->assertEquals($ret[1], 'indice');
        
        $segments = array('bienvenido', 'prueba');

        $ret = $my_routes->alias_by_segment($segments);
        $this->assertCount(2, $ret);
        
        $this->assertEquals($ret[0], 'welcome');
        $this->assertEquals($ret[1], 'demo');
    }
}
