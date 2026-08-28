<?php

$env = static function (string $name, string $default): string {
    $value = getenv($name);

    return false === $value ? $default : $value;
};

return [
    'mysql' => [
        'dsn'      => $env('MYSQL_DSN', 'mysql:host=127.0.0.1;dbname=kumbia_test;charset=utf8'),
        'username' => $env('MYSQL_USER', 'root'),
        'password' => $env('MYSQL_PASSWORD', ''),
        'params'   => [
            \PDO::ATTR_PERSISTENT => \true,
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION
        ]
    ],
    'pgsql' => [
        'dsn'      => $env('PGSQL_DSN', 'pgsql:dbname=kumbia_test;host=127.0.0.1'),
        'username' => $env('PGSQL_USER', 'postgres'),
        'password' => $env('PGSQL_PASSWORD', ''),
        'params'   => [
            \PDO::ATTR_PERSISTENT => \true,
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION
        ]
    ],
    'sqlite' => [
        'dsn' => $env('SQLITE_DSN', 'sqlite::memory:'),
        'username' => '',
        'password' => '',
    ],
    'no_password' => [
        'dsn' => $env('PGSQL_INVALID_DSN', 'pgsql:dbname=no_exist;host=127.0.0.1'),
    ],
];
