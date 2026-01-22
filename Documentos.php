<?php include('includes/head.php'); ?>

<?php include('includes/header.php'); ?>

<link rel="stylesheet" href="style/documentos.css">

<main class="main-content content-container">

  <!-- SEÇÃO: MODELOS DE DOCUMENTOS -->
  <section class="modelos-section">
    <header class="section-header">
      <div class="title-with-icon">
        <i class="fas fa-folder-open" aria-hidden="true"></i>
        <h1>Modelos de Documentos</h1>
      </div>

      <button type="button" class="btn-add-model" onclick="doc_openUploadModal()" aria-label="Enviar novo modelo">
        <i class="fas fa-upload" aria-hidden="true"></i> Enviar Modelo
      </button>
    </header>

    <div class="cards-grid" id="modelosContainer" role="list" aria-label="Lista de modelos de documentos">
      <!-- Modelos serão inseridos aqui dinamicamente -->
    </div>
  </section>

  <!-- SEÇÃO: DOCUMENTOS GERADOS -->
  <section class="documentos-gerados-section">
    <header class="section-header">
      <div class="title-with-icon">
        <i class="fas fa-file-contract" aria-hidden="true"></i>
        <h2>Documentos Gerados</h2>
      </div>
    </header>

    <div class="cards-grid" id="contratosContainer" role="list" aria-label="Lista de documentos gerados">
      <!-- Documentos gerados serão inseridos aqui dinamicamente -->
    </div>
  </section>

</main>

