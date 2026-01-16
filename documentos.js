// documentos.js — versão revisada, segura e idempotente
console.log('documentos.js carregado');

/* ================= CONFIG ================= */
const DOC_ACTIVE_CLASS = 'doc-ativo';

/* ================= UTIL ================= */
function doc_toggleBodyScroll(disable) {
  document.body.style.overflow = disable ? 'hidden' : '';
}

function doc_showOverlay(id) {
  const el = document.getElementById(id);
  if (!el) return console.warn('Overlay não encontrado:', id);
  el.classList.add(DOC_ACTIVE_CLASS);
  el.style.display = 'flex';
  doc_toggleBodyScroll(true);
}

function doc_hideOverlay(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove(DOC_ACTIVE_CLASS);
  el.style.display = 'none';
  doc_toggleBodyScroll(false);
}

/* ================= INICIALIZAÇÃO ================= */
document.addEventListener('DOMContentLoaded', () => {
  // Carrega modelos e contratos (mantém compatibilidade com versão antiga)
  try { doc_loadModels(); } catch (e) { console.warn('doc_loadModels erro', e); }
  try { carregarContratos(); } catch (e) { console.warn('carregarContratos erro', e); }

  // Inicializa formulário de upload (se presente)
  initUploadForm();

  // Não fechamos mais o modal ao clicar fora (UX ruim) — só por ESC ou pelo botão de fechar.
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      ['uploadOverlay', 'docOverlay', 'editarOverlay'].forEach(id => {
        const el = document.getElementById(id);
        if (el && el.classList.contains(DOC_ACTIVE_CLASS)) doc_hideOverlay(id);
      });
    }
  }, false);
});

/* ================= MODAIS ================= */
function doc_openUploadModal() { doc_showOverlay('uploadOverlay'); }
function doc_closeUploadModal() { doc_hideOverlay('uploadOverlay'); }

function doc_openCreateModal(modelo = '', titulo = '') {
  const input = document.getElementById('modeloSelecionado');
  const title = document.getElementById('modalTitulo');
  if (input) input.value = modelo || '';
  if (title) title.innerText = titulo ? `Criar ${titulo}` : 'Composição do Documento';

  // reset do estado do modal (limpo)
  resetDocModal();

  // mostrar modal e inicializar lógica (idempotente)
  doc_showOverlay('docOverlay');
  initDocModal();
}

function doc_closeCreateModal() { doc_hideOverlay('docOverlay'); }

/* ================= UPLOAD MODELO ================= */
function initUploadForm() {
  const form = document.getElementById('formUploadModelo');
  if (!form) return;
  if (form._tmis_attached) return;
  form._tmis_attached = true;

  form.addEventListener('submit', e => {
    e.preventDefault();
    enviarModelo(false);
  });
}

async function enviarModelo(confirmar) {
  const form = document.getElementById('formUploadModelo');
  if (!form) return;

  const fd = new FormData(form);
  if (confirmar) fd.append('confirmar', '1');

  try {
    const res = await fetch('upload_modelo.php', { method: 'POST', body: fd, cache: 'no-store' });
    const data = await res.json();

    if (data.confirmacao) {
      if (confirm(data.mensagem)) enviarModelo(true);
      return;
    }

    if (data.sucesso) finalizarUpload(data.arquivo);
    else alert(data.erro || 'Erro ao enviar modelo');

  } catch (err) {
    console.error(err);
    alert('Erro de comunicação com o servidor');
  }
}

function finalizarUpload(arquivo) {
  alert(`Modelo "${arquivo}" salvo com sucesso.`);
  doc_closeUploadModal();
  try { doc_loadModels(); } catch (e) {}
  const form = document.getElementById('formUploadModelo');
  if (form) form.reset();
  const label = document.getElementById('nomeArquivo');
  if (label) label.innerText = 'Nenhum arquivo selecionado';
}

