<?php
include "../db.php";

$id = $_GET['id'] ?? null;

if ($id) {
  $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
}

echo json_encode(["success" => true]);
