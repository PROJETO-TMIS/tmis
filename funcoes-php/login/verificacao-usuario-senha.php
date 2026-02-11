<?php

session_start();

require_once(__DIR__ . '/../../config.php');
require_once(RAIZ_PROJETO . '/funcoes-php/conexao.php');

//$email = 'tiagohilarioterto@gmail.com';
//$senha = 'VY48TdYcDR*W';
$permanecer_logado = true;

$email = $_POST['email'];
$senha = $_POST['senha'];
//$permanecer_logado = $_POST['permanecer_logado'];
$json = [];



$sql = "SELECT id, senha, nome_usuario FROM usuarios WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':email', $email);

$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($dados)) {

    $senhaCp = $dados['senha'];

    if (password_verify($senha, $senhaCp)) {

        $_SESSION['id'] = $dados['id'];
        $_SESSION['nome_usuario'] = $dados['nome_usuario'];

        $impressao_digital = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']); 

        if($permanecer_logado){
            $token = bin2hex(random_bytes(32));

            setcookie('permanecer_logado', $token, time() + (60 * 60 * 24 * 30), "/");

            $sql = "UPDATE usuarios SET token = :token, impressao_digital = :impressao_digital WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':impressao_digital', $impressao_digital);
            $stmt->bindValue(':id', $_SESSION['id']);

            $stmt->execute();
        } else {

            $sql = "UPDATE usuarios SET impressao_digital = :impressao_digital WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':impressao_digital', $impressao_digital);
            $stmt->bindValue(':id', $_SESSION['id']);

            $stmt->execute();

        }

        $json['sucesso'] = true;
       
    } else {
        $json['sucesso'] = false;
        $json['mensagem'] = 'Senha incorreta';
    }
} else {
    $json['sucesso'] = false;
    $json['mensagem'] = 'E-mail não cadastro, tente redefinir a senha...';
}

echo json_encode($json);
?>