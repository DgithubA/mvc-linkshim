<?php
namespace Lms\Core;

class Database
{
    private static ?self $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $db_connection = $_ENV['DB_CONNECTION'];
        if ($db_connection === 'mysql' or $db_connection === 'pgsql') {
            $db_host = $_ENV['DB_HOST'];
            $db_port = $_ENV['DB_PORT'];
            $db_name = $_ENV['DB_DATABASE'];
            $db_user = $_ENV['DB_USERNAME'];
            $db_pass = $_ENV['DB_PASSWORD'];
            $dsn = "$db_connection:dbname=$db_name;port=$db_port;host=$db_host";
        } elseif ($db_connection === 'sqlite') {
            $db_name = $_ENV['DB_DATABASE'];
            $dsn = "$db_connection:$db_name";
        } else throw new \Exception('Unsupported database type');

        $this->pdo = new \PDO($dsn, $db_user ?? null, $db_pass ?? null);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}

?>
