<?php 

require_once(__DIR__ . '/../config.php');

$conteudo = file_get_contents(RAIZ_PROJETO . '/.env');
$dados = preg_split("/[=\n\r]+/", $conteudo);

$db_host   = trim($dados[1]);
$db_banco  = trim($dados[3]);
$db_usuario = trim($dados[5]);
$db_senha  = trim($dados[7]);

// ABRINDO BANCO DE DADOS
try {

    $pdo = new PDO("mysql:host=$db_host;dbname=$db_banco", $db_usuario, $db_senha);
    // Gerando um erro se der erro:
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao abrir o banco: " . $e->getMessage());
}
?>