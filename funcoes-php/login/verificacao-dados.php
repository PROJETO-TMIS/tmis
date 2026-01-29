<?php 
function nome($nome) {
    $nomeLimpo = preg_replace('/\s+/', ' ', trim($nome));
    $nomeMinusculo = mb_strtolower($nomeLimpo, 'UTF-8');
    $nomeTratado = mb_strtoupper(mb_substr($nomeMinusculo, 0, 1)) . mb_substr($nomeMinusculo, 1);

    $partes = explode(' ', $nomeTratado);
    $isValid = count($partes) >= 2 && !empty($partes[1]);

    if($isValid == null){
        $nomeTratado = '';
    }

    return [
        'valor' => $nomeTratado,
        'erro' => $isValid ? false : true
    ];
}

function email($email) {
    $emailLimpo = trim(mb_strtolower($email, 'UTF-8'));
    
    $isValid = filter_var($emailLimpo, FILTER_VALIDATE_EMAIL);

    if (!$isValid) {
        $emailLimpo = '';
    }

    return [
        'valor' => $emailLimpo,
        'erro' => $isValid ? false : true
    ];
}

function cpf($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    $isValid = true;

    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        $isValid = false;
    } else {
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $isValid = false;
                break;
            }
        }
    }

    if (!$isValid) {
        $cpf = '';
    }

    return [
        'valor' => $cpf,
        'erro' => $isValid ? false : true
    ];
}


function ddi($valor) {
    // Remove espaços e garante que não seja nulo
    $ddi = trim($valor);

    // Remove qualquer coisa que não seja número ou o sinal de +
    $ddi = preg_replace('/[^0-9+]/', '', $ddi);

    // Validação: Um DDI real tem entre 1 e 4 dígitos (ex: +1, +55, +1234)
    // Vamos verificar se após a limpeza restou algo válido
    $apenasNumeros = preg_replace('/[^0-9]/', '', $ddi);
    $isValid = (strlen($apenasNumeros) >= 1 && strlen($apenasNumeros) <= 4);

    return [
        'valor' => $isValid ? $ddi : "55",
        'erro' => $isValid ? false : true
    ];
}




function telefone($telefone) {
    $telefoneLimpo = preg_replace('/[^0-9]/', '', $telefone);
    
    $tamanho = strlen($telefoneLimpo);
    $isValid = ($tamanho == 11);

    if (!$isValid) {
        $telefoneLimpo = '';
    }

    return [
        'valor' => $telefoneLimpo,
        'erro' => $isValid ? false : true
    ];
}




function cnpj($cnpj) {
    $cnpj = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $cnpj));

    $isValid = true;

    if (strlen($cnpj) != 14 || preg_match('/(\h)\1{13}/', $cnpj)) {
        $isValid = false;
    } else {
        for ($t = 12; $t < 14; $t++) {
            $d = 0;
            $p = ($t - 7);
            for ($c = 0; $c < $t; $c++) {
                $valorCaractere = is_numeric($cnpj[$c]) ? (int)$cnpj[$c] : ord($cnpj[$c]) - 48;
                $d += $valorCaractere * $p;
                $p = ($p == 2) ? 9 : --$p;
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cnpj[$c] != $d) {
                $isValid = false;
                break;
            }
        }
    }

    if (!$isValid) {
        $cnpj = '';
    }

    return [
        'valor' => $cnpj,
        'erro' => $isValid ? false : true
    ];
}

function niveisAcesso($pdo, $id_nivel) {
    
    $id = (int) $id_nivel;

    
    $stmt = $pdo->prepare("SELECT id FROM niveis_acesso WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $existe = $stmt->fetch();

    $isValid = $existe ? true : false;

    if (!$isValid) {
        $id = 0;
    }

    return [
        'valor' => $id,
        'erro' => $isValid ? false : true
    ];
}


?>