<!-- MODAL: CRIAR DOCUMENTO (AVANÇADO) -->
<div id="docOverlay" class="overlay" role="dialog" aria-labelledby="modalTitulo" aria-hidden="true">
  <div class="doc-modal doc-modal-large">

    <!-- CABEÇALHO DO MODAL -->
    <header class="doc-modal-header">
      <div>
        <h3 id="modalTitulo">Composição do Documento</h3>
        <p class="doc-model-name">Modelo selecionado</p>
      </div>

      <div class="doc-header-actions">
        <label for="doc-search" class="sr-only">Buscar cliente</label>
        <input
          type="search"
          id="doc-search"
          name="doc-search"
          placeholder="Buscar cliente (em breve)"
          disabled
          class="doc-search"
          aria-label="Buscar cliente"
        >
        <button type="button" class="doc-close" onclick="doc_closeCreateModal()" aria-label="Fechar modal">
          &times;
        </button>
      </div>
    </header>

    <!-- FORMULÁRIO -->
    <form id="formCriarDocumento" action="gerar_documento.php" method="POST">
      <input type="hidden" name="modelo" id="modeloSelecionado" value="">

      <div class="doc-modal-body">

        <!-- COLUNA ESQUERDA: FORMULÁRIO -->
        <div class="doc-form-area">

          <!-- SELETOR TIPO DE CLIENTE -->
          <div class="doc-type-selector" role="tablist" aria-label="Tipo de Cliente">
            <button type="button" class="active" role="tab" aria-selected="true" aria-controls="panel-pf">
              Pessoa Física
            </button>
            <button type="button" role="tab" aria-selected="false" aria-controls="panel-pj">
              Pessoa Jurídica
            </button>
          </div>

          <!-- NAVEGAÇÃO ENTRE ABAS -->
          <nav class="doc-tabs" role="tablist" aria-label="Navegação de informações">
            <button type="button" class="active" role="tab" aria-selected="true" aria-controls="tab-dados">
              Informações Pessoais
            </button>
            <button type="button" role="tab" aria-selected="false" aria-controls="tab-complementares">
              Complementares
            </button>
            <button type="button" role="tab" aria-selected="false" aria-controls="tab-documentacao">
              Documentação
            </button>
          </nav>

          <!-- ABA 1: INFORMAÇÕES PESSOAIS -->
          <div class="doc-tab-content active" id="tab-dados" role="tabpanel" aria-labelledby="tab-dados">

            <h4>Dados Principais</h4>

            <div class="doc-grid">

              <!-- CAMPOS PESSOA FÍSICA -->
              <div class="form-group" data-pf>
                <label for="nome-cliente">Nome do Cliente*</label>
                <input type="text" id="nome-cliente" name="NOME" placeholder="Nome completo" required aria-required="true">
              </div>

              <div class="form-group" data-pf>
                <label for="profissao">Profissão</label>
                <input type="text" id="profissao" name="PROFISSAO">
              </div>

              <div class="form-group" data-pf>
                <label for="estado-civil">Estado Civil</label>
                <select id="estado-civil" name="ESTADO_CIVIL">
                  <option value="">Selecione</option>
                  <option value="Solteiro">Solteiro(a)</option>
                  <option value="Casado">Casado(a)</option>
                  <option value="Divorciado">Divorciado(a)</option>
                  <option value="Viuvo">Viúvo(a)</option>
                  <option value="Uniao Estavel">União Estável</option>
                </select>
              </div>

              <div class="form-group" data-pf>
                <label for="nacionalidade">Nacionalidade</label>
                <select id="nacionalidade" name="NACIONALIDADE">
                  <option value="Brasileira">Brasileira</option>
                  <option value="Estrangeira">Estrangeira</option>
                </select>
              </div>

              <div class="form-group" data-pf>
                <label for="rg">RG</label>
                <input type="text" id="rg" name="RG">
              </div>

              <div class="form-group" data-pf>
                <label for="orgao-emissor">Órgão Emissor</label>
                <input type="text" id="orgao-emissor" name="ORGAO_EMISSOR">
              </div>

              <div class="form-group" data-pf>
                <label for="cpf">CPF*</label>
                <input type="text" id="cpf" name="CPF_CNPJ" placeholder="000.000.000-00" required aria-required="true">
              </div>

              <!-- CAMPOS PESSOA JURÍDICA (inicialmente ocultos) -->
              <div class="form-group" data-pj style="display:none;">
                <label for="razao-social">Razão Social*</label>
                <input type="text" id="razao-social" name="RAZAO_SOCIAL" placeholder="Razão social">
              </div>

              <div class="form-group" data-pj style="display:none;">
                <label for="nome-fantasia">Nome Fantasia</label>
                <input type="text" id="nome-fantasia" name="NOME_FANTASIA">
              </div>

              <div class="form-group" data-pj style="display:none;">
                <label for="cnpj">CNPJ*</label>
                <input type="text" id="cnpj" name="CNPJ" placeholder="00.000.000/0000-00">
              </div>

              <div class="form-group" data-pj style="display:none;">
                <label for="inscricao-estadual">Inscrição Estadual / Municipal</label>
                <input type="text" id="inscricao-estadual" name="INSCRICAO_ESTADUAL">
              </div>

            </div>

            <h4>Contato</h4>
            <div class="doc-grid">
              <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="TELEFONE" placeholder="(00) 00000-0000">
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="EMAIL" placeholder="email@exemplo.com">
              </div>
            </div>

            <h4>Endereço</h4>
            <div class="doc-grid">
              <div class="form-group">
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="CEP">
              </div>

              <div class="form-group">
                <label for="rua">Rua / Logradouro</label>
                <input type="text" id="rua" name="RUA">
              </div>

              <div class="form-group">
                <label for="numero">Número</label>
                <input type="text" id="numero" name="NUMERO">
              </div>

              <div class="form-group">
                <label for="bairro">Bairro</label>
                <input type="text" id="bairro" name="BAIRRO">
              </div>

              <div class="form-group">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="CIDADE">
              </div>

              <div class="form-group">
                <label for="uf">UF</label>
                <select id="uf" name="UF">
                  <option value="">Selecione</option>
                  <option value="AC">AC</option><option value="AL">AL</option><option value="AP">AP</option><option value="AM">AM</option>
                  <option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES">ES</option>
                  <option value="GO">GO</option><option value="MA">MA</option><option value="MT">MT</option><option value="MS">MS</option>
                  <option value="MG">MG</option><option value="PA">PA</option><option value="PB">PB</option><option value="PR">PR</option>
                  <option value="PE">PE</option><option value="PI">PI</option><option value="RJ">RJ</option><option value="RN">RN</option>
                  <option value="RS">RS</option><option value="RO">RO</option><option value="RR">RR</option><option value="SC">SC</option>
                  <option value="SP">SP</option><option value="SE">SE</option><option value="TO">TO</option>
                </select>
              </div>

              <div class="form-group">
                <label for="complemento">Complemento</label>
                <input type="text" id="complemento" name="COMPLEMENTO">
              </div>
            </div>

            <button type="button" class="btn-add-client">
              <i class="fas fa-plus" aria-hidden="true"></i> Adicionar cliente
            </button>

          </div>

          <!-- ABA 2: COMPLEMENTARES -->
          <div class="doc-tab-content" id="tab-complementares" role="tabpanel" aria-labelledby="tab-complementares" hidden>
            <h4>Serviço</h4>

            <div class="form-group">
              <label for="servico-desc">Descrição dos Serviços</label>
              <textarea id="servico-desc" name="SERVICO_DESC" rows="4"></textarea>
            </div>

            <div class="form-group">
              <label for="honorarios">Honorários Advocatícios (por extenso)</label>
              <textarea id="honorarios" name="HONORARIOS" rows="2"></textarea>
            </div>
          </div>

          <!-- ABA 3: DOCUMENTAÇÃO -->
          <div class="doc-tab-content" id="tab-documentacao" role="tabpanel" aria-labelledby="tab-documentacao" hidden>
            <h4>Documentação</h4>
            <p>
              Os documentos principais (CPF/CNPJ e RG) já foram preenchidos na aba anterior.
              Aqui você pode adicionar observações sobre documentos adicionais (CTPS, CNH, Passaporte etc.).
            </p>
          </div>

        </div>

        <!-- COLUNA DIREITA: PAINEL LATERAL -->
        <aside class="doc-side-panel">

          <h4>Clientes adicionados</h4>

          <ul class="doc-client-list" id="docClientList" aria-live="polite">
            <li id="docClientEmptyMsg">Nenhum cliente adicionado.</li>
          </ul>

          <button type="submit" class="btn-generate-doc">
            <i class="fas fa-file-word" aria-hidden="true"></i> Gerar Documento
          </button>

        </aside>

      </div>
    </form>

  </div>
