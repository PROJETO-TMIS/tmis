<?php 



// ========== COLETANDO DADOS ==========

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




// ========== VERIFICANDO DADOS ==========

require_once 'verificacao-dados.php';

// Verificação Empresa
$config = [
    0 => ['chave' => 'nome_fantasia', 'funcao' => 'nome'],
    1 => ['chave' => 'razao_social',  'funcao' => 'nome'],
    2 => ['chave' => 'cnpj',          'funcao' => 'cnpj']
];

$empresaDicionario = [];
$erros = [];

foreach ($empresa as $index => $valor) {
    if (isset($config[$index])) {
        $chave  = $config[$index]['chave'];
        $funcao = $config[$index]['funcao'];

        // Executa a validação do seu arquivo verificacao-dados.php
        $resultado = $funcao($valor);

        // Salva no dicionário
        $empresaDicionario[$chave] = $resultado['valor'];

        if ($resultado['erro']) {
            $erros[] = "Campo: $chave - Valor: $valor";
        }
    }
}

// Imprimindo Mensagens de Erro
if (!empty($erros)) {
    echo "<h2>⚠️ Erros de Validação Encontrados:</h2>";
    echo "<ul>";
    foreach ($erros as $msg) {
        echo "<li>$msg</li>";
    }
    echo "</ul>";
    
    exit("A operação foi cancelada. Corrija os erros acima para continuar.");
}



//Verificação Funcionário
$config = [
    0 => ['chave' => 'nome_completo', 'funcao' => 'nome'],
    1 => ['chave' => 'email',         'funcao' => 'email'],
    2 => ['chave' => 'cpf',           'funcao' => 'cpf'],
    3 => ['chave' => 'codigo_pais',    'funcao' => 'ddi'],
    4 => ['chave' => 'telefone',      'funcao' => 'telefone'],
    5 => ['chave' => 'nivel_acesso',      'funcao' => 'niveisAcesso'] 
];

$funcionarioDicionario = [];
$funcionariosDicionario = [];
$erros = [];

// 3. Processamento
foreach ($funcionarios as $i_f => $funcionario){
    foreach ($funcionario as $index => $valor) {
        if (isset($config[$index])) {
            $chave  = $config[$index]['chave'];
            $funcao = $config[$index]['funcao'];

            // Se for a função de nível de acesso, ela precisa do $pdo (conexão banco)
            if ($funcao == 'niveisAcesso') {
                $resultado = $funcao($pdo, $valor); 
            } else {
                $resultado = $funcao($valor);
            }

            $funcionarioDicionario[$chave] = $resultado['valor'];

            if ($resultado['erro']) {
                $erros[] = "Id: $i_f - Campo: $chave - Valor: $valor";
            }
        }
    }
    $funcionariosDicionario[] = $funcionarioDicionario;
}



// 4. Bloqueio com Exit
if (!empty($erros)) {
    echo "<h2>⚠️ Erros de Validação (Funcionários):</h2><ul>";
    foreach ($erros as $msg) { echo "<li>$msg</li>"; }
    echo "</ul>";
    exit("A operação de cadastro de funcionário foi interrompida.");
}






// ========== ESCREVENDO DADOS ==========

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
$sql = "SELECT id FROM empresas WHERE cnpj = :cnpj AND status = 'A' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':cnpj', $empresaDicionario['cnpj']); 
$stmt->execute();
$empresa_existe = $stmt->fetch();



