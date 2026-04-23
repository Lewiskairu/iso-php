<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $databaseUrl = config('db.database_url');

        if ($databaseUrl) {
            $parts    = parse_url((string) $databaseUrl);
            $driver   = $parts['scheme'] ?? 'mysql';
            $host     = $parts['host']   ?? '127.0.0.1';
            $port     = $parts['port']   ?? 3306;
            $database = ltrim((string) ($parts['path'] ?? ''), '/');
            $user     = urldecode($parts['user'] ?? '');
            $pass     = urldecode($parts['pass'] ?? '');
        } else {
            $driver   = (string) config('db.driver',   'mysql');
            $host     = (string) config('db.host',     '127.0.0.1');
            $port     = (string) config('db.port',     '3306');
            $database = (string) config('db.database');
            $user     = (string) config('db.username');
            $pass     = (string) config('db.password');
        }

        $charset = (string) config('db.charset', 'utf8mb4');
        $socket  = (string) config('db.socket',  '');

        // Use Unix socket only if explicitly configured (e.g. local XAMPP/LAMPP dev)
        // On shared hosting or production, leave db.socket empty to use TCP/IP
        if ($socket !== '') {
            $dsn = sprintf(
                '%s:unix_socket=%s;dbname=%s;charset=%s',
                $driver, $socket, $database, $charset
            );
        } else {
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $driver, $host, $port, $database, $charset
            );
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => false,
        ];

        if ($driver === 'mysql') {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ANSI_QUOTES'))";
        }

        self::$pdo = new PDO($dsn, $user, $pass, $options);
        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $conn = self::connection();
        
        // Auto-convert standard double-quoted identifiers ("key", "createdAt") 
        // to backticks (`key`, `createdAt`) for MySQL/MariaDB.
        if ($conn->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            $sql = preg_replace('/"([a-zA-Z0-9_]+)"/', '`$1`', $sql);
        }

        $statement = $conn->prepare($sql);
        $statement->execute($params);
        return $statement;
    }
}