document.getElementById('loginForm').addEventListener('submit', function (e) {
  e.preventDefault(); // Impede o envio tradicional do form

  const nome = document.getElementById('nome').value;
  const email = document.getElementById('email').value;
  const senha = document.getElementById('senha').value;

  if (nome && email && senha) {
    // Se os campos estiverem preenchidos, redireciona
    window.location.href = 'home.html';
  } else {
    alert('Por favor, preencha todos os campos!');
  }
});