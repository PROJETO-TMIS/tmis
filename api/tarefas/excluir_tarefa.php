<?php
include "../db.php";

$id = $_GET['id'];

$conn->query("DELETE FROM tarefas WHERE id = $id");

echo json_encode(["sucesso" => true]);
