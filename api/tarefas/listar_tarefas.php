<?php
require_once '../db.php';

$sql = "
    SELECT 
        id,
        descricao,
        DATE_FORMAT(data_tarefa, '%d/%m/%Y') AS data,
        prioridade,
        cliente_id,
        observacao AS obs,
        responsaveis,
        status
    FROM tarefas
    ORDER BY data_tarefa ASC
";

$result = $conn->query($sql);

$tarefas = [];

while ($row = $result->fetch_assoc()) {
    // converte status do banco para booleano usado no JS
    $row['concluida'] = ($row['status'] === 'Concluida');
    $tarefas[] = $row;
}

header('Content-Type: application/json');
echo json_encode($tarefas);