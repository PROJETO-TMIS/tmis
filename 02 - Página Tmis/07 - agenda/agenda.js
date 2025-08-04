document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendario');
  const modal = document.getElementById('modal');
  const dataInput = document.getElementById('data');

  function formatarData(dateObj) {
    const dia = String(dateObj.getDate()).padStart(2, '0');
    const mes = String(dateObj.getMonth() + 1).padStart(2, '0');
    const ano = dateObj.getFullYear();
    return `${dia}/${mes}/${ano}`;
  }

  let tarefas = JSON.parse(localStorage.getItem("tarefas")) || [];

  const eventos = tarefas.map(tarefa => ({
    title: tarefa.descricao,
    start: tarefa.data.split('/').reverse().join('-'),
    allDay: true,
    extendedProps: {
      prioridade: tarefa.prioridade,
      cliente: tarefa.cliente,
      obs: tarefa.obs,
      responsaveis: tarefa.responsaveis
    }
  }));

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'pt-br',
    fixedWeekCount: false,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: eventos,

    dateClick: function (info) {
      document.getElementById('task-form').reset();
      dataInput.value = info.dateStr;
      delete modal.dataset.editando;
      modal.classList.remove('hidden');
    },

    eventClick: function (info) {
      const dataStr = formatarData(info.event.start);
      const tarefa = tarefas.find(t => t.descricao === info.event.title && t.data === dataStr);

      if (tarefa) {
        document.getElementById('descricao').value = tarefa.descricao;
        document.getElementById('data').value = tarefa.data.split('/').reverse().join('-');
        document.getElementById('prioridade').value = tarefa.prioridade;
        document.getElementById('cliente').value = tarefa.cliente;
        document.getElementById('obs').value = tarefa.obs;
        document.getElementById('responsaveis').value = tarefa.responsaveis;

        modal.dataset.editando = tarefa.data + tarefa.descricao;
        modal.classList.remove('hidden');
      }
    }
  });

  calendar.render();

  const addBtn = document.querySelector('.add-btn');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      document.getElementById('task-form').reset();
      delete modal.dataset.editando;
      modal.classList.remove('hidden');
    });
  }

  document.getElementById('cancelar').addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  document.querySelector('.close-modal').addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  document.getElementById('task-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const descricao = document.getElementById('descricao').value;
    const data = document.getElementById('data').value;
    const prioridade = document.getElementById('prioridade').value;
    const cliente = document.getElementById('cliente').value;
    const obs = document.getElementById('obs').value;
    const responsaveis = document.getElementById('responsaveis').value;

    const novaTarefa = {
      descricao,
      data: data.split('-').reverse().join('/'),
      prioridade,
      cliente,
      obs,
      responsaveis
    };

    if (modal.dataset.editando) {
      const id = modal.dataset.editando;
      const index = tarefas.findIndex(t => (t.data + t.descricao) === id);
      if (index !== -1) {
        tarefas.splice(index, 1);
        const oldEvent = calendar.getEvents().find(ev =>
          (formatarData(ev.start) + ev.title) === id
        );
        if (oldEvent) oldEvent.remove();
      }
      delete modal.dataset.editando;
    }

    tarefas.push(novaTarefa);
    localStorage.setItem("tarefas", JSON.stringify(tarefas));

    calendar.addEvent({
      title: novaTarefa.descricao,
      start: data,
      allDay: true,
      extendedProps: {
        prioridade: novaTarefa.prioridade,
        cliente: novaTarefa.cliente,
        obs: novaTarefa.obs,
        responsaveis: novaTarefa.responsaveis
      }
    });

    this.reset();
    modal.classList.add('hidden');
  });
});
