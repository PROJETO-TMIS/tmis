<?php include('includes/head.php'); ?>

<?php include('includes/header.php'); ?>

<link rel="stylesheet" href="style/configuracoes">

<main class="main-content configuracoes-section">
    
  <!-- CABEÇALHO DA PÁGINA -->
  <header class="configuracoes-header">
    <h1><i class="fas fa-gear" aria-hidden="true"></i> Configurações</h1>
  </header>

  <!-- CONTEÚDO DAS CONFIGURAÇÕES -->
  <section class="configuracoes-content">
    
    <!-- Seção de Perfil -->
    <article class="config-card">
      <h2>Perfil do Usuário</h2>
      <form id="form-perfil">
        <div class="form-group">
          <label for="nome-usuario">Nome Completo</label>
          <input type="text" id="nome-usuario" name="nome-usuario" value="Samuel Lima">
        </div>
        
        <div class="form-group">
          <label for="email-usuario">Email</label>
          <input type="email" id="email-usuario" name="email-usuario" value="samuel@tmis.com">
        </div>
        
        <div class="form-group">
          <label for="telefone-usuario">Telefone</label>
          <input type="tel" id="telefone-usuario" name="telefone-usuario" value="(17) 99999-9999">
        </div>
        
        <button type="submit" class="btn-salvar">Salvar Alterações</button>
      </form>
    </article>

    <!-- Seção de Segurança -->
    <article class="config-card">
      <h2>Segurança</h2>
      <form id="form-senha">
        <div class="form-group">
          <label for="senha-atual">Senha Atual</label>
          <input type="password" id="senha-atual" name="senha-atual">
        </div>
        
        <div class="form-group">
          <label for="nova-senha">Nova Senha</label>
          <input type="password" id="nova-senha" name="nova-senha">
        </div>
        
        <div class="form-group">
          <label for="confirmar-senha">Confirmar Nova Senha</label>
          <input type="password" id="confirmar-senha" name="confirmar-senha">
        </div>
        
        <button type="submit" class="btn-salvar">Alterar Senha</button>
      </form>
    </article>

    <!-- Seção de Notificações -->
    <article class="config-card">
      <h2>Notificações</h2>
      <div class="config-options">
        <div class="config-item">
          <label for="notif-email">
            <input type="checkbox" id="notif-email" name="notif-email" checked>
            Receber notificações por email
          </label>
        </div>
        
        <div class="config-item">
          <label for="notif-tarefas">
            <input type="checkbox" id="notif-tarefas" name="notif-tarefas" checked>
            Alertas de tarefas
          </label>
        </div>
        
        <div class="config-item">
          <label for="notif-publicacoes">
            <input type="checkbox" id="notif-publicacoes" name="notif-publicacoes" checked>
            Novas publicações
          </label>
        </div>
        
        <div class="config-item">
          <label for="notif-compromissos">
            <input type="checkbox" id="notif-compromissos" name="notif-compromissos" checked>
            Lembretes de agenda
          </label>
        </div>
        
        <button type="button" class="btn-salvar" onclick="salvarNotificacoes()">Salvar Preferências</button>
      </div>
    </article>

    <!-- Seção de Aparência -->
    <article class="config-card">
      <h2>Aparência</h2>
      <div class="config-options">
        <div class="form-group">
          <label for="tema">Tema do Sistema</label>
          <select id="tema" name="tema">
            <option value="claro">Claro</option>
            <option value="escuro">Escuro</option>
            <option value="auto">Automático</option>
          </select>
        </div>
        
        <button type="button" class="btn-salvar" onclick="alterarTema()">Aplicar Tema</button>
      </div>
    </article>

  </section>

</main>

<!-- SCRIPTS -->
<script src="configuracoes.js"></script>

<?php include('includes/footer.php'); ?>