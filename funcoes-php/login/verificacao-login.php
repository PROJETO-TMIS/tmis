<?php 

session_start();
require_once('../conexao.php');

if(isset($_SESSION['id'])){
    echo 'entrou pela session';
} elseif(isset($_COOKIE['remember_me'])) {
    echo 'entrou pelo cooking';
} else {
    echo 'não entrou';
}

?>