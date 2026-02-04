<?php

namespace App\System;

use PDO;
use PDOException;

/**
 * Class Main
 *
 * Bootstrapper class for the application
 *
 * @package App\System
 */
class Main
{
    /**
     * PDO Database connection
     *
     * @var PDO
     */
    public PDO $db;

    /**
     * Config class
     *
     * @var Config
     */
    protected Config $Config;

    /**
     * Main constructor.
     * @param bool $initialize_db
     */
    public function __construct($initialize_db = true)
    {
        // Create Instance of Config
        $this->Config = new Config();
        
        // Create PDO connection directly
        $config = $this->Config->dbConnection();
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db']};charset={$config['charset']}";
        
        try {
            $this->db = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Build API response
     *
     * @param array $data Array of data to build
     * @param array $response_column Array of allowed columns in response
     * @param bool $status Flag for sending success or fail response
     * @return false|string JSON-encoded response or array of values
     */
    public function buildApiResponse(array $data = array(), array $response_column = array(), bool $status = true)
    {
        // Check if data is a multi-dimensional array
        if (count($data) === count($data, COUNT_RECURSIVE)) {
            // Not multi-dimensional
            $filtered = array_intersect_key($data, array_flip($response_column));
        } else {
            // Check if array has a normal multi-dimensional indexing
            if (array_key_exists(0, $data)) {
                $filtered = array_map(function ($arr) use ($response_column) {
                    return array_intersect_key($arr, array_flip($response_column));
                }, $data);
            } else {
                $filtered = array_intersect_key($data, array_flip($response_column));
            }
        }

        return $status
            ? $this->jsonSuccessResponse($filtered)
            : $this->jsonFailResponse($data);
    }

    /**
     * Trim payload
     *
     * @param $payload
     * @return array|string
     */
    public function trimPayload($payload)
    {
        return is_array($payload) ? array_map(array($this, 'trimPayload'), $payload) : trim($payload);
    }

    /**
     * JSON success response
     *
     * @param array $data
     * @return false|string
     */
    protected function jsonSuccessResponse(array $data = array())
    {
        return json_encode([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * JSON fail response
     *
     * @param array $data
     * @return false|string
     */
    protected function jsonFailResponse(array $data = array())
    {
        return json_encode([
            'success' => false,
            'error' => $data['message'] ?? 'An error occurred',
            'data' => $data
        ]);
    }

    /**
     * JSON error invalid parameters
     *
     * @return false|string
     */
    public function jsonErrorInvalidParameters()
    {
        return $this->jsonFailResponse(['message' => 'Invalid parameters']);
    }
}