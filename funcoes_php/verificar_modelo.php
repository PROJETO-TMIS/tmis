<?php
$nome = $_GET['nome'] ?? '';
$nome = trim($nome);

$dir = __DIR__ . '/templates/';
$arquivo = $dir . $nome . '.docx';

echo json_encode([
  'existe' => file_exists($arquivo)
]);