/* ================= LISTAR MODELOS ================= */
function doc_loadModels() {
  fetch('listar_modelos.php', { cache: 'no-store' })
    .then(r => r.json())
    .then(modelos => {
      const container = document.getElementById('modelosContainer');
      if (!container) return;
      container.innerHTML = '';

      if (!Array.isArray(modelos) || modelos.length === 0) {
        container.innerHTML = '<p>Nenhum modelo encontrado</p>';
        return;
      }

      modelos.forEach(m => {
        const card = document.createElement('div');
        card.className = 'doc-card';
        card.innerHTML = `
          <div class="card-icon"><i class="far fa-file-lines"></i></div>
          <div class="card-title">${escapeHtml(m.nome)}</div>
          <button class="card-action-btn">CRIAR DOCUMENTO</button>
          <div class="card-actions-secondary">
            <button class="btn-edit"><i class="fas fa-pen"></i> Editar</button>
            <button class="btn-delete"><i class="fas fa-trash"></i> Excluir</button>
          </div>
        `;

        const btnCreate = card.querySelector('.card-action-btn');
        if (btnCreate) btnCreate.addEventListener('click', () => doc_openCreateModal(m.arquivo, m.nome));

        const btnEdit = card.querySelector('.btn-edit');
        if (btnEdit) btnEdit.addEventListener('click', () => doc_openEditModal(m.arquivo, m.nome));

        const btnDel = card.querySelector('.btn-delete');
        if (btnDel) btnDel.addEventListener('click', () => doc_deleteModel(m.arquivo));

        container.appendChild(card);
      });
    })
    .catch(() => {
      const c = document.getElementById('modelosContainer');
      if (c) c.innerHTML = '<p>Erro ao carregar modelos</p>';
    });
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

/* ================= EXCLUIR MODELO ================= */
function doc_deleteModel(arquivo) {
  if (!confirm('Deseja excluir este modelo?')) return;
  const fd = new FormData();
  fd.append('arquivo', arquivo);

  fetch('excluir_modelo.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.sucesso) {
        alert('Modelo excluído.');
        doc_loadModels();
      } else {
        alert(d.erro || 'Erro ao excluir');
      }
    })
    .catch(err => {
      console.error('Erro excluir modelo', err);
      alert('Erro ao excluir modelo');
    });
}

/* ================= CONTRATOS ================= */
function carregarContratos() {
  fetch('listar_contratos.php', { cache: 'no-store' })
    .then(r => r.json())
    .then(lista => {
      const cont = document.getElementById('contratosContainer');
      if (!cont) return;
      cont.innerHTML = '';

      if (!Array.isArray(lista) || lista.length === 0) {
        cont.innerHTML = '<p>Nenhum documento gerado.</p>';
        return;
      }

      lista.forEach(c => {
        const card = document.createElement('div');
        card.className = 'doc-card saved-doc';
        const titulo = document.createElement('div');
        titulo.className = 'card-title';
        titulo.textContent = c.nome;
        const btns = document.createElement('div');
        btns.className = 'card-actions-secondary';

        const baixar = document.createElement('a');
        baixar.href = 'contratos/' + encodeURIComponent(c.arquivo);
        baixar.textContent = 'Baixar';
        baixar.className = 'btn-download';
        baixar.setAttribute('download', '');

        const excluir = document.createElement('button');
        excluir.textContent = 'Excluir';
        excluir.className = 'btn-delete';
        excluir.onclick = () => excluirContrato(c.arquivo);

        btns.appendChild(baixar);
        btns.appendChild(excluir);
        card.appendChild(titulo);
        card.appendChild(btns);
        cont.appendChild(card);
      });
    })
    .catch(e => console.error('Erro ao listar contratos', e));
}

