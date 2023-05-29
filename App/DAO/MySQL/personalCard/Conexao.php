<?php

namespace App\DAO\MySQL\personalCard;

abstract class Conexao
{
    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct()
    {
        $host = getenv('personalCard_MYSQL_HOST');
        $port = getenv('personalCard_MYSQL_PORT');
        $user = getenv('personalCard_MYSQL_USER');
        $pass = getenv('personalCard_MYSQL_PASSWORD');
        $dbname = getenv('personalCard_MYSQL_DBNAME');

        $dsn = "mysql:host={$host};dbname={$dbname};port={$port}";

        $this->pdo = new \PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );
    }
}
