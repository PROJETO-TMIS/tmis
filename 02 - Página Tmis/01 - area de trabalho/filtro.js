document.addEventListener("DOMContentLoaded", function () {
  const buttons = document.querySelectorAll('.filter-btn');
  const painelPersonalizado = document.getElementById('filtro-personalizado');
  const btnAplicar = document.getElementById('aplicar-filtro');
  const tbody = document.querySelector('.task-table tbody');
  const contador = document.getElementById('qtd-tarefas');
  let tarefaParaExcluir = null;

  // Filtros
  buttons.forEach(button => {
    button.addEventListener('click', () => {
      buttons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');

      const filter = button.getAttribute('data-filter');
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (filter === 'personalizado') {
        painelPersonalizado.classList.remove('hidden');
      } else {
        painelPersonalizado.classList.add('hidden');
      }

      const rows = tbody.querySelectorAll('tr');
      rows.forEach(row => {
        const dateText = row.children[1].innerText;
        const taskDate = new Date(dateText.split('/').reverse().join('-'));
        taskDate.setHours(0, 0, 0, 0);

        if (filter === 'hoje') {
          row.style.display = (taskDate.getTime() === today.getTime()) ? '' : 'none';
        } else if (filter === 'todo') {
          row.style.display = '';
        } else if (filter === 'atrasadas') {
          row.style.display = (taskDate < today) ? '' : 'none';
        } else if (filter === 'personalizado') {
          row.style.display = '';
        }
      });

      aplicarStatusNasDatas();
      atualizarContadorTarefas();
    });
  });

  btnAplicar.addEventListener('click', () => {
    const inicio = document.getElementById('data-inicio').value;
    const fim = document.getElementById('data-fim').value;

    if (!inicio || !fim) {
      alert("Escolha as duas datas.");
      return;
    }

    const dataInicio = new Date(inicio);
    const dataFim = new Date(fim);
    dataInicio.setHours(0, 0, 0, 0);
    dataFim.setHours(0, 0, 0, 0);

    const rows = tbody.querySelectorAll('tr');
    rows.forEach(row => {
      const texto = row.children[1].innerText;
      const data = new Date(texto.split('/').reverse().join('-'));
      data.setHours(0, 0, 0, 0);

      row.style.display = (data >= dataInicio && data <= dataFim) ? '' : 'none';
    });

    aplicarStatusNasDatas();
    atualizarContadorTarefas();
  });

  function aplicarStatusNasDatas() {
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    const rows = tbody.querySelectorAll('tr');
    rows.forEach(row => {
      const td = row.children[1];
      const texto = td.innerText.trim();
      const data = new Date(texto.split('/').reverse().join('-'));
      data.setHours(0, 0, 0, 0);

      const diffDias = Math.floor((data - hoje) / (1000 * 60 * 60 * 24));
      let classe = '';

      if (diffDias < 0) classe = 'data-status-vermelha';
      else if (diffDias === 0) classe = 'data-status-laranja';
      else if (diffDias === 1) classe = 'data-status-azul';
      else classe = 'data-status-verde';

      td.innerHTML = `<span class="status-ball ${classe}"></span>${texto}`;
    });
  }

  function atualizarContadorTarefas() {
    const visiveis = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
    if (contador) {
      contador.innerText = visiveis.length;
    }
  }

  // Criação de tarefa dinâmica com lixeira
  function criarLinhaTarefa(tarefa) {
    const novaLinha = document.createElement('tr');
    novaLinha.innerHTML = `
      <td>${tarefa.descricao}</td>
      <td>${tarefa.data}</td>
      <td class="prioridade"><span class="prioridade-tag ${tarefa.prioridade}">${tarefa.prioridadeTexto}</span></td>
      <td>${tarefa.cliente}</td>
      <td>${tarefa.obs}</td>
      <td>${tarefa.responsaveis}</td>
      <td><i class="fas fa-trash delete-icon"></i></td>
    `;

    novaLinha.querySelector('.delete-icon').addEventListener('click', () => {
      tarefaParaExcluir = novaLinha;
      document.getElementById('confirmar-exclusao').classList.remove('hidden');
    });

    tbody.appendChild(novaLinha);
  }

  // Modal abrir
  document.querySelector('.add-btn').addEventListener('click', () => {
    document.getElementById('modal').classList.remove('hidden');
  });

  document.getElementById('cancelar').addEventListener('click', () => {
    document.getElementById('modal').classList.add('hidden');
  });

  document.querySelector('.close-modal').addEventListener('click', () => {
    document.getElementById('modal').classList.add('hidden');
  });

  document.getElementById('task-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const tarefa = {
      descricao: document.getElementById('descricao').value,
      data: document.getElementById('data').value.split('-').reverse().join('/'),
      prioridade: document.getElementById('prioridade').value,
      cliente: document.getElementById('cliente').value,
      obs: document.getElementById('obs').value,
      responsaveis: document.getElementById('responsaveis').value,
    };

    tarefa.prioridadeTexto = tarefa.prioridade === 'urgente' ? 'Alta' :
                             tarefa.prioridade === 'media' ? 'Média' : 'Baixa';

    criarLinhaTarefa(tarefa);
    document.getElementById('modal').classList.add('hidden');
    this.reset();

    aplicarStatusNasDatas();
    atualizarContadorTarefas();
  });

  // Modal de confirmação
  document.getElementById('cancelar-exclusao').addEventListener('click', () => {
    document.getElementById('confirmar-exclusao').classList.add('hidden');
    tarefaParaExcluir = null;
  });

  document.getElementById('confirmar-excluir').addEventListener('click', () => {
    if (tarefaParaExcluir) {
      tarefaParaExcluir.remove();
      tarefaParaExcluir = null;
      atualizarContadorTarefas();
    }
    document.getElementById('confirmar-exclusao').classList.add('hidden');
  });

  // Carrega tarefas do localStorage
  const tarefas = JSON.parse(localStorage.getItem("tarefas")) || [];
  tarefas.forEach(tarefa => {
    tarefa.prioridadeTexto = tarefa.prioridade === 'urgente' ? 'Alta' :
                              tarefa.prioridade === 'media' ? 'Média' : 'Baixa';
    criarLinhaTarefa(tarefa);
  });

  aplicarStatusNasDatas();
  atualizarContadorTarefas();
});