function excluirContrato(arquivo) {
  if (!confirm('Tem certeza que deseja excluir este contrato?')) return;
  fetch('excluir_contrato.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ arquivo })
  })
    .then(r => r.json())
    .then(res => {
      if (res.sucesso) {
        alert('Contrato excluído.');
        carregarContratos();
      } else {
        alert(res.erro || 'Erro ao excluir contrato.');
      }
    })
    .catch(err => { console.error('Erro excluir contrato', err); alert('Erro ao excluir contrato'); });
}

/* ================= MODAL AVANÇADO (ABAS / PF-PJ / CLIENTES) ================= */
/* Estado */
let docClientes = [];
let docTipoCliente = 'PF';

/**
 * initDocModal - idempotente; chama ao abrir o modal
 */
function initDocModal() {
  const overlay = document.getElementById('docOverlay');
  if (!overlay) return;

  // Evitar rebinds repetidos: flags simples em overlay
  if (overlay._tmis_initialized) {
    // apenas atualiza a renderização (caso o modal tenha sido reaberto)
    atualizarCamposTipo(overlay);
    renderClientes();
    return;
  }
  overlay._tmis_initialized = true;

  // ABAS
  const tabs = overlay.querySelectorAll('.doc-tabs button');
  const contents = overlay.querySelectorAll('.doc-tab-content');
  if (tabs.length && contents.length) {
    tabs.forEach((btn, i) => {
      btn.addEventListener('click', () => {
        tabs.forEach(b => b.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        if (contents[i]) contents[i].classList.add('active');
      });
    });
  }

  // PF / PJ
  const typeBtns = overlay.querySelectorAll('.doc-type-selector button');
  if (typeBtns.length) {
    typeBtns.forEach((btn, i) => {
      btn.addEventListener('click', () => {
        typeBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        docTipoCliente = i === 0 ? 'PF' : 'PJ';
        atualizarCamposTipo(overlay);
      });
    });
  }

  // ADICIONAR CLIENTE
  const addBtn = overlay.querySelector('.btn-add-client');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      const cliente = coletarDadosCliente(overlay);
      if (!cliente) return;
      docClientes.push(cliente);
      renderClientes();
      limparFormularioCliente(overlay);
    });
  }

  // SUBMIT: serializar clientes para backend (garante que exista pelo menos um cliente)
  const formCriar = overlay.querySelector('#formCriarDocumento');
  if (formCriar && !formCriar._tmis_submit) {
    formCriar._tmis_submit = true;

    formCriar.addEventListener('submit', (e) => {
      // --- ESSENCIAL: desativa validação nativa temporariamente ---
      const requiredEls = Array.from(formCriar.querySelectorAll('[required]'));
      requiredEls.forEach(el => el.removeAttribute('required'));

      // validação real (JS)
      if (!docClientes.length) {
        e.preventDefault();
        alert('Adicione pelo menos um cliente antes de gerar o documento.');

        // reativa requireds para não perder comportamento caso o usuário continue no modal
        requiredEls.forEach(el => el.setAttribute('required', ''));
        return;
      }

      // serializa clientes
      let h = formCriar.querySelector('input[name="clientes_json"]');
      if (!h) {
        h = document.createElement('input');
        h.type = 'hidden';
        h.name = 'clientes_json';
        formCriar.appendChild(h);
      }
      h.value = JSON.stringify(docClientes || []);
      // allow normal submit (will send clientes_json)
    });
  }

  // Inicializa busca CEP dentro do overlay (idempotente)
  initCepAutoFill(overlay);

  // Render inicial
  atualizarCamposTipo(overlay);
  renderClientes();
}

/* ================= CEP (ViaCEP) ================= */
function debounce(fn, wait) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
}

function initCepAutoFill(overlay) {
  if (!overlay) overlay = document;
  const cepInput = overlay.querySelector('input[name="CEP"]');
  if (!cepInput) return;
  if (cepInput._tmis_cep) return;
  cepInput._tmis_cep = true;

  // busca automática ao digitar 8 dígitos (debounced)
  const onCepTyped = debounce(async () => {
    const cep = (cepInput.value || '').replace(/\D/g, '');
    if (cep.length !== 8) return;

    try {
      const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
      const data = await res.json();
      if (data.erro) { /* não alertar sempre para não incomodar */ return; }

      const formArea = cepInput.closest('.doc-form-area') || overlay.querySelector('.doc-form-area');
      if (!formArea) return;

      formArea.querySelector('input[name="RUA"]') && (formArea.querySelector('input[name="RUA"]').value = data.logradouro || '');
      formArea.querySelector('input[name="BAIRRO"]') && (formArea.querySelector('input[name="BAIRRO"]').value = data.bairro || '');
      formArea.querySelector('input[name="CIDADE"]') && (formArea.querySelector('input[name="CIDADE"]').value = data.localidade || '');
      formArea.querySelector('select[name="UF"]') && (formArea.querySelector('select[name="UF"]').value = data.uf || '');
    } catch (err) {
      console.error('Erro ViaCEP', err);
    }
  }, 400);

  cepInput.addEventListener('input', onCepTyped);
  // também mantemos blur para compatibilidade
  cepInput.addEventListener('blur', () => onCepTyped());
}

/* ================= funções de cliente (scoped) ================= */
function atualizarCamposTipo(overlay) {
  if (!overlay) overlay = document;
  overlay.querySelectorAll('[data-pf]').forEach(el => {
    el.style.display = docTipoCliente === 'PF' ? '' : 'none';
  });
  overlay.querySelectorAll('[data-pj]').forEach(el => {
    el.style.display = docTipoCliente === 'PJ' ? '' : 'none';
  });

  // sincroniza estado visual dos botões PF/PJ (se existirem)
  const typeBtns = overlay.querySelectorAll('.doc-type-selector button');
  if (typeBtns.length) {
    typeBtns.forEach((b, i) => {
      const shouldActive = (i === 0 && docTipoCliente === 'PF') || (i === 1 && docTipoCliente === 'PJ');
      b.classList.toggle('active', shouldActive);
    });
  }
}

function coletarDadosCliente(overlay) {
  if (!overlay) overlay = document.querySelector('#docOverlay') || document;
  const formArea = overlay.querySelector('.doc-form-area');
  if (!formArea) return null;

  const nome = (formArea.querySelector('input[name="NOME"]')?.value || '').trim();
  const razao = (formArea.querySelector('input[name="RAZAO_SOCIAL"]')?.value || '').trim();
  const cpfcnpj = (formArea.querySelector('input[name="CPF_CNPJ"]')?.value || '').trim();
  const telefone = (formArea.querySelector('input[name="TELEFONE"]')?.value || '').trim();
  const email = (formArea.querySelector('input[name="EMAIL"]')?.value || '').trim();
  const rua = (formArea.querySelector('input[name="RUA"]')?.value || '').trim();
  const numero = (formArea.querySelector('input[name="NUMERO"]')?.value || '').trim();
  const bairro = (formArea.querySelector('input[name="BAIRRO"]')?.value || '').trim();
  const cidade = (formArea.querySelector('input[name="CIDADE"]')?.value || '').trim();
  const uf = (formArea.querySelector('select[name="UF"]')?.value || '').trim();
  const cep = (formArea.querySelector('input[name="CEP"]')?.value || '').trim();

  if (docTipoCliente === 'PF' && !nome) { alert('Nome do cliente é obrigatório.'); return null; }
  if (docTipoCliente === 'PJ' && !razao) { alert('Razão social é obrigatória.'); return null; }

  return {
    tipo: docTipoCliente,
    nome: docTipoCliente === 'PF' ? nome : razao,
    cpf_cnpj: cpfcnpj,
    contato: { telefone, email },
    endereco: { rua, numero, bairro, cidade, uf, cep },
    meta: { timestamp: Date.now() }
  };
}

