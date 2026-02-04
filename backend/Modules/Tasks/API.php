<?php

namespace App\Modules\Tasks;

use App\System\Engine;
use App\Helpers\GlobalHelpers;

/**
 * Class API
 * 
 * Tasks Module API - Implements ThingEngineer pattern
 * @package App\Modules\Tasks
 */
class API
{
    /**
     * Engine instance
     *
     * @var Engine
     */
    protected Engine $engine;

    /**
     * Table name
     *
     * @var string
     */
    protected string $table;

    /**
     * Acceptable parameters
     *
     * @var array
     */
    protected array $accepted_parameters;

    /**
     * Response column
     *
     * @var array
     */
    protected array $response_column;

    /**
     * GlobalHelpers instance
     *
     * @var GlobalHelpers
     */
    protected GlobalHelpers $helpers;

    /**
     * API constructor.
     */
    public function __construct()
    {
        $this->table = 'tasks';
        
        // Initialize Engine with ThingEngineer pattern
        $this->engine = new Engine('Tasks', $this->table);

        $this->accepted_parameters = [
            'id',
            'title',
            'description',
            'status',
            'priority',
            'created_at',
            'updated_at'
        ];

        $this->response_column = [
            'id',
            'title',
            'description',
            'status',
            'priority',
            'created_at',
            'updated_at'
        ];

        $this->helpers = new GlobalHelpers();
    }

    /**
     * HTTP GET handler
     *
     * @param array $payload
     * @param bool $api
     * @return false|string
     */
    public function httpGet($payload = array(), $api = true)
    {
        // Basic validation
        if (!is_array($payload)) {
            return json_encode($this->engine->buildErrorResponse('Invalid parameters'));
        }

        try {
            // Fetch specific task by ID
            if (isset($payload['id'])) {
                $task = $this->engine->getById((int)$payload['id']);
                
                if (!$task) {
                    return json_encode($this->engine->buildErrorResponse('Task not found'));
                }
                
                $queryResult = $task;
            } else {
                // Fetch all tasks with optional filtering
                $filters = [];
                if (isset($payload['status'])) {
                    $filters['status'] = $payload['status'];
                }
                
                $queryResult = $this->engine->getAll($filters);
            }

            // Filter response columns
            $filteredResult = $this->filterResponseColumns($queryResult);
            
            return $api ? json_encode($this->engine->buildSuccessResponse($filteredResult)) : $filteredResult;
        } catch (\Exception $e) {
            return json_encode($this->engine->buildErrorResponse($e->getMessage()));
        }
    }

    /**
     * Setup database table
     *
     * @return false|string
     */
    public function setupTable()
    {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS tasks (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
                    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
            
            $this->engine->db->exec($sql);
            return json_encode($this->engine->buildSuccessResponse(['message' => 'Database table created successfully']));
        } catch (\Exception $e) {
            return json_encode($this->engine->buildErrorResponse($e->getMessage()));
        }
    }

    /**
     * HTTP POST handler
     *
     * @param $payload
     * @return false|string
     */
    public function httpPost($payload)
    {
        // Basic validation
        if (!is_array($payload)) {
            return json_encode($this->engine->buildErrorResponse('Invalid parameters'));
        }

        try {
            // Validate required fields
            $required_fields = ['title'];
            $validation = $this->engine->validateRequiredFields($payload, $required_fields);
            
            if ($validation !== true) {
                return json_encode($this->engine->buildErrorResponse(
                    'Missing required fields',
                    ['missing_fields' => $validation]
                ));
            }

            // Sanitize input
            $payload = $this->engine->sanitizeInput($payload);

            // Set default values
            $payload['status'] = $payload['status'] ?? 'pending';
            $payload['priority'] = $payload['priority'] ?? 'medium';
            $payload['created_at'] = date('Y-m-d H:i:s');
            $payload['updated_at'] = date('Y-m-d H:i:s');

            // Create task using Engine
            $id = $this->engine->create($payload);
            
            if ($id) {
                $payload['id'] = $id;
                $filteredResult = $this->filterResponseColumns($payload);
                return json_encode($this->engine->buildSuccessResponse($filteredResult));
            } else {
                return json_encode($this->engine->buildErrorResponse('Failed to create task'));
            }
        } catch (\Exception $e) {
            return json_encode($this->engine->buildErrorResponse($e->getMessage()));
        }
    }

