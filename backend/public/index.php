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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}

require_once '../config/database.php';
require_once '../src/Task.php';

use App\Task;

$database = new Database();
$db = $database->getConnection();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/api/health' && $method === 'GET') {
  echo json_encode([
    'status' => 'ok',
    'message' => 'API is running'
  ]);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}

http_response_code(404);
echo json_encode(['error' => 'Route not found']);
