document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const campoErro = document.getElementById('Erro');
    campoErro.innerText = "Verificando..."; 

    try {
        const response = await fetch('funcoes-php/login/verificacao-usuario-senha.php', {
            method: 'POST',
            body: new FormData(this)
        });

        // 1. Primeiro pegamos o texto bruto para evitar erros de conversão caso o PHP falhe
        const textoBruto = await response.text();
        
        // Exibe o que veio do PHP exatamente como ele enviou (bom para ver erros de PHP)
        console.log("Resposta bruta do servidor:", textoBruto);

        // 2. Agora tentamos transformar em JSON
        const dados = JSON.parse(textoBruto);

        // 3. MOSTRAR NA TELA: Transforma o objeto em uma string bonita
        // O JSON.stringify com esses parâmetros (null, 2) organiza o texto com quebras de linha
        campoErro.style.color = 'blue';
        campoErro.innerHTML = `<pre>${JSON.stringify(dados, null, 2)}</pre>`;

        // Lógica de decisão
        if (dados.sucesso) {
            setTimeout(() => window.location.href = 'home.php', 500); // Aumentei o tempo para você conseguir ler
        } else {
            // Se quiser apenas a mensagem após o debug, use: campoErro.innerText = dados.mensagem;
        }

    } catch (error) {
        campoErro.style.color = 'red';
        campoErro.innerText = "Erro na requisição ou JSON inválido. Verifique o Console (F12).";
        console.error("Erro completo:", error);
    }
});