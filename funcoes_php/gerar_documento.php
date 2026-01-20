<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Acesso inválido');
}

/* =========================
   FUNÇÕES AUXILIARES
========================= */
function v($arr, $key) {
    return isset($arr[$key]) ? trim((string)$arr[$key]) : '';
}

function dataExtenso() {
    $meses = [
        1 => 'janeiro', 2 => 'fevereiro', 3 => 'março',
        4 => 'abril', 5 => 'maio', 6 => 'junho',
        7 => 'julho', 8 => 'agosto', 9 => 'setembro',
        10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
    ];
    $d = (int) date('d');
    $m = (int) date('m');
    $y = date('Y');
    return $d . ' de ' . ($meses[$m] ?? '') . ' de ' . $y;
}

function safeSet(TemplateProcessor $t, $key, $value) {
    // PhpWord exige strings; evitar nulls
    $t->setValue((string)$key, (string)$value);
}

/* =========================
   MODELO
========================= */
$modelo = trim($_POST['modelo'] ?? '');
if ($modelo === '') exit('Modelo não informado');
$modelo = basename($modelo);

/* =========================
   CLIENTES JSON
========================= */
$clientes = json_decode($_POST['clientes_json'] ?? '', true);
if (!is_array($clientes) || count($clientes) === 0) {
    exit('Nenhum cliente informado (clientes_json).');
}

/* =========================
   CAMINHOS
========================= */
$templatePath = __DIR__ . "/templates/$modelo";
$dirContratos = __DIR__ . '/contratos';

if (!file_exists($templatePath)) exit('Modelo não encontrado');
if (!is_dir($dirContratos)) {
    // tentar criar
    if (!mkdir($dirContratos, 0755, true)) exit('Pasta contratos não existe e não foi possível criar');
}
if (!is_writable($dirContratos)) exit('Pasta contratos sem permissão de escrita');

/* =========================
   TEMPLATE
========================= */
try {
    $template = new TemplateProcessor($templatePath);
} catch (Exception $e) {
    exit('Erro ao abrir o template: ' . $e->getMessage());
}

/* =========================
   DEFINIÇÕES GLOBAIS / DATA
========================= */
safeSet($template, 'DATA', date('d/m/Y'));
safeSet($template, 'DATA_EXTENSO', dataExtenso());

/* =========================
   FUNÇÃO PARA FORMATAR ENDEREÇO COMPLETO
========================= */
function enderecoFormatado($e) {
    $rua = v($e, 'rua');
    $numero = v($e, 'numero');
    $bairro = v($e, 'bairro');
    $cidade = v($e, 'cidade');
    $uf = v($e, 'uf');
    $cep = v($e, 'cep');
    $parts = [];
    if ($rua !== '') $parts[] = $rua . ($numero ? ', ' . $numero : '');
    if ($bairro !== '') $parts[] = $bairro;
    $cidadeuf = ($cidade ? $cidade : '') . ($uf ? '/' . $uf : '');
    if (trim($cidadeuf) !== '') $parts[] = $cidadeuf;
    $out = implode(' - ', $parts);
    if ($cep !== '') $out .= ($out ? ' - ' : '') . 'CEP ' . $cep;
    return $out;
}

/* =========================
   PREENCHER CLIENTES: SEM SUFIXO (PRINCIPAL) E NUMERADOS
========================= */
$maxClients = count($clientes); // suporta N clientes
$primary = $clientes[0];
$nomePrincipal = v($primary, 'nome');
if ($nomePrincipal === '') exit('Nome do cliente principal inválido');

/* preencher placeholders do cliente principal (sem sufixo) */
safeSet($template, 'NOME', $nomePrincipal);
safeSet($template, 'CPF_CNPJ', v($primary, 'cpf_cnpj'));
safeSet($template, 'RG', v($primary, 'rg'));
safeSet($template, 'ORGAO_EMISSOR', v($primary, 'orgao_emissor'));
safeSet($template, 'NACIONALIDADE', v($primary, 'nacionalidade'));
safeSet($template, 'ESTADO_CIVIL', v($primary, 'estado_civil'));
safeSet($template, 'PROFISSAO', v($primary, 'profissao'));
safeSet($template, 'NOME_MAE', v($primary, 'nome_mae'));
safeSet($template, 'NOME_PAI', v($primary, 'nome_pai'));

// contato
safeSet($template, 'TELEFONE', v($primary['contato'] ?? [], 'telefone'));
safeSet($template, 'EMAIL', v($primary['contato'] ?? [], 'email'));

// endereco
$e1 = $primary['endereco'] ?? [];
safeSet($template, 'CEP', v($e1,'cep'));
safeSet($template, 'RUA', v($e1,'rua'));
safeSet($template, 'NUMERO', v($e1,'numero'));
safeSet($template, 'COMPLEMENTO', v($e1,'complemento'));
safeSet($template, 'BAIRRO', v($e1,'bairro'));
safeSet($template, 'CIDADE', v($e1,'cidade'));
safeSet($template, 'UF', v($e1,'uf'));
safeSet($template, 'ENDERECO', enderecoFormatado($e1));

