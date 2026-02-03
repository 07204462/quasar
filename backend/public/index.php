<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

$uri = $_SERVER['REQUEST_URI'];
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