</div>

<!-- MODAL: UPLOAD MODELO -->
<div id="uploadOverlay" class="overlay" role="dialog" aria-labelledby="upload-titulo" aria-hidden="true">
  <div class="modal">

    <header class="modal-header">
      <h3 id="upload-titulo"><i class="fas fa-file-upload" aria-hidden="true"></i> Novo Modelo</h3>
      <button type="button" class="close-modal" onclick="doc_closeUploadModal()" aria-label="Fechar modal">
        &times;
      </button>
    </header>

    <form action="upload_modelo.php" method="POST" enctype="multipart/form-data" id="formUploadModelo">
      
      <div class="form-group">
        <label for="nomeModelo">Nome do Modelo</label>
        <input
          type="text"
          id="nomeModelo"
          name="nome_modelo"
          placeholder="Ex: Contrato Padrão"
          required
          aria-required="true"
        >
      </div>

      <input type="hidden" name="confirmar" id="confirmarUpload" value="0">

      <label for="uploadArquivo" class="file-label">
        <i class="fas fa-file-word" aria-hidden="true"></i>
        Selecionar arquivo DOCX
      </label>

      <input
        type="file"
        id="uploadArquivo"
        name="arquivo"
        accept=".docx"
        hidden
        required
        aria-required="true"
        onchange="document.getElementById('nomeArquivo').innerText = this.files[0]?.name || 'Nenhum arquivo selecionado'"
      >

      <span id="nomeArquivo" class="file-name" aria-live="polite">
        Nenhum arquivo selecionado
      </span>

      <button type="submit" class="btn-confirmar">
        <i class="fas fa-save" aria-hidden="true"></i> Salvar Modelo
      </button>
    </form>

  </div>
</div>

<!-- MODAL: EDITAR MODELO -->
<div id="editarOverlay" class="overlay" role="dialog" aria-labelledby="editar-titulo" aria-hidden="true">
  <div class="editar-modal">

    <header class="editar-header">
      <h3 id="editar-titulo"><i class="fas fa-pen" aria-hidden="true"></i> Editar Modelo</h3>
      <button type="button" class="editar-close" onclick="doc_closeEditModal()" aria-label="Fechar modal">
        &times;
      </button>
    </header>

    <form action="editar_modelo.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="arquivo_antigo" id="editarArquivoAntigo">

      <div class="form-group">
        <label for="editarNomeModelo">Novo nome do modelo</label>
        <input type="text" id="editarNomeModelo" name="novo_nome" required aria-required="true">
      </div>

      <label for="editar-arquivo" class="file-label">
        <i class="fas fa-file-word" aria-hidden="true"></i>
        Substituir arquivo DOCX (opcional)
        <input type="file" id="editar-arquivo" name="novo_arquivo" accept=".docx" hidden>
      </label>

      <button type="submit" class="btn-confirmar">
        <i class="fas fa-save" aria-hidden="true"></i> Salvar Alterações
      </button>
    </form>

  </div>
</div>

<!-- SCRIPTS -->
<script src="documentos.js"></script>

<?php include('includes/footer.php'); ?>