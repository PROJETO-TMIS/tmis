<?php
require_once 'db.php';

$result = $conn->query("SELECT * FROM clientes");

$clientes = [];

while ($row = $result->fetch_assoc()) {
    $clientes[] = $row;
}

echo "<pre>";
print_r($clientes);
