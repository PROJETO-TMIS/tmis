<?php 

$caminhoEmpresa = '../../armazenamento/novo-cadastro/Cadastro - Nova Empresa - Empresa.csv';
$caminhoFuncionarios = '../../armazenamento/novo-cadastro/Cadastro - Nova Empresa - Funcionarios.csv';



if (($handle = fopen($caminhoEmpresa, "r")) !== FALSE) {
    
    $linhaAtual = 1;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    
    if ($linhaAtual == 3) {
        $empresa = array_slice($data, 0, 3);
        break;
    }
    
    $linhaAtual++;
    }
    
    fclose($handle);
}

if (($handle = fopen($caminhoFuncionarios, "r")) !== FALSE) {

    $linhaAtual = 1;
    $funcionarios = [];
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    
    if ($linhaAtual >= 3) {
        $linha = array_slice($data, 0, 3);
        array_push($funcionarios, $linha);
    }
    
    $linhaAtual++;
    }
    
    fclose($handle);
}

$conteudo = file_get_contents("../../.env");
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


// ADICIONANDO EMPRESAS
$sql = "SELECT id FROM empresas WHERE cnpj = :cnpj LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':cnpj', $empresa[2]); 
$stmt->execute();
$empresa_existe = $stmt->fetch();


if(!$empresa_existe) {
    $sql = "INSERT INTO empresas (nome_fantasia, razao_social, cnpj) VALUES (:nome, :razao, :cnpj);";
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':nome', $empresa[0]); 
    $stmt->bindValue(':razao', $empresa[1]);
    $stmt->bindValue(':cnpj', $empresa[2]);

    $stmt->execute();
    
    echo "Cadastro da Empresa Realizado com Sucesso! <br>";

} else {
    echo "Empresa já existe <br>";
}





// ADICIONANDO FUNCIONÁRIOS
foreach ($funcionarios as $funcionario) {
    
    // 1. Verificar se o funcionário já existe (usando CPF como exemplo de identificador único)
    $sql_busca = "SELECT id FROM funcionarios WHERE cpf = :cpf LIMIT 1";
    $stmt_busca = $pdo->prepare($sql_busca);
    $stmt_busca->bindValue(':cpf', $funcionario[2]); 
    $stmt_busca->execute();
    $funcionario_existe = $stmt_busca->fetch();

    if (!$funcionario_existe) {

        //Gerando Senha



        
        $sql_insere = "INSERT INTO funcionarios (nome_completo, e-mail, cpf, senha, codigo_pais, telefone, nivel_acesso) VALUES (:nome, :e-mail, :cpf, :senha, :codigo_pais, :telefone, :nivel_acesso)";
        $stmt_insere = $pdo->prepare($sql_insere);

        // 3. Vincular os valores da variável que está sendo varrida
        $stmt_insere->bindValue(':nome',       $funcionario[0]); 
        $stmt_insere->bindValue(':cargo',      $funcionario[1]);
        $stmt_insere->bindValue(':cpf',        $funcionario[2]);
        $stmt_insere->bindValue(':empresa_id', $funcionario[3]); // ID da empresa que ele pertence

        $stmt_insere->execute();
        
        echo "Funcionário {$funcionario[0]} cadastrado com sucesso! <br>";

    } else {
        echo "Funcionário com CPF {$funcionario[2]} já existe no sistema. <br>";
    }
}



?>