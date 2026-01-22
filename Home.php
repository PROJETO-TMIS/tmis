
<?php include('includes/head.php'); ?>

<?php include('includes/header.php'); ?>

<link rel="stylesheet" href="style/home.css">

<main class="main-content">
  
  <!-- SEÇÃO DE CARDS INFORMATIVOS -->
  <section class="info-cards" aria-label="Resumo de informações">
    <article class="card tarefas">
      <div class="card-icon" aria-hidden="true">
        <i class="fas fa-clipboard-list"></i>
      </div>
      <div class="card-content">
        <span>Tarefas</span>
        <strong id="qtd-tarefas">0</strong>
      </div>
    </article>

    <article class="card alertas">
      <div class="card-icon" aria-hidden="true">
        <i class="fas fa-bell"></i>
      </div>
      <div class="card-content">
        <span>Notificações</span>
        <strong>10</strong>
      </div>
    </article>

    <article class="card movimentacoes">
      <div class="card-icon" aria-hidden="true">
        <i class="fas fa-scale-balanced"></i>
      </div>
      <div class="card-content">
        <span>Publicações</span>
        <strong>10</strong>
      </div>
    </article>
  </section>

  <!-- SEÇÃO DE GERENCIAMENTO DE TAREFAS -->
  <section class="task-section">
    <h1 class="sr-only">Gerenciamento de Tarefas</h1>
    
    <!-- FILTROS DE TAREFAS -->
    <div class="task-filters" role="toolbar" aria-label="Filtros de tarefas">
      <button class="filter-btn active" data-filter="todo" aria-pressed="true">
        Todo período
      </button>
      <button class="filter-btn" data-filter="hoje" aria-pressed="false">
        Hoje
      </button>
      <button class="filter-btn" data-filter="atrasadas" aria-pressed="false">
        Atrasadas
      </button>
      <button class="filter-btn" data-filter="personalizado" aria-pressed="false">
        Personalizado
      </button>
      <button class="add-btn" title="Adicionar nova tarefa" aria-label="Adicionar nova tarefa">
        <i class="fas fa-plus" aria-hidden="true"></i>
      </button>
    </div>

    <!-- FILTRO PERSONALIZADO -->
    <div id="filtro-personalizado" class="filtro-personalizado hidden" aria-hidden="true">
      <label for="data-inicio">
        De: 
        <input type="date" id="data-inicio" name="data-inicio">
      </label>
      <label for="data-fim">
        Até: 
        <input type="date" id="data-fim" name="data-fim">
      </label>
      <button type="button" id="aplicar-filtro">Buscar</button>
    </div>

    <!-- TABELA DE TAREFAS -->
    <div class="task-table-container">
      <table class="task-table">
        <caption class="sr-only">Lista de tarefas</caption>
        <thead>
          <tr>
            <th scope="col">Tarefa</th>
            <th scope="col">Data Limite</th>
            <th scope="col">Prioridade</th>
            <th scope="col">Cliente</th>
            <th scope="col">Observação</th>
            <th scope="col">Responsáveis</th>
            <th scope="col">Ação</th>
          </tr>
        </thead>
        <tbody id="task-tbody">
          <!-- As tarefas serão inseridas aqui dinamicamente -->
        </tbody>
      </table>
    </div>
  </section>

  <!-- MODAL DE ADICIONAR/EDITAR TAREFA -->
  <div id="modal" class="modal hidden" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-content">
      <button class="close-modal" aria-label="Fechar modal">&times;</button>
      <h2 id="modal-title">Adicionar tarefa</h2>
      <form id="task-form">
        <div class="form-group">
          <label for="descricao">Descrição da tarefa*</label>
          <input 
            type="text" 
            id="descricao" 
            name="descricao"
            placeholder="Ex: Ligar para o cliente" 
            required 
            aria-required="true"
          >
        </div>

        <div class="form-group">
          <label for="data">Data*</label>
          <input 
            type="date" 
            id="data" 
            name="data"
            required 
            aria-required="true"
          >
        </div>

        <div class="form-group">
          <label for="prioridade">Prioridade*</label>
          <select id="prioridade" name="prioridade" required aria-required="true">
            <option value="urgente">Alta</option>
            <option value="media">Média</option>
            <option value="baixa">Baixa</option>
          </select>
        </div>

        <div class="form-group">
          <label for="cliente">Cliente</label>
          <input 
            type="text" 
            id="cliente" 
            name="cliente"
            placeholder="Nome do cliente ou empresa"
          >
        </div>

        <div class="form-group">
          <label for="obs">Observação</label>
          <input 
            type="text" 
            id="obs" 
            name="obs"
            placeholder="Ex: contrato anexo, ligar antes das 14h..."
          >
        </div>

        <div class="form-group">
          <label for="responsaveis">Responsáveis</label>
          <input 
            type="text" 
            id="responsaveis" 
            name="responsaveis"
            placeholder="Digite os nomes separados por vírgula"
          >
        </div>

        <div class="modal-actions">
          <button type="button" id="cancelar">Cancelar</button>
          <button type="submit">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
  <div id="confirmar-exclusao" class="modal hidden" role="alertdialog" aria-labelledby="confirmar-exclusao-titulo" aria-hidden="true">
    <div class="modal-content">
      <h2 id="confirmar-exclusao-titulo">Confirmar exclusão</h2>
      <p>Tem certeza que deseja excluir esta tarefa?</p>
      <div class="modal-actions">
        <button type="button" id="cancelar-exclusao">Cancelar</button>
        <button type="button" id="confirmar-excluir">Excluir</button>
      </div>
    </div>
  </div>

</main>

<!-- SCRIPTS -->
<script src="filtro.js"></script>

<?php include('includes/footer.php'); ?>