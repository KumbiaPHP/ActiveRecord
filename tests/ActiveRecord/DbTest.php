<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Db;

class DbTest extends TestCase
{
    public function testGetThatDontExistInConfig()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^No existen datos de conexión para la bd/');
        
        Db::get('no_exist');
    }
}
