<?php

namespace App;

use PDO;

class Task {
  private $conn;
  private $table_name = "tasks";

  public $id;
  public $title;
  public $description;
  public $completed;
  public $created_at;
  public $updated_at;

  public function __construct($db) {
    $this->conn = $db;
  }

  public function getAll() {
    $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function getOne() {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $this->id);
    $stmt->execute();
    return $stmt;
  }

  public function create() {
    $query = "INSERT INTO " . $this->table_name . " (title, description, completed) VALUES (:title, :description, :completed)";
    $stmt = $this->conn->prepare($query);

    $this->title = htmlspecialchars(strip_tags($this->title));
    $this->description = htmlspecialchars(strip_tags($this->description));
    $this->completed = $this->completed ? 1 : 0;

    $stmt->bindParam(":title", $this->title);
    $stmt->bindParam(":description", $this->description);
    $stmt->bindParam(":completed", $this->completed);

    if($stmt->execute()) {
      $this->id = $this->conn->lastInsertId();
      return true;
    }
    return false;
  }

  public function update() {
    $query = "UPDATE " . $this->table_name . " SET title = :title, description = :description, completed = :completed WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    $this->title = htmlspecialchars(strip_tags($this->title));
    $this->description = htmlspecialchars(strip_tags($this->description));
    $this->completed = $this->completed ? 1 : 0;

    $stmt->bindParam(":title", $this->title);
    $stmt->bindParam(":description", $this->description);
    $stmt->bindParam(":completed", $this->completed);
    $stmt->bindParam(":id", $this->id);

    return $stmt->execute();
  }

  public function delete() {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $this->id);
    return $stmt->execute();
  }
}
