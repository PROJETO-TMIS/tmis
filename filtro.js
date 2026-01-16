document.addEventListener("DOMContentLoaded", function () {

  const buttons = document.querySelectorAll('.filter-btn');
  const painelPersonalizado = document.getElementById('filtro-personalizado');
  const btnAplicar = document.getElementById('aplicar-filtro');
  const tbody = document.getElementById('task-tbody');
  const contador = document.getElementById('qtd-tarefas');
  const modal = document.getElementById('modal');
  const taskForm = document.getElementById('task-form');
  const modalTitle = document.getElementById('modal-title');
  const addBtn = document.querySelector('.add-btn');
  const closeModal = document.querySelector('.close-modal');
  const cancelBtn = document.getElementById('cancelar');
  const confirmarExclusaoModal = document.getElementById('confirmar-exclusao');
  const cancelarExclusaoBtn = document.getElementById('cancelar-exclusao');
  const confirmarExcluirBtn = document.getElementById('confirmar-excluir');

  let tarefas = [];
  let tarefaParaExcluir = null;

  // ===============================
  // BANCO DE DADOS
  // ===============================

  async function carregarTarefas() {
    const response = await fetch('api/tarefas/listar_tarefas.php');
    tarefas = await response.json();
    renderizarTabela();
  }

  async function excluirTarefa(id) {
    await fetch(`api/tarefas/excluir_tarefa.php?id=${id}`);
    await carregarTarefas();
  }

  // ===============================
  // RENDERIZAÇÃO
  // ===============================

  function renderizarTabela() {
    tbody.innerHTML = '';

    tarefas.forEach((tarefa) => {
      const tr = document.createElement('tr');

      const prioridadeTexto =
        tarefa.prioridade === 'Alta' ? 'Alta' :
        tarefa.prioridade === 'Media' ? 'Média' : 'Baixa';

      tr.innerHTML = `
        <td>${tarefa.descricao}</td>
        <td>${tarefa.data}</td>
        <td class="prioridade">
          <span class="prioridade-tag ${tarefa.prioridade.toLowerCase()}">
            ${prioridadeTexto}
          </span>
        </td>
        <td>${tarefa.cliente || '-'}</td>
        <td>${tarefa.obs || '-'}</td>
        <td>${tarefa.responsaveis || '-'}</td>
        <td>
          <div class="action-icons">
            <i class="fas fa-trash action-icon delete-icon"></i>
          </div>
        </td>
      `;

      tr.querySelector('.delete-icon').addEventListener('click', () => {
        tarefaParaExcluir = tarefa.id;
        confirmarExclusaoModal.classList.remove('hidden');
      });

      tbody.appendChild(tr);
    });

    aplicarStatusNasDatas();
    atualizarContadorTarefas();
  }

  // ===============================
  // STATUS DAS DATAS
  // ===============================

  function aplicarStatusNasDatas() {
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    const rows = tbody.querySelectorAll('tr');
    rows.forEach(row => {
      const texto = row.children[1].innerText;
      const data = new Date(texto.split('/').reverse().join('-'));
      data.setHours(0, 0, 0, 0);

      const diff = Math.floor((data - hoje) / (1000 * 60 * 60 * 24));
      let classe = '';

      if (diff < 0) classe = 'data-status-vermelha';
      else if (diff === 0) classe = 'data-status-laranja';
      else if (diff === 1) classe = 'data-status-azul';
      else classe = 'data-status-verde';

      row.children[1].innerHTML = `<span class="status-ball ${classe}"></span>${texto}`;
    });
  }

  function atualizarContadorTarefas() {
    contador.innerText = tbody.querySelectorAll('tr').length;
  }

  // ===============================
  // FILTROS
  // ===============================

  function aplicarFiltro(filter) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const rows = tbody.querySelectorAll('tr');

    rows.forEach(row => {
      const dateText = row.children[1].innerText;
      const taskDate = new Date(dateText.split('/').reverse().join('-'));
      taskDate.setHours(0, 0, 0, 0);

      if (filter === 'hoje') {
        row.style.display = taskDate.getTime() === today.getTime() ? '' : 'none';
      } else if (filter === 'atrasadas') {
        row.style.display = taskDate < today ? '' : 'none';
      } else {
        row.style.display = '';
      }
    });

    aplicarStatusNasDatas();
    atualizarContadorTarefas();
  }

  buttons.forEach(button => {
    button.addEventListener('click', () => {
      buttons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');

      const filter = button.dataset.filter;
      painelPersonalizado.classList.toggle('hidden', filter !== 'personalizado');
      aplicarFiltro(filter);
    });
  });

  btnAplicar.addEventListener('click', aplicarFiltro);

  // ===============================
  // MODAL
  // ===============================

  addBtn.addEventListener('click', () => {
    modalTitle.innerText = 'Adicionar tarefa';
    taskForm.reset();
    modal.classList.remove('hidden');
  });

  closeModal.addEventListener('click', () => modal.classList.add('hidden'));
  cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));

  // ===============================
  // SALVAR TAREFA
  // ===============================

  taskForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const tarefa = {
      descricao: document.getElementById('descricao').value.trim(),
      data: document.getElementById('data').value.split('-').reverse().join('/'),
      prioridade: document.getElementById('prioridade').value,
      obs: document.getElementById('obs').value.trim(),
      responsaveis: document.getElementById('responsaveis').value.trim()
    };

    await fetch('api/tarefas/salvar_tarefa.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(tarefa)
    });

    modal.classList.add('hidden');
    await carregarTarefas();
    this.reset();
  });

  // ===============================
  // CONFIRMAÇÃO DE EXCLUSÃO
  // ===============================

  confirmarExcluirBtn.addEventListener('click', async () => {
    if (tarefaParaExcluir) {
      await excluirTarefa(tarefaParaExcluir);
      tarefaParaExcluir = null;
    }
    confirmarExclusaoModal.classList.add('hidden');
  });

  cancelarExclusaoBtn.addEventListener('click', () => {
    confirmarExclusaoModal.classList.add('hidden');
    tarefaParaExcluir = null;
  });

  // ===============================
  // INICIAR
  // ===============================

  carregarTarefas();
});
