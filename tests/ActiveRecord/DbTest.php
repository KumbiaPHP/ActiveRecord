<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Db;

class DbTest extends TestCase
{
    public function testGetInstance()
    {
        $instance = Db::get(getenv('DB'));

        $this->assertInstanceOf('PDO', $instance);
    }

    public function testGet()
    {
        $instance = Db::get(getenv('DB'));

        $instance2 = Db::get(getenv('DB'));

        $this->assertEquals($instance, $instance2);
    }
}
