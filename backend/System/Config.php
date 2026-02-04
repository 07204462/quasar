<?php

namespace App\System;

/**
 * Class Config
 * @package App\System
 */
class Config
{
    /**
     * Database Connection String
     *
     * @var string[]
     */
    protected array $conn;

    /**
     * Config constructor.
     */
    public function __construct(){
        // Database Configuration - using environment variables with defaults
        $this->conn = [
            // The hostname of your database server. Often this is ‘localhost’.
            'host' => getenv('DB_HOST') ?: 'db',
            // The username used to connect to the database.
            'username' => getenv('DB_USERNAME') ?: 'app_user',
            // The password used to connect to the database.
            'password' => getenv('DB_PASSWORD') ?: 'secret',
            // The name of the database you want to connect to.
            'db' => getenv('DB_DATABASE') ?: 'app_db',
            // The port used to connect to the database.
            'port' => getenv('DB_PORT') ?: '3306',
            // An optional table prefix which will added to the table name when running Query Builder queries.
            'prefix' => '',
            // The character set used in communicating with the database.
            'charset' => 'utf8mb4',
        ];
    }

    /**
     * dbConnection
     *
     * @return string[]
     */
    public function dbConnection(): array
    {
        return $this->conn;
    }
}
