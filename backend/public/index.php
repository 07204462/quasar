<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

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

if (preg_match('/^\/api\/tasks\/?(\d+)?$/', $uri, $matches)) {
  $task = new Task($db);

  switch($method) {
    case 'GET':
      if (isset($matches[1])) {
        $task->id = $matches[1];
        $stmt = $task->getOne();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
          echo json_encode($row);
        } else {
          http_response_code(404);
          echo json_encode(['error' => 'Task not found']);
        }
      } else {
        $stmt = $task->getAll();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tasks);
      }
      break;

    case 'POST':
      $data = json_decode(file_get_contents("php://input"));

      if (!empty($data->title)) {
        $task->title = $data->title;
        $task->description = $data->description ?? '';
        $task->completed = $data->completed ?? false;

        if ($task->create()) {
          http_response_code(201);
          echo json_encode([
            'message' => 'Task created',
            'id' => $task->id
          ]);
        } else {
          http_response_code(500);
          echo json_encode(['error' => 'Unable to create task']);
        }
      } else {
        http_response_code(400);
        echo json_encode(['error' => 'Title is required']);
      }
      break;

    case 'PUT':
      if (isset($matches[1])) {
        $data = json_decode(file_get_contents("php://input"));

        $task->id = $matches[1];
        $task->title = $data->title;
        $task->description = $data->description ?? '';
        $task->completed = $data->completed ?? false;

        if ($task->update()) {
          echo json_encode(['message' => 'Task updated']);
        } else {
          http_response_code(500);
          echo json_encode(['error' => 'Unable to update task']);
        }
      } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
      }
      break;

    case 'DELETE':
      if (isset($matches[1])) {
        $task->id = $matches[1];

        if ($task->delete()) {
          echo json_encode(['message' => 'Task deleted']);
        } else {
          http_response_code(500);
          echo json_encode(['error' => 'Unable to delete task']);
        }
      } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
      }
      break;

    default:
      http_response_code(405);
      echo json_encode(['error' => 'Method not allowed']);
  }
  exit;
}

http_response_code(404);
echo json_encode(['error' => 'Route not found']);