if(!$empresa_existe) {
    $sql = "INSERT INTO empresas (nome_fantasia, razao_social, cnpj) VALUES (:nome_fantasia, :razao_social, :cnpj);";
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':nome_fantasia', $empresaDicionario['nome_fantasia']); 
    $stmt->bindValue(':razao_social', $empresaDicionario['razao_social']);
    $stmt->bindValue(':cnpj', $empresaDicionario['cnpj']);

    $stmt->execute();

    $id_empresa = $pdo->lastInsertId();
    
    echo "
        <div style='padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; font-family: sans-serif; margin: 10px 0;'>
            <strong>✅ Sucesso!</strong> O cadastro da empresa <strong>{$empresaDicionario['nome_fantasia']}</strong> foi realizado com sucesso.
        </div>
        ";

} else {
    echo "
        <div style='padding: 15px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px; font-family: Arial, sans-serif; margin: 10px 0;'>
            <strong>⚠️ Atenção:</strong> Esta empresa já possui cadastro em nossa base de dados.
        </div>
        ";

    $id_empresa = $empresa_existe['id'];

}





// ADICIONANDO FUNCIONÁRIOS
foreach ($funcionariosDicionario as $funcionarioDicionario) {
    
    // 1. Verificar se o funcionário já existe (usando CPF como exemplo de identificador único)
    $sql_busca = "SELECT id FROM usuarios WHERE email = :email AND status = 'A' LIMIT 1";
    $stmt_busca = $pdo->prepare($sql_busca);
    $stmt_busca->bindValue(':email', $funcionarioDicionario['email']); 
    $stmt_busca->execute();
    $funcionario_existe = $stmt_busca->fetch();

    if (!$funcionario_existe) {

        function senhaAleatoria($tamanho = 15) {
            // Definimos os caracteres que podem compor a senha
            $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
            $senha = '';
            $max = strlen($caracteres) - 1;

            for ($i = 0; $i < $tamanho; $i++) {
                // Seleciona um índice aleatório da string de caracteres
                $senha .= $caracteres[random_int(0, $max)];
            }

            return $senha;
        }

        //Gerando Senha

        $senha = senhaAleatoria(12);
        $senhaCp = password_hash($senha, PASSWORD_BCRYPT);


        
        $sql_insere = "INSERT INTO funcionarios (id_empresa, nome_completo, email, cpf, senha, codigo_pais, telefone, nivel_acesso) VALUES (:id_empresa, :nome_completo, :email, :cpf, :senha, :codigo_pais, :telefone, :nivel_acesso)";
        $stmt_insere = $pdo->prepare($sql_insere);

        // 3. Vincular os valores da variável que está sendo varrida
        $stmt_insere->bindValue(':id_empresa',    $id_empresa); 
        $stmt_insere->bindValue(':nome_completo', $funcionarioDicionario['nome_completo']); 
        $stmt_insere->bindValue(':email',         $funcionarioDicionario['email']);
        $stmt_insere->bindValue(':cpf',           $funcionarioDicionario['cpf']);
        $stmt_insere->bindValue(':senha',         $senhaCp);
        $stmt_insere->bindValue(':codigo_pais',   $funcionarioDicionario['codigo_pais']); 
        $stmt_insere->bindValue(':telefone',      $funcionarioDicionario['telefone']); 
        $stmt_insere->bindValue(':nivel_acesso',  $funcionarioDicionario['nivel_acesso']); 
        

        $stmt_insere->execute();
        
        echo "
            <div style='padding: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; font-family: sans-serif; margin: 15px 0; line-height: 1.6;'>
                <div style='font-size: 1.2em; margin-bottom: 10px;'>
                    <strong>✅ Cadastro Realizado com Sucesso!</strong>
                </div>
                <hr style='border: 0; border-top: 1px solid #c3e6cb; margin: 10px 0;'>
                <p style='margin: 5px 0;'>
                    <strong>Empresa:</strong> {$empresaDicionario['nome_fantasia']}
                </p>
                <p style='margin: 5px 0;'>
                    <strong>Funcionário:</strong> {$funcionario[0]}
                </p>
            </div>
            ";

    } else {
        echo "
            <div style='padding: 15px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px; font-family: sans-serif; margin: 10px 0;'>
                <strong>⚠️ Atenção!</strong> O funcionário com CPF <strong>{$funcionario[2]}</strong> já está cadastrado no sistema.
            </div>
            ";
    }
}



?>