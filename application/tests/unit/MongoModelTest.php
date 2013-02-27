<?php

class MongoModelTest extends CashewTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }
    
    public function testCreation()
    {
        $mongo_model = new CashewMongoModel();
        $this->assertNotNull($mongo_model);
    }
    
    public function testFilter()
    {
        $mongo_model = new CashewMongoModel();
        $this->assertTrue(is_array($mongo_model->filter()));
        $models = $mongo_model->filter();
    }
    
    public function testGetby()
    {
        $mongo_model = new CashewMongoModel();
        $this->assertTrue(is_null($mongo_model->get_by('name','roberto'))); 
    }
    
    
    public function testAll()
    {
        $mongo_model = new CashewMongoModel();
        $this->assertTrue(is_array($mongo_model->all()));
        
        $models = $mongo_model->all();
        
    }
    
    public function testDelete()
    {
        $mongo_model = new CashewMongoModel();
        $models = $mongo_model->all();
        foreach ($models as $model)
        {
            $model->delete();
        }
    }
}