<?php
include "../db.php";

$result = $conn->query("SELECT * FROM clientes ORDER BY id DESC");

$clientes = [];

while ($row = $result->fetch_assoc()) {
  $clientes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($clientes);