// campos adicionais / complementares (permanecem vazios se não enviados)
safeSet($template, 'NUMERO_PROCESSO', $_POST['NUMERO_PROCESSO'] ?? '');
safeSet($template, 'DOCUMENTO_COMPLEMENTAR', $_POST['DOCUMENTO_COMPLEMENTAR'] ?? '');
safeSet($template, 'DESCRICAO_SERVICOS', $_POST['DESCRICAO_SERVICOS'] ?? '');
// HONORÁRIOS — criar vários aliases para evitar problemas de nomes divergentes no Word
$hon = $_POST['HONORARIOS_ADVOCATICIOS'] ?? $_POST['HONORARIOS_ADVOGATICIOS'] ?? $_POST['HONORARIOS'] ?? $_POST['HONORARIOS_ADVOGADOS'] ?? ($_POST['HONORARIOS_ADVOCATICIOS'] ?? '');
safeSet($template, 'HONORARIOS_ADVOCATICIOS', $hon);
safeSet($template, 'HONORARIOS_ADVOGATICIOS', $hon);
safeSet($template, 'HONORARIOS', $hon);
safeSet($template, 'HONORARIOS_ADVOGADOS', $hon);

/* =========================
   CLIENTES NUMERADOS (1..N)
   -> cria placeholders NOME_1, CPF_CNPJ_1, ... NOME_2, CPF_CNPJ_2, ...
========================= */
foreach ($clientes as $idx => $c) {
    $n = $idx + 1; // 1-based
    $e = $c['endereco'] ?? [];

    // nomes (tanto NOME_n quanto RAZAO_SOCIAL_n)
    safeSet($template, "NOME_$n", v($c, 'nome'));
    safeSet($template, "RAZAO_SOCIAL_$n", v($c, 'razao_social'));
    safeSet($template, "NOME_FANTASIA_$n", v($c, 'nome_fantasia'));

    // documentos
    safeSet($template, "CPF_CNPJ_$n", v($c, 'cpf_cnpj')); // geral: CPF ou CNPJ
    safeSet($template, "CPF_$n", v($c, 'cpf')); // alias
    safeSet($template, "CNPJ_$n", v($c, 'cnpj')); // alias PJ
    safeSet($template, "RG_$n", v($c, 'rg'));
    safeSet($template, "ORGAO_EMISSOR_$n", v($c, 'orgao_emissor'));
    safeSet($template, "INSCRICAO_ESTADUAL_$n", v($c, 'inscricao_estadual'));

    // PF / PJ extras
    safeSet($template, "NACIONALIDADE_$n", v($c, 'nacionalidade'));
    safeSet($template, "ESTADO_CIVIL_$n", v($c, 'estado_civil'));
    safeSet($template, "PROFISSAO_$n", v($c, 'profissao'));
    safeSet($template, "NOME_MAE_$n", v($c, 'nome_mae'));
    safeSet($template, "NOME_PAI_$n", v($c, 'nome_pai'));

    // contato
    safeSet($template, "TELEFONE_$n", v($c['contato'] ?? [], 'telefone'));
    safeSet($template, "EMAIL_$n", v($c['contato'] ?? [], 'email'));

    // endereço e formato completo
    safeSet($template, "CEP_$n", v($e,'cep'));
    safeSet($template, "RUA_$n", v($e,'rua'));
    safeSet($template, "NUMERO_$n", v($e,'numero'));
    safeSet($template, "COMPLEMENTO_$n", v($e,'complemento'));
    safeSet($template, "BAIRRO_$n", v($e,'bairro'));
    safeSet($template, "CIDADE_$n", v($e,'cidade'));
    safeSet($template, "UF_$n", v($e,'uf'));
    safeSet($template, "ENDERECO_$n", enderecoFormatado($e));

    // complementares por cliente
    safeSet($template, "NUMERO_PROCESSO_$n", $_POST["NUMERO_PROCESSO_$n"] ?? '');
    safeSet($template, "DOCUMENTO_COMPLEMENTAR_$n", $_POST["DOCUMENTO_COMPLEMENTAR_$n"] ?? '');
    safeSet($template, "DESCRICAO_SERVICOS_$n", $_POST["DESCRICAO_SERVICOS_$n"] ?? '');
    // honorários por cliente (aliases)
    $hon_n = $_POST["HONORARIOS_ADVOCATICIOS_$n"] ?? $_POST["HONORARIOS_$n"] ?? '';
    safeSet($template, "HONORARIOS_ADVOCATICIOS_$n", $hon_n);
    safeSet($template, "HONORARIOS_$n", $hon_n);
}

/* =========================
   NOME DO ARQUIVO (sanitiza)
========================= */
$nomeModelo = preg_replace('/\.docx$/i', '', $modelo);
$nomeModelo = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $nomeModelo);
$nomeClienteSafe = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $nomePrincipal);
$nomeModelo = trim($nomeModelo);
$nomeClienteSafe = trim($nomeClienteSafe);

$arquivo = ($nomeClienteSafe !== '' ? $nomeClienteSafe . ' - ' : '') . $nomeModelo . '.docx';
$caminho = $dirContratos . '/' . $arquivo;

$i = 1;
while (file_exists($caminho)) {
    $arquivo = ($nomeClienteSafe !== '' ? $nomeClienteSafe . ' - ' : '') . $nomeModelo . " ($i).docx";
    $caminho = $dirContratos . '/' . $arquivo;
    $i++;
}

/* =========================
   SALVAR E DOWNLOAD
========================= */
try {
    $template->saveAs($caminho);
} catch (Exception $e) {
    exit('Erro ao salvar o documento: ' . $e->getMessage());
}

if (!file_exists($caminho)) {
    exit('Falha ao criar o arquivo final');
}

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . basename($arquivo) . '"');
header('Content-Length: ' . filesize($caminho));
readfile($caminho);
exit;
