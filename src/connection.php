<?php
class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                $host = getenv('DB_HOST') ?: 'db';
                $dbname = getenv('DB_NAME') ?: 'postgres';
                $user = getenv('DB_USER') ?: 'postgres';
                $password = getenv('DB_PASS') ?: 'postgres';

                $dsn = "pgsql:host=$host;dbname=$dbname";

                self::$connection = new PDO($dsn, $user, $password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
                exit;
            }
        }
        return self::$connection;
    }
}
