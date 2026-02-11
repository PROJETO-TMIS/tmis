<?php 

session_start();
require_once('../tmis/funcoes-php/conexao.php');


if(isset($_SESSION['id'])){
    

} elseif(isset($_COOKIE['permanecer_logado'])) {
    echo 'entrou pelo cooking  ';

    
    $impressao_digital = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']); 
    $token = $_COOKIE['permanecer_logado'];

    $sql = "SELECT id, nome_usuario FROM usuarios WHERE token = :token AND impressao_digital = :impressao_digital";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->bindValue(':impressao_digital', $impressao_digital);

    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty($usuario)){
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome_usuario'] = $usuario['nome_usuario'];

        
    } else {
        //"Token não coincidiu com a impressão digital!";

        header('Location: ../tmis/login.php');

        exit();
    }
} else {
    header('Location: ../tmis/login.php');

    exit();
}

?>