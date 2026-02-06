<?php
    // Importar as classes necessárias
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($email_destinatario, $titulo, $corpo, $nome_envio='Contato Tmis'){


    // Carregar o autoloader do Composer
    require '../../vendor/autoload.php';

    $conteudo = file_get_contents("../../.env");
    $dados = preg_split("/[=\n\r]+/", $conteudo);

    $email_password  = trim($dados[11]);
    $email_user   = trim($dados[9]);
   
    $mail = new PHPMailer(true);
    try {
        // Configurações do Servidor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';    // Endereço do servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = $email_user;       // Teu e-mail
        $mail->Password   = $email_password;           // Tua senha ou senha de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Destinatários
        $mail->setFrom($email_user, $nome_envio);
        $mail->addAddress($email_destinatario);    // E-mail de quem recebe

        // Conteúdo do E-mail
        // Você dá um apelido (CID) para a imagem
        $mail->addEmbeddedImage('../../Imagens/01 - logo_le.png', 'logo_tmis');
        $mail->isHTML(true);
        $mail->Subject = $titulo;
        $mail->Body    = $corpo;

        $mail->send();
        return 'E-mail enviado com sucesso!';
    } catch (Exception $e) {
        return "Erro ao enviar e-mail: {$mail->ErrorInfo}";
    }
}
?>