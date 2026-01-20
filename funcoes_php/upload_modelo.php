<?php
// upload_modelo.php
header('Content-Type: application/json');

// ===== CONFIG =====
$PASTA_MODELOS = __DIR__ . '/templates/';

// ===== 1️⃣ Verifica se a pasta existe =====
if (!is_dir($PASTA_MODELOS)) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'A pasta "templates" não existe.'
    ]);
    exit;
}

// ===== 2️⃣ Verifica upload =====
if (
    !isset($_FILES['arquivo']) ||
    $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK
) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Arquivo não enviado corretamente.'
    ]);
    exit;
}

// ===== 3️⃣ Nome base informado pelo usuário =====
$nomeBase  = trim($_POST['nome_modelo'] ?? '');
$confirmar = $_POST['confirmar'] ?? '0';

if ($nomeBase === '') {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Nome do modelo é obrigatório.'
    ]);
    exit;
}

// ===== 4️⃣ Normaliza nome do arquivo =====
$nomeBase = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $nomeBase);
$nomeBase = trim($nomeBase);

$nomeArquivo  = $nomeBase . '.docx';
$caminhoFinal = $PASTA_MODELOS . $nomeArquivo;

// ===== 5️⃣ Se já existe e ainda não confirmou =====
if (file_exists($caminhoFinal) && $confirmar !== '1') {
    echo json_encode([
        'confirmacao' => true,
        'mensagem' => 'Já existe um arquivo com esse nome. Deseja prosseguir?'
    ]);
    exit;
}

// ===== 6️⃣ Se confirmou, gera (1), (2), (3)... =====
$contador = 1;
while (file_exists($caminhoFinal)) {
    $nomeArquivo  = $nomeBase . " ($contador).docx";
    $caminhoFinal = $PASTA_MODELOS . $nomeArquivo;
    $contador++;
}

// ===== 7️⃣ Move o arquivo =====
if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoFinal)) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Falha ao mover o arquivo para a pasta templates.'
    ]);
    exit;
}

// ===== 8️⃣ Sucesso =====
echo json_encode([
    'sucesso' => true,
    'arquivo' => $nomeArquivo
]);
