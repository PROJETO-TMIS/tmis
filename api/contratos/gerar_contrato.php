<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../db.php';

if (!isset($_GET['id'])) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'ID não informado'
    ]);
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT 
        id,
        tipo,
        nome,
        cpf_cnpj,
        rg_ie,
        telefone,
        email,
        endereco,
        observacoes,
        data_cadastro
    FROM clientes
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Cliente não encontrado'
    ]);
    exit;
}

$cliente = $result->fetch_assoc();

echo json_encode([
    'sucesso' => true,
    'cliente' => $cliente
]);
