<?php
include "../db.php";

$dados = json_decode(file_get_contents("php://input"), true);

$tipo = $dados['tipo'] ?? null;
$nome = $dados['nome'];
$cpf_cnpj = $dados['cpfCnpj'] ?? null;
$rg_ie = $dados['rgIe'] ?? null;
$telefone = $dados['telefone'] ?? null;
$email = $dados['email'] ?? null;
$endereco = $dados['endereco'] ?? null;
$observacoes = $dados['observacoes'] ?? null;

$sql = "INSERT INTO clientes 
(tipo, nome, cpf_cnpj, rg_ie, telefone, email, endereco, observacoes)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
  "ssssssss",
  $tipo,
  $nome,
  $cpf_cnpj,
  $rg_ie,
  $telefone,
  $email,
  $endereco,
  $observacoes
);

$stmt->execute();

echo json_encode(["success" => true]);
