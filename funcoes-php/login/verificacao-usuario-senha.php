<?php

session_start();

require_once('../conexao.php');

$email = 'tiagohilarioterto@gmail.com';
$senha = 'VY48TdYcDR*W';
$permanecer_logado = true;



$sql = "SELECT id, senha, nome_usuario FROM usuarios WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':email', $email);

$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($dados)) {

    $senhaCp = $dados['senha'];

    if (password_verify($senha, $senhaCp)) {
        echo "Senha correta! Iniciando sessão...";

        $_SESSION['id'] = $dados['id'];
        $_SESSION['nome_usuario'] = $dados['nome_usuario'];

        $impressao_digital = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']); 

        if($permanecer_logado){
            $token = bin2hex(random_bytes(32));

            setcookie("remember_me", $token, time() + (86400 * 30), "/");

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
       
    } else {
        echo "Senha incorreta.";
    }
} else {
    echo "E-mail não cadastro";
}
?>