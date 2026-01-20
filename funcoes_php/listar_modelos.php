<?php
header('Content-Type: application/json');

$pasta = __DIR__ . '/templates';

if (!is_dir($pasta)) {
  echo json_encode([]);
  exit;
}

$arquivos = glob($pasta . '/*.docx');
$templates = [];

foreach ($arquivos as $arquivo) {
  $nomeArquivo = basename($arquivo);
  $nomeSemExt = pathinfo($nomeArquivo, PATHINFO_FILENAME);

  $templates[] = [
    'nome' => strtoupper(str_replace('_', ' ', $nomeSemExt)),
    'arquivo' => $nomeArquivo
  ];
}

echo json_encode($templates);
