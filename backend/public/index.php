<?php
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string
$uri = parse_url($uri, PHP_URL_PATH);

// API routes
if (strpos($uri, '/api/') === 0) {
    $apiPath = substr($uri, 5); // Remove '/api/'
    
    // Parse the module and action from the path
    // Format: /api/{module}/{action}/{id?}
    $pathParts = explode('/', trim($apiPath, '/'));
    
    if (empty($pathParts[0])) {
        http_response_code(404);
        echo json_encode(['error' => 'Module not specified']);
        exit;
    }
    
    $moduleName = ucfirst($pathParts[0]);
    $action = isset($pathParts[1]) ? $pathParts[1] : '';
    $id = isset($pathParts[2]) ? $pathParts[2] : null;
    
    // Check if module exists
    $moduleClass = "App\\Modules\\{$moduleName}\\API";
    $moduleFile = __DIR__ . "/../Modules/{$moduleName}/API.php";
    
    if (!file_exists($moduleFile)) {
        http_response_code(404);
        echo json_encode(['error' => "Module '{$moduleName}' not found"]);
        exit;
    }
    
    // Load module
    $module = new $moduleClass();
    
    // Handle request based on HTTP method
    switch ($method) {
        case 'GET':
            $payload = $_GET;
            if ($id) {
                $payload['id'] = $id;
            }
            echo $module->httpGet($payload);
            break;
            
        case 'POST':
            $payload = json_decode(file_get_contents('php://input'), true) ?? [];
            if ($action === 'setup' && $moduleName === 'Tasks') {
                // Special case for table setup
                echo $module->setupTable();
            } else {
                echo $module->httpPost($payload);
            }
            break;
            
        case 'PUT':
            $payload = json_decode(file_get_contents('php://input'), true) ?? [];
            if ($id) {
                echo $module->httpPut($id, $payload);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID required for PUT request']);
            }
            break;
            
        case 'DELETE':
            $payload = json_decode(file_get_contents('php://input'), true) ?? [];
            if ($id) {
                echo $module->httpDel($id, $payload);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID required for DELETE request']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
    exit;
} elseif ($uri === '/health' && $method === 'GET') {
    echo json_encode([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}