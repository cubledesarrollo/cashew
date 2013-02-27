<?php

class CategoryTest extends CashewTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->CI->load->model('Category');
        $this->CI->load->model('Alias');
    }
    
    public function testRootCategory()
    {
        $c = new Category();
        $c->name = 'Cat.';
        $c->save();
        $this->assertCount(1, $this->CI->Category->all());
        $this->assertTrue($c->is_root());
        
        $foo = $this->CI->Category->get($c->id);
        $this->assertEquals($c->name, 'Cat.');
    }
   
    public function testSetParent()
    {
        $c = new Category();
        $c->name = 'Cat.';
        $c->save();
        $son = new Category();
        $son->name = 'Cat.';
        $son->parent = $c;
        $son->save();
        
        $this->assertEquals($son->category_id, $c->id);
    }
    
    public function testGetParent()
    {
        $c = new Category();
        $c->name = 'Cat.';
        $c->save();
        $son = new Category();
        $son->name = 'Cat.';
        $son->parent = $c;
        $son->save();
        
        $this->assertNotNull($son->parent);
        $this->assertEquals($son->parent->id, $c->id);
    }

    public function testRemoveParent()
    {
        $c = new Category();
        $c->name = 'Cat.';
        $c->save();
        $son = new Category();
        $son->name = 'Cat.';
        $son->parent = $c;
        $son->save();
        
        $this->assertNotNull($son->parent);
        $this->assertEquals($son->parent->id, $c->id);
        
        $son->parent = null;
        $son->save();
        
        $foo = $this->CI->Category->get($son->id);
        
        $this->assertNull($foo->parent);
    }
    
    public function testChildren()
    {
        $c = new Category();
        $c->name = 'Cat.';
        $c->save();
        $son = new Category();
        $son->name = 'Cat.';
        $son->parent = $c;
        $son->save();
        $son = new Category();
        $son->name = 'Cat.';
        $son->parent = $c;
        $son->save();
        $this->assertCount(2, $c->children());
    }
    
    public function testCategoriesAlias()
    {
        $c = new Category();
        $c->name = 'Cat.';
        $c->save();
        
        $c->add_alias('foo');
        $c->save();
        
        $this->assertCount(1, $this->CI->Alias->all());
        
        $cs = $this->CI->Category->filter_by_alias('foo');
        $this->assertCount(1, $cs);
        $this->assertInstanceOf('Category', $cs[0]);
        $this->assertEquals($cs[0]->id, $c->id);
    }
    
    public function testCategoriesToArray()
    {
        $c = new Category();
        $c->name = 'Cat.';
        $c->save();
        
        $c->add_alias('foo');
        $c->save();
        
        $c->add_alias('bar');
        $c->save();
        
        $json = json_encode($c->to_array());
        $decode = json_decode($json);
        
        //$this->assertArray($decode->category->aliases);
        $this->assertCount(2, $decode->category->aliases);
    }
}