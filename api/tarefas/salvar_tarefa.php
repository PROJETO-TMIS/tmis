<?php
include "../db.php";

$dados = json_decode(file_get_contents("php://input"), true);

$dataBanco = DateTime::createFromFormat('d/m/Y', $dados['data'])
  ->format('Y-m-d');

$sql = $conn->prepare("
  INSERT INTO tarefas
  (descricao, data_tarefa, prioridade, cliente_id, observacao, responsaveis)
  VALUES (?, ?, ?, NULL, ?, ?)
");

$sql->bind_param(
  "sssss",
  $dados['descricao'],
  $dataBanco,
  $dados['prioridade'],
  $dados['obs'],
  $dados['responsaveis']
);

$sql->execute();

echo json_encode(["sucesso" => true]);