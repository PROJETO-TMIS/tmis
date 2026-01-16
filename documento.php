<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Acesso inválido');
}

$modelo = $_POST['modelo'];

$templatePath = __DIR__ . "/templates/$modelo";

if (!file_exists($templatePath)) {
    exit('Modelo não encontrado');
}

$template = new TemplateProcessor($templatePath);

$template->setValue('NOME', $_POST['NOME'] ?? '');
$template->setValue('CPF_CNPJ', $_POST['CPF_CNPJ'] ?? '');
$template->setValue('EMAIL', $_POST['EMAIL'] ?? '');
$template->setValue('TELEFONE', $_POST['TELEFONE'] ?? '');
$template->setValue('ENDERECO', $_POST['ENDERECO'] ?? '');
$template->setValue('DATA', date('d/m/Y'));

$nomeArquivo = 'documento_' . time() . '.docx';
$saida = __DIR__ . "/contracts/$nomeArquivo";

$template->saveAs($saida);

// força download
header("Content-Disposition: attachment; filename=$nomeArquivo");
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
readfile($saida);
exit;
