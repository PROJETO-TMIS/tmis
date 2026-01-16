const Clientes = (() => {

  let clientes = [];
  let clientesFiltrados = [];
  let editandoId = null;

  // ✅ CAMINHO REAL
  const API_BASE = './api/clientes';

  let modal, tbody, mensagemVazia;

  // =============================
  // INIT
  // =============================
  document.addEventListener('DOMContentLoaded', () => {
    modal = document.getElementById('clientesModal');
    tbody = document.getElementById('clientes-tbody');
    mensagemVazia = document.getElementById('mensagem-vazia');

    carregarClientes();
    eventos();
  });

  function eventos() {
    document
      .getElementById('pesquisa-clientes')
      ?.addEventListener('input', aplicarFiltros);

    document
      .getElementById('ordenacao-clientes')
      ?.addEventListener('change', aplicarFiltros);
  }

  // =============================
  // MODAL
  // =============================
  function abrirModal() {
    editandoId = null;
    document.getElementById('form-cliente').reset();
    document.getElementById('modal-titulo').innerText = 'Adicionar Cliente';
    modal.classList.add('ativo');
  }

  function fecharModal() {
    modal.classList.remove('ativo');
    editandoId = null;
  }

  // =============================
  // API
  // =============================
  async function carregarClientes() {
    try {
      const res = await fetch(`${API_BASE}/listar_clientes.php`);
      clientes = await res.json();
      aplicarFiltros();
    } catch (e) {
      console.error('Erro ao carregar clientes', e);
    }
  }

  async function salvarCliente(e) {
    e.preventDefault();

    const formData = new FormData();
    if (editandoId) formData.append('id', editandoId);

    formData.append('tipo', val('tipo-cliente'));
    formData.append('nome', val('nome-cliente'));
    formData.append('cpf_cnpj', val('cpf-cnpj'));
    formData.append('rg_ie', val('rg-ie'));
    formData.append('telefone', val('telefone-cliente'));
    formData.append('email', val('email-cliente'));
    formData.append('endereco', val('endereco-cliente'));
    formData.append('observacoes', val('observacoes'));

    try {
      await fetch(`${API_BASE}/salvar_cliente.php`, {
        method: 'POST',
        body: formData
      });

      fecharModal();
      carregarClientes();
    } catch (e) {
      console.error('Erro ao salvar cliente', e);
    }
  }

  async function excluirCliente(id) {
    if (!confirm('Deseja excluir este cliente?')) return;

    await fetch(`${API_BASE}/excluir_cliente.php?id=${id}`);
    carregarClientes();
  }

  // =============================
  // FILTROS
  // =============================
function aplicarFiltros() {
  const termo = val('pesquisa-clientes').toLowerCase();
  const ordem = val('ordenacao-clientes');

  clientesFiltrados = clientes.filter(c => {
    const nome = (c.nome || '').toLowerCase();
    const telefone = (c.telefone || '').toLowerCase();
    const email = (c.email || '').toLowerCase();

    return (
      nome.includes(termo) ||
      telefone.includes(termo) ||
      email.includes(termo)
    );
  });

  if (ordem === 'az') {
    clientesFiltrados.sort((a, b) =>
      (a.nome || '').localeCompare(b.nome || '')
    );
  } else if (ordem === 'za') {
    clientesFiltrados.sort((a, b) =>
      (b.nome || '').localeCompare(a.nome || '')
    );
  }

  renderizar();
}


  // =============================
  // TABELA
  // =============================
  function renderizar() {
    tbody.innerHTML = '';

    if (clientesFiltrados.length === 0) {
      mensagemVazia.style.display = 'block';
      return;
    }

    mensagemVazia.style.display = 'none';

    clientesFiltrados.forEach(c => {
      tbody.innerHTML += `
        <tr>
          <td>${c.tipo === 'fisica' ? '👤' : '🏢'}</td>
          <td>
            <a href="#" onclick="Clientes.editar(${c.id}); return false;">
              ${c.nome}
            </a>
          </td>
          <td>${c.telefone || '-'}</td>
          <td>${c.email || '-'}</td>
          <td>
            <button class="btn-acao btn-editar" onclick="Clientes.editar(${c.id})">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn-acao btn-excluir" onclick="Clientes.excluir(${c.id})">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    });
  }

  function editar(id) {
    const c = clientes.find(x => x.id == id);
    if (!c) return;

    editandoId = id;
    document.getElementById('modal-titulo').innerText = 'Editar Cliente';

    setVal('tipo-cliente', c.tipo);
    setVal('nome-cliente', c.nome);
    setVal('cpf-cnpj', c.cpf_cnpj);
    setVal('rg-ie', c.rg_ie);
    setVal('telefone-cliente', c.telefone);
    setVal('email-cliente', c.email);
    setVal('endereco-cliente', c.endereco);
    setVal('observacoes', c.observacoes);

    modal.classList.add('ativo');
  }

  // =============================
  // HELPERS
  // =============================
  function val(id) {
    return document.getElementById(id)?.value || '';
  }

  function setVal(id, valor) {
    const el = document.getElementById(id);
    if (el) el.value = valor ?? '';
  }

  // =============================
  // PUBLIC
  // =============================
  return {
    abrirModal,
    fecharModal,
    salvarCliente,
    excluir: excluirCliente,
    editar
  };

})();
