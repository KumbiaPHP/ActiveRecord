<?php

use ActiveRecord\Db;

class DbTest extends PHPUnit_Framework_TestCase
{

    public function testDbGet()
    {
        $instance  = Db::get($GLOBALS['config_database']);
        
        $this->assertInstanceOf('PDO', $instance);
        
        $instance2 = Db::get($GLOBALS['config_database']);
        
        $this->assertEquals($instance, $instance2);
        
        $instance3 = Db::get($GLOBALS['config_database'], true);
        
        $this->assertFalse($instance === $instance3);
    }

}
