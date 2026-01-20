<?php include('includes/head.php'); ?>

<?php include('includes/header.php'); ?>

<main class="main-content agenda-section">
    
  <!-- CABEÇALHO DA PÁGINA -->
  <header class="agenda-header">
    <h1>Agenda</h1>
    <button type="button" class="add-evento-btn" onclick="abrirModalAgenda()" aria-label="Criar novo compromisso">
      <i class="fas fa-plus" aria-hidden="true"></i> Criar
    </button>
  </header>

  <!-- CALENDÁRIO -->
  <section class="calendar-container" aria-label="Calendário de compromissos">
    <div id="calendario"></div>
  </section>

</main>

<!-- MODAL DE COMPROMISSO -->
<div id="modal-agenda" class="modal" role="dialog" aria-labelledby="modal-titulo-agenda" aria-hidden="true">
  <div class="modal-content">
    
    <!-- CABEÇALHO DO MODAL -->
    <header class="modal-header">
      <h2 id="modal-titulo-agenda">Novo Compromisso</h2>
      <button type="button" class="close" onclick="fecharModalAgenda()" aria-label="Fechar modal">
        &times;
      </button>
    </header>

    <!-- FORMULÁRIO -->
    <form id="form-agenda" onsubmit="salvarEvento(event)">
      
      <div class="form-group">
        <label for="titulo-evento">Título do Compromisso*</label>
        <input 
          type="text" 
          id="titulo-evento" 
          name="titulo-evento"
          placeholder="Adicionar título" 
          required 
          aria-required="true"
        >
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="data-evento">Data*</label>
          <input 
            type="date" 
            id="data-evento" 
            name="data-evento"
            required 
            aria-required="true"
          >
        </div>
        <div class="form-group">
          <label for="hora-evento">Hora</label>
          <input 
            type="time" 
            id="hora-evento" 
            name="hora-evento"
          >
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="prioridade-evento">Prioridade</label>
          <select id="prioridade-evento" name="prioridade-evento">
            <option value="baixa">Baixa</option>
            <option value="media" selected>Média</option>
            <option value="alta">Alta</option>
            <option value="urgente">Urgente</option>
          </select>
        </div>
        <div class="form-group">
          <label for="categoria-evento">Categoria</label>
          <select id="categoria-evento" name="categoria-evento">
            <option value="reuniao">Reunião</option>
            <option value="prazo">Prazo Processual</option>
            <option value="audiencia">Audiência</option>
            <option value="outro">Outro</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="descricao-evento">Descrição</label>
        <textarea 
          id="descricao-evento" 
          name="descricao-evento"
          rows="3" 
          placeholder="Adicionar descrição"
        ></textarea>
      </div>

      <input type="hidden" id="evento-id" name="evento-id">

      <!-- RODAPÉ DO MODAL -->
      <footer class="modal-footer">
        <button type="button" class="btn-cancelar" onclick="fecharModalAgenda()">
          Cancelar
        </button>
        <button type="submit" class="btn-salvar">Salvar</button>
      </footer>
      
    </form>
  </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
<script src="agenda.js"></script>

<?php include('includes/footer.php'); ?>