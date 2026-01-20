<?php
header('Content-Type: application/json');

// ===== CONFIG =====
$PASTA_MODELOS = __DIR__ . '/templates/';

// ===== 1️⃣ Verifica pasta =====
if (!is_dir($PASTA_MODELOS)) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Pasta templates não encontrada.'
    ]);
    exit;
}

// ===== 2️⃣ Verifica POST =====
$arquivo = $_POST['arquivo'] ?? '';
$arquivo = trim($arquivo);

if ($arquivo === '') {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Nome do arquivo não informado.'
    ]);
    exit;
}

// ===== 3️⃣ Segurança =====
$arquivo = basename($arquivo);

// ===== 4️⃣ Caminho final =====
$caminho = $PASTA_MODELOS . $arquivo;

// ===== 5️⃣ Verifica existência =====
if (!file_exists($caminho)) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Arquivo não encontrado.'
    ]);
    exit;
}

// ===== 6️⃣ Exclui =====
if (!unlink($caminho)) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Não foi possível excluir o arquivo.'
    ]);
    exit;
}

// ===== 7️⃣ Sucesso =====
echo json_encode([
    'sucesso' => true
]);