    /**
     * HTTP PUT handler
     *
     * @param null|int $id
     * @param $payload
     * @return false|string
     */
    public function httpPut($id = null, $payload = array())
    {
        if (empty($id) || !is_numeric($id)) {
            return json_encode($this->engine->buildErrorResponse('Invalid parameters'));
        }

        try {
            // Check if task exists
            $existingTask = $this->engine->getById((int)$id);
            
            if (!$existingTask) {
                return json_encode($this->engine->buildErrorResponse('Task not found'));
            }

            // Validate payload
            foreach ($payload as $key => $value) {
                if (!in_array($key, $this->accepted_parameters)) {
                    return json_encode($this->engine->buildErrorResponse('Invalid parameters'));
                }
            }

            // Sanitize input
            $payload = $this->engine->sanitizeInput($payload);
            
            // Add updated timestamp
            $payload['updated_at'] = date('Y-m-d H:i:s');

            // Update task using Engine
            $success = $this->engine->update((int)$id, $payload);
            
            if ($success) {
                $updatedTask = $this->engine->getById((int)$id);
                $filteredResult = $this->filterResponseColumns($updatedTask);
                return json_encode($this->engine->buildSuccessResponse($filteredResult));
            } else {
                return json_encode($this->engine->buildErrorResponse('Failed to update task'));
            }
        } catch (\Exception $e) {
            return json_encode($this->engine->buildErrorResponse($e->getMessage()));
        }
    }

    /**
     * HTTP DELETE handler
     *
     * @param $id
     * @param $payload
     * @return false|string
     */
    public function httpDel($id, $payload)
    {
        // Check if ID exists
        if (empty($id) || !is_numeric($id)) {
            return json_encode($this->engine->buildErrorResponse('Invalid parameters'));
        }

        try {
            // Check if task exists
            $existingTask = $this->engine->getById((int)$id);
            
            if (!$existingTask) {
                return json_encode($this->engine->buildErrorResponse('Task not found'));
            }

            // Delete task using Engine
            $success = $this->engine->delete((int)$id);
            
            if ($success) {
                return json_encode($this->engine->buildSuccessResponse([]));
            } else {
                return json_encode($this->engine->buildErrorResponse('Failed to delete task'));
            }
        } catch (\Exception $e) {
            return json_encode($this->engine->buildErrorResponse($e->getMessage()));
        }
    }

    /**
     * Filter response columns
     *
     * @param array $data
     * @return array
     */
    private function filterResponseColumns($data): array
    {
        if (empty($data)) {
            return [];
        }
        
        // Check if data is a multi-dimensional array
        if (isset($data[0])) {
            // Multi-dimensional array
            $filtered = [];
            foreach ($data as $item) {
                $filtered[] = array_intersect_key($item, array_flip($this->response_column));
            }
            return $filtered;
        } else {
            // Single-dimensional array
            return array_intersect_key($data, array_flip($this->response_column));
        }
    }

    /**
     * HTTP File Upload handler (stub - not implemented for tasks)
     *
     * @param int $identity
     * @param array $payload
     * @return false|string
     */
    public function httpFileUpload(int $identity, array $payload)
    {
        return json_encode($this->engine->buildErrorResponse('File upload not supported for tasks'));
    }

    /**
     * HTTP File Download handler (stub - not implemented for tasks)
     *
     * @param int $identity
     * @param array $payload
     * @return false|string
     */
    public function httpFileDownload(int $identity, array $payload)
    {
        return json_encode($this->engine->buildErrorResponse('File download not supported for tasks'));
    }
}