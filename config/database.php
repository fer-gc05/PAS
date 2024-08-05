<?php
class Connection
{
    private static $connection;

    public static function Connect()
    {
        $server = 'localhost';
        $db_name = 'pas';
        $username = 'root';
        $password = '';

        self::$connection = new mysqli($server, $username, $password, $db_name);

        if (self::$connection->connect_error) {
            die("Connection error: " . self::$connection->connect_error);
        }

        if (!self::$connection->set_charset("utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", self::$connection->error);
            exit();
        }

        return self::$connection;
    }

    public static function getConnection()
    {
        if (self::$connection) {
            return self::$connection;
        } else {
            return self::Connect();
        }
    }

    public static function closeConnection()
    {
        if (self::$connection) {
            self::$connection->close();
        }
    }
}