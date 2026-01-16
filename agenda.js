// ========================================
// SISTEMA DE AGENDA PROFISSIONAL - TMIS
// ========================================

const API_URL_AGENDA = 'http://localhost:3000/api/agenda';
let calendar;
let eventos = [];
let editandoEventoId = null;

// ========================================
// INICIALIZAÇÃO
// ========================================

document.addEventListener('DOMContentLoaded', function() {
  inicializarCalendario();
  buscarEventos();
});

function inicializarCalendario() {
  const calendarEl = document.getElementById('calendario');
  
  calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'pt-br',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: {
      today: 'Hoje',
      month: 'Mês',
      week: 'Semana',
      day: 'Dia'
    },
    events: function(info, successCallback, failureCallback) {
      successCallback(eventos.map(e => ({
        id: e.id,
        title: e.titulo,
        start: e.hora ? `${e.data}T${e.hora}` : e.data,
        className: `prioridade-${e.prioridade}`, // Usa classes CSS para cores pastéis
        extendedProps: e
      })));
    },
    eventClick: function(info) {
      editarEvento(info.event.extendedProps.id);
    },
    dateClick: function(info) {
      abrirModalAgenda(info.dateStr);
    },
    editable: true,
    eventDrop: async function(info) {
      const evento = info.event.extendedProps;
      const novaData = info.event.startStr.split('T')[0];
      const novaHora = info.event.startStr.split('T')[1] ? info.event.startStr.split('T')[1].substring(0, 5) : evento.hora;
      
      await atualizarDataEvento(evento.id, novaData, novaHora);
    }
  });
  
  calendar.render();
}

// ========================================
// FUNÇÕES DE COMUNICAÇÃO COM A API
// ========================================

async function buscarEventos() {
  try {
    // Por enquanto usa localStorage se a API falhar, mas tenta a API primeiro
    try {
      const response = await fetch(API_URL_AGENDA);
      if (response.ok) {
        eventos = await response.json();
      } else {
        carregarDoLocalStorage();
      }
    } catch (e) {
      carregarDoLocalStorage();
    }
    
    calendar.refetchEvents();
  } catch (error) {
    console.error('Erro ao buscar eventos:', error);
  }
}

async function salvarEvento(event) {
  event.preventDefault();
  
  const eventoData = {
    titulo: document.getElementById('titulo-evento').value,
    data: document.getElementById('data-evento').value,
    hora: document.getElementById('hora-evento').value,
    prioridade: document.getElementById('prioridade-evento').value,
    categoria: document.getElementById('categoria-evento').value,
    cliente: document.getElementById('cliente-evento').value,
    descricao: document.getElementById('descricao-evento').value
  };
  
  try {
    let response;
    if (editandoEventoId) {
      response = await fetch(`${API_URL_AGENDA}/${editandoEventoId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(eventoData)
      });
    } else {
      response = await fetch(API_URL_AGENDA, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(eventoData)
      });
    }

    // Se a API não estiver rodando, salva no localStorage para teste
    if (!response || !response.ok) {
      salvarNoLocalStorage(eventoData);
    }

    mostrarNotificacao('Compromisso salvo com sucesso!', 'sucesso');
    fecharModalAgenda();
    buscarEventos();
  } catch (error) {
    // Fallback para localStorage em caso de erro de conexão
    salvarNoLocalStorage(eventoData);
    mostrarNotificacao('Salvo localmente (Servidor offline)', 'sucesso');
    fecharModalAgenda();
    buscarEventos();
  }
}

async function atualizarDataEvento(id, novaData, novaHora) {
  try {
    await fetch(`${API_URL_AGENDA}/${id}/data`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ data: novaData, hora: novaHora })
    });
    buscarEventos();
  } catch (e) {
    // Atualiza no localStorage se falhar
    const idx = eventos.findIndex(ev => ev.id == id);
    if (idx !== -1) {
      eventos[idx].data = novaData;
      eventos[idx].hora = novaHora;
      localStorage.setItem('agenda_eventos', JSON.stringify(eventos));
      buscarEventos();
    }
  }
}

// ========================================
// FUNÇÕES DO MODAL
// ========================================

function abrirModalAgenda(data = '') {
  editandoEventoId = null;
  document.getElementById('modal-titulo-agenda').textContent = 'Novo Compromisso';
  document.getElementById('form-agenda').reset();
  if (data) document.getElementById('data-evento').value = data;
  document.getElementById('modal-agenda').style.display = 'block';
}

function fecharModalAgenda() {
  document.getElementById('modal-agenda').style.display = 'none';
  editandoEventoId = null;
}

function editarEvento(id) {
  editandoEventoId = id;
  const ev = eventos.find(e => e.id == id);
  
  if (ev) {
    document.getElementById('modal-titulo-agenda').textContent = 'Editar Compromisso';
    document.getElementById('titulo-evento').value = ev.titulo;
    document.getElementById('data-evento').value = ev.data;
    document.getElementById('hora-evento').value = ev.hora || '';
    document.getElementById('prioridade-evento').value = ev.prioridade;
    document.getElementById('categoria-evento').value = ev.categoria;
    document.getElementById('cliente-evento').value = ev.cliente || '';
    document.getElementById('descricao-evento').value = ev.descricao || '';
    
    document.getElementById('modal-agenda').style.display = 'block';
  }
}

// ========================================
// RENDERIZAÇÃO E AUXILIARES
// ========================================

// Função de barra lateral removida para design mais limpo

function getCorPrioridade(p) {
  switch(p) {
    case 'baixa': return '#28a745';
    case 'media': return '#0078d4';
    case 'alta': return '#ffc107';
    case 'urgente': return '#dc3545';
    default: return '#0078d4';
  }
}

// Funções de Fallback (LocalStorage)
function carregarDoLocalStorage() {
  const storage = localStorage.getItem('agenda_eventos');
  if (storage) eventos = JSON.parse(storage);
}

function salvarNoLocalStorage(dados) {
  if (editandoEventoId) {
    const idx = eventos.findIndex(e => e.id == editandoEventoId);
    eventos[idx] = { ...dados, id: editandoEventoId };
  } else {
    eventos.push({ ...dados, id: Date.now() });
  }
  localStorage.setItem('agenda_eventos', JSON.stringify(eventos));
}

function mostrarNotificacao(msg, tipo) {
  const n = document.createElement('div');
  n.className = `notificacao notificacao-${tipo}`;
  n.style.position = 'fixed';
  n.style.top = '20px';
  n.style.right = '20px';
  n.style.zIndex = '9999';
  n.innerHTML = `<span>${msg}</span>`;
  document.body.appendChild(n);
  setTimeout(() => n.remove(), 3000);
}
