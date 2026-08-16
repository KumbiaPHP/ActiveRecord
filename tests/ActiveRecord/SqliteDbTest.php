<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Db;

/**
 * @requires extension pdo_sqlite
 */
class SqliteDbTest extends TestCase
{
    public function testGetInstance()
    {
        $instance = Db::get('sqlite');

        $this->assertInstanceOf('PDO', $instance);
    }

    public function testSetConfig()
    {
        Db::setConfig([
            'dynamic' => [
                'dsn'      => 'sqlite::memory:',
                'username' => '',
                'password' => '',
            ],
        ]);

        $this->assertInstanceOf('PDO', Db::get('dynamic'));
    }
}
