<?php

namespace App\System;

/**
 * Class Engine
 * 
 * ThingEngineer Pattern - Base engine class for modular functionality
 * Provides common utilities and patterns for module engines
 *
 * @package App\System
 */
class Engine
{
    /**
     * Database connection
     *
     * @var \PDO
     */
    public \PDO $db;

    /**
     * Configuration
     *
     * @var Config
     */
    protected Config $config;

    /**
     * Module name
     *
     * @var string
     */
    protected string $moduleName;

    /**
     * Table name
     *
     * @var string
     */
    protected string $table;

    /**
     * Engine constructor.
     *
     * @param string $moduleName
     * @param string $table
     */
    public function __construct(string $moduleName, string $table)
    {
        $this->moduleName = $moduleName;
        $this->table = $table;
        
        // Initialize configuration
        $this->config = new Config();
        
        // Initialize database connection
        $config = $this->config->dbConnection();
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db']};charset={$config['charset']}";
        
        try {
            $this->db = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get all records
     *
     * @param array $filters
     * @return array
     */
    public function getAll(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($filters)) {
            $whereClauses = [];
            foreach ($filters as $field => $value) {
                $whereClauses[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        $sql .= " ORDER BY id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Get record by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $setClause = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "{$column} = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }

    /**
     * Delete record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$id]);
    }

    /**
     * Validate required fields
     *
     * @param array $data
     * @param array $requiredFields
     * @return array|true
     */
    public function validateRequiredFields(array $data, array $requiredFields)
    {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        return empty($missing) ? true : $missing;
    }

    /**
     * Sanitize input data
     *
     * @param array $data
     * @return array
     */
    public function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Build success response
     *
     * @param array $data
     * @return array
     */
    public function buildSuccessResponse(array $data = []): array
    {
        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Build error response
     *
     * @param string $message
     * @param array $additionalData
     * @return array
     */
    public function buildErrorResponse(string $message, array $additionalData = []): array
    {
        return [
            'success' => false,
            'error' => $message,
            'data' => $additionalData
        ];
    }
}