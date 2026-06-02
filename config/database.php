<?php
/**
 * Database Connection Class
 */

class Database {
    private static $instance = null;
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;

    private function __construct() {
        $config = require_once __DIR__ . '/config.php';
        $db_config = $config['database'];
        
        $this->host = $db_config['host'];
        $this->username = $db_config['username'];
        $this->password = $db_config['password'];
        $this->database = $db_config['name'];
        $this->port = $db_config['port'];
        
        $this->connect();
    }

    private function connect() {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );

            if ($this->connection->connect_error) {
                throw new Exception('Database connection failed: ' . $this->connection->connect_error);
            }

            $this->connection->set_charset('utf8mb4');
        } catch (Exception $e) {
            die('Connection Error: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        $result = $this->connection->query($sql);
        if (!$result && APP_DEBUG) {
            die('Query Error: ' . $this->connection->error);
        }
        return $result;
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }

    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
