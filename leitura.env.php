<?php 

$conteudo = file_get_contents(".env");
$dados = preg_split("/[=\n\r]+/", $conteudo);

$host   = trim($dados[1]);
$banco  = trim($dados[3]);
$usuario = trim($dados[5]);
$senha  = trim($dados[7]);


?>