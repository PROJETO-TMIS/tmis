<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$arquivo = basename($data['arquivo'] ?? '');
$caminho = __DIR__ . '/contratos/' . $arquivo;

if (!$arquivo || !file_exists($caminho)) {
    echo json_encode(['erro' => 'Arquivo não encontrado']);
    exit;
}

if (unlink($caminho)) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['erro' => 'Não foi possível excluir']);
}
