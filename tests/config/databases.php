<?php

return [
    //Mysql
    'mysql' => [
        'dsn'      => 'mysql:host=127.0.0.1;dbname=kumbia_test;charset=utf8',
        'username' => 'root',
        'password' => '',
        'params'   => [
            \PDO::ATTR_PERSISTENT => \true, //conexión persistente
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION
        ]
    ],
    //Pgsql
    'pgsql' => [
        'dsn'      => 'pgsql:dbname=kumbia_test;host=localhost',
        'username' => 'postgres',
        'password' => '414141',
        'params'   => [
            \PDO::ATTR_PERSISTENT => \true, //conexión persistente
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION
            ]
    ],
    //Sqlite
    'sqlite' => [
        'dsn' => 'sqlite::memory:',
        'username' => '',
        'password' => '',
    ],

    // bad connections to tests errors
    'no_dsn' => [
        'dsn' => ''
    ],
    'no_password' => [
        'dsn' => 'pgsql:dbname=no_exist;host=localhost'
    ],
    'bad_credentials' => [
        'dsn' => 'pgsql:dbname=no_exist;host=localhost',
        'password' => 'as'
    ]


    //More connections
];
