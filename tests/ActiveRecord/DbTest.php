<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Db;

class DbTest extends TestCase
{
    /**
     * @requires extension pdo_sqlite
    */
    public function testGetInstance()
    {
        
        $instance = Db::get('sqlite');

        $this->assertInstanceOf('PDO', $instance);
    }

    /**
     * @requires extension pdo_mysql
     */
    public function testGet()
    {
        $instance = Db::get('mysql');

        $instance2 = Db::get('mysql');

        $this->assertEquals($instance, $instance2);
    }

    public function testGetThatDontExistInConfig()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp('/^No existen datos de conexión para la bd/');
        
        $instance = Db::get('no_exist');
    }

    public function testGetConfigWithoutPassword()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp('/No se pudo realizar la conexión con/');
        
        $instance = Db::get('no_password');
    }

     public function testSetConfig()
    {
        $config = ['dynamic' => [
            'dsn'      => 'pgsql:dbname=kumbia_test;host=localhost',
            'username' => 'postgres',
            'password' => '',
            'params'   => [
                \PDO::ATTR_PERSISTENT => \true, //conexión persistente
                \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION
                ]
            ]
        ];
        
        Db::setConfig($config);
        $instance = Db::get('dynamic');

        $this->assertInstanceOf('PDO', $instance);
     }
}
