<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Db;

/**
 * @requires extension pdo_mysql
 */
class MysqlDbTest extends TestCase
{
    public function testGetReturnsSameInstance()
    {
        $instance = Db::get('mysql');

        $this->assertSame($instance, Db::get('mysql'));
    }
}
