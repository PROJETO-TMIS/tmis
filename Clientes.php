<?php include('includes/head.php'); ?>

<?php include('includes/header.php'); ?>

<main class="main-content clientes-section">

  <!-- CABEÇALHO DA PÁGINA -->
  <header class="clientes-header">
    <h1><i class="fas fa-user-group" aria-hidden="true"></i> Clientes</h1>
    <button type="button" class="add-cliente-btn" onclick="Clientes.abrirModal()" aria-label="Adicionar novo cliente">
      Adicionar
    </button>
  </header>

  <!-- SEÇÃO DE FILTROS -->
  <section class="clientes-filtros" aria-label="Filtros de clientes">
    <label for="pesquisa-clientes" class="sr-only">Pesquisar clientes</label>
    <input
      type="search"
      id="pesquisa-clientes"
      name="pesquisa-clientes"
      placeholder="Pesquisar por nome, telefone ou email"
      aria-label="Pesquisar por nome, telefone ou email"
    >

    <label for="ordenacao-clientes" class="sr-only">Ordenar clientes</label>
    <select id="ordenacao-clientes" name="ordenacao-clientes" aria-label="Ordenar clientes por">
      <option value="recentes">Mais recentes</option>
      <option value="antigos">Mais antigos</option>
      <option value="az">Nome (A–Z)</option>
      <option value="za">Nome (Z–A)</option>
    </select>
  </section>

  <!-- TABELA DE CLIENTES -->
  <section class="clientes-table-section">
    <div class="table-wrapper">
      <table class="clientes-table">
        <caption class="sr-only">Lista de clientes cadastrados</caption>
        <thead>
          <tr>
            <th scope="col">Tipo</th>
            <th scope="col">Nome</th>
            <th scope="col">Telefone</th>
            <th scope="col">Email</th>
            <th scope="col">Ações</th>
          </tr>
        </thead>
        <tbody id="clientes-tbody">
          <!-- Os clientes serão inseridos aqui dinamicamente -->
        </tbody>
      </table>
    </div>

    <!-- MENSAGEM QUANDO NÃO HÁ CLIENTES -->
    <div id="mensagem-vazia" class="mensagem-vazia" style="display: none;" role="status" aria-live="polite">
      <i class="fas fa-user-group" aria-hidden="true"></i>
      <p>Nenhum cliente cadastrado ainda.</p>
    </div>
  </section>

</main>

<!-- MODAL DE ADICIONAR/EDITAR CLIENTE -->
<div id="clientesModal" class="clientes-modal" role="dialog" aria-labelledby="modal-titulo" aria-hidden="true">
  <div class="clientes-modal-content">

    <!-- CABEÇALHO DO MODAL -->
    <header class="clientes-modal-header">
      <h2 id="modal-titulo">Adicionar Cliente</h2>
      <button 
        type="button" 
        class="clientes-close" 
        onclick="Clientes.fecharModal()" 
        aria-label="Fechar modal"
      >
        &times;
      </button>
    </header>

    <!-- FORMULÁRIO -->
    <form id="form-cliente" onsubmit="Clientes.salvarCliente(event)">

      <div class="form-group">
        <label for="tipo-cliente">Tipo de Cliente*</label>
        <select id="tipo-cliente" name="tipo-cliente" required aria-required="true">
          <option value="fisica">Pessoa Física</option>
          <option value="juridica">Pessoa Jurídica</option>
        </select>
      </div>

      <div class="form-group">
        <label for="nome-cliente">Nome*</label>
        <input 
          type="text" 
          id="nome-cliente" 
          name="nome-cliente"
          required 
          aria-required="true"
        >
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="cpf-cnpj">CPF / CNPJ</label>
          <input 
            type="text" 
            id="cpf-cnpj" 
            name="cpf-cnpj"
            placeholder="000.000.000-00"
          >
        </div>
        <div class="form-group">
          <label for="rg-ie">RG / IE</label>
          <input 
            type="text" 
            id="rg-ie" 
            name="rg-ie"
            placeholder="00.000.000-0"
          >
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="telefone-cliente">Telefone</label>
          <input 
            type="tel" 
            id="telefone-cliente" 
            name="telefone-cliente"
            placeholder="(00) 00000-0000"
          >
        </div>
        <div class="form-group">
          <label for="email-cliente">Email</label>
          <input 
            type="email" 
            id="email-cliente" 
            name="email-cliente"
            placeholder="exemplo@email.com"
          >
        </div>
      </div>

      <div class="form-group">
        <label for="endereco-cliente">Endereço</label>
        <input 
          type="text" 
          id="endereco-cliente" 
          name="endereco-cliente"
          placeholder="Rua, número, bairro, cidade - UF"
        >
      </div>

      <div class="form-group">
        <label for="observacoes">Observações</label>
        <textarea 
          id="observacoes" 
          name="observacoes"
          placeholder="Informações adicionais sobre o cliente"
          rows="4"
        ></textarea>
      </div>

      <!-- RODAPÉ DO MODAL -->
      <footer class="clientes-modal-footer">
        <button 
          type="button" 
          class="btn-cancelar" 
          onclick="Clientes.fecharModal()"
        >
          Cancelar
        </button>
        <button type="submit" class="btn-salvar">Salvar</button>
      </footer>

    </form>

  </div>
</div>

<!-- SCRIPTS -->
<script src="clientes.js"></script>

<?php include('includes/footer.php'); ?>