<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Db;

/**
 * @requires extension pdo_pgsql
 */
class PgsqlDbTest extends TestCase
{
    public function testGetConfigWithoutPassword()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/No se pudo realizar la conexión con/');

        Db::get('no_password');
    }
}