function renderClientes() {
  const list = document.getElementById('docClientList');
  if (!list) return;
  list.innerHTML = '';

  if (!docClientes.length) {
    const li = document.createElement('li');
    li.id = 'docClientEmptyMsg';
    li.textContent = 'Nenhum cliente adicionado.';
    list.appendChild(li);
    return;
  }

  docClientes.forEach((c, i) => {
    const li = document.createElement('li');
    li.innerHTML = `
      <span><i class="fas fa-user"></i> ${escapeHtml(c.nome)}</span>
      <span>
        <button type="button" class="doc-edit" data-i="${i}" title="Editar"><i class="fas fa-pen"></i></button>
        <button type="button" class="doc-remove" data-i="${i}" title="Remover"><i class="fas fa-trash"></i></button>
      </span>
    `;
    list.appendChild(li);
  });

  // attach handlers (safe to call multiple times)
  list.querySelectorAll('.doc-remove').forEach(btn => {
    btn.onclick = () => {
      const i = Number(btn.getAttribute('data-i'));
      removerCliente(i);
    };
  });
  list.querySelectorAll('.doc-edit').forEach(btn => {
    btn.onclick = () => {
      const i = Number(btn.getAttribute('data-i'));
      editarCliente(i);
    };
  });
}

function removerCliente(index) {
  if (!confirm('Remover este cliente?')) return;
  docClientes.splice(index, 1);
  renderClientes();
}

function editarCliente(index) {
  const cliente = docClientes[index];
  if (!cliente) return;
  const formArea = document.querySelector('.doc-form-area');
  if (!formArea) return;

  if (cliente.tipo === 'PF') formArea.querySelector('input[name="NOME"]').value = cliente.nome || '';
  else formArea.querySelector('input[name="RAZAO_SOCIAL"]').value = cliente.nome || '';

  formArea.querySelector('input[name="CPF_CNPJ"]').value = cliente.cpf_cnpj || '';
  formArea.querySelector('input[name="TELEFONE"]').value = cliente.contato?.telefone || '';
  formArea.querySelector('input[name="EMAIL"]').value = cliente.contato?.email || '';
  formArea.querySelector('input[name="RUA"]').value = cliente.endereco?.rua || '';
  formArea.querySelector('input[name="NUMERO"]').value = cliente.endereco?.numero || '';
  formArea.querySelector('input[name="BAIRRO"]').value = cliente.endereco?.bairro || '';
  formArea.querySelector('input[name="CIDADE"]').value = cliente.endereco?.cidade || '';
  if (formArea.querySelector('select[name="UF"]')) formArea.querySelector('select[name="UF"]').value = cliente.endereco?.uf || '';
  formArea.querySelector('input[name="CEP"]').value = cliente.endereco?.cep || '';

  // remove entry and keep for re-save
  docClientes.splice(index, 1);
  renderClientes();
}

function limparFormularioCliente(overlay) {
  if (!overlay) overlay = document.querySelector('#docOverlay') || document;
  overlay.querySelectorAll('.doc-form-area input, .doc-form-area textarea, .doc-form-area select')
    .forEach(el => el.value = '');
}

function resetDocModal() {
  docClientes = [];
  docTipoCliente = 'PF';
  // nao chamamos atualizarCamposTipo sem overlay aqui; initDocModal vai sincronizar visual
  renderClientes();
}

/* ================= EDIT MODEL MODAL ================= */
function doc_openEditModal(arquivo, nome) {
  const overlay = document.getElementById('editarOverlay');
  if (!overlay) {
    console.warn('editarOverlay não encontrado');
    return;
  }

  const oldInput = document.getElementById('editarArquivoAntigo');
  const nameInput = document.getElementById('editarNomeModelo');
  if (oldInput) oldInput.value = arquivo || '';
  if (nameInput) nameInput.value = nome || '';

  doc_showOverlay('editarOverlay');
}

function doc_closeEditModal() {
  doc_hideOverlay('editarOverlay');
}
