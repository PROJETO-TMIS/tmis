<?php
// tmis/listar_contratos.php
header('Content-Type: application/json');

$dir = __DIR__ . '/contratos';

if (!is_dir($dir)) {
    echo json_encode([]);
    exit;
}

$arquivos = scandir($dir);
$resultado = [];

foreach ($arquivos as $a) {
    if ($a === '.' || $a === '..') continue;
    if (!str_ends_with($a, '.docx')) continue;

    $resultado[] = [
        'arquivo' => $a,
        'nome' => preg_replace('/\.docx$/i', '', $a)
    ];
}

echo json_encode($resultado);
