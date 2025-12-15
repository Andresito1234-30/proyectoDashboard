document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('login-form');
  const loginAlert = document.getElementById('login-alert');

  if (!loginForm) return;

  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    loginAlert.innerHTML = '';

    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    if (!email || !password) {
      loginAlert.innerHTML = '<p style="color:red;">Correo y contraseña requeridos</p>';
      return;
    }

    try {
      const res = await fetch('/api/auth_login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
      });

      const data = await res.json();

      if (!res.ok || !data.ok) {
        throw new Error(data.error || 'Error al iniciar sesión');
      }

      // 🔑 AQUÍ YA EXISTE $_SESSION EN PHP
      window.location.href = '/admin/index.php';

    } catch (err) {
      loginAlert.innerHTML = `<p style="color:red;">${err.message}</p>`;
    }
  });
});
