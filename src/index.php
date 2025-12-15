<?php
// /src/index.php
require __DIR__ . '/conexionpdo.php';
session_start();

// (Opcional) depuración
// ini_set('display_errors', 1); error_reporting(E_ALL);

// Mensajes mostrados en la UI (se usarán para errores devueltos por los proxies)
$mensaje_login = '';
$mensaje_reg   = '';

// Detectar si la sesión expiró
if (isset($_GET['session_expired']) && $_GET['session_expired'] == '1') {
  $mensaje_login = '⏳ Tu sesión ha expirado por seguridad. Por favor inicia sesión nuevamente.';
}

// SI YA ESTÁ LOGUEADO, ENTRA AL DASHBOARD
if (!empty($_SESSION['user_id'])) {
  header('Location: /admin/index.php');
  exit;
}

// Tu Client ID (GIS). No uses el client secret en el front.
$GOOGLE_CLIENT_ID = '43219663738-4a1q985vnjmfr87back9i52v7fjbk2lt.apps.googleusercontent.com';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign In/Up Form</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="/assets/img/favicon.png">

  <!-- CSS de esta pantalla -->
  <link rel="stylesheet" href="../assets/css/login_registrarse.css"/>

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />

  <!-- Google Identity Services (GIS) -->
  <script src="https://accounts.google.com/gsi/client" async defer></script>

  <style>
    .divider { margin: 12px 0; font-size: 12px; color: #64748b; }
    .google-btn-wrap { display:flex; justify-content:center; margin-top:6px; }
    .input-group { position: relative; margin-bottom: 10px; }
    .input-group i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #888; }
    .input-group input { padding-left: 38px; width: 100%; }
    .input-group input[type="file"] { padding-left: 10px; }
  </style>
</head>
<body>
  <div><h1>¡¡¡Bienvenido!!!</h1></div>

  <div class="container <?= ($mensaje_reg) ? 'right-panel-active' : '' ?>" id="container">
    <!-- REGISTRO -->
    <div class="form-container sign-up-container">
      <form id="register-form" enctype="multipart/form-data" novalidate>
        <h2>Crear una cuenta</h2>
        <div id="register-alert"></div>
        <?php if ($mensaje_reg): ?>
          <p style="color:#ff1744; font-weight:bold;"><?= htmlspecialchars($mensaje_reg) ?></p>
        <?php else: ?>
          <span>Crea tu cuenta para acceder</span>
        <?php endif; ?>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="firstname" placeholder="Nombre" required />
        </div>
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="lastname" placeholder="Apellido" required />
        </div>
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="Correo electrónico" required />
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Contraseña" required />
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="confirmPassword" placeholder="Confirmar contraseña" required />
        </div>
        <button type="submit">Sign Up</button>
      </form>
    </div>

    <!-- LOGIN -->
    <div class="form-container sign-in-container">
      <form id="login-form">
        <h2>Iniciar sesión</h2>
        <div id="login-alert"></div>
        <?php if ($mensaje_login): ?>
          <p style="color:#ff1744; font-weight:bold;"><?= htmlspecialchars($mensaje_login) ?></p>
        <?php else: ?>
          <span>Use su cuenta ya registrada</span>
        <?php endif; ?>
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" id="login-email" placeholder="Correo electrónico" required />
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" id="login-password" placeholder="Contraseña" required />
        </div>
        <button type="submit">Sign In</button>

        <!-- Separador + Botón de Google (GIS) -->
        <div class="divider">— o —</div>

        <!-- Config GIS onload -->
        <div id="g_id_onload"
             data-client_id="<?= htmlspecialchars($GOOGLE_CLIENT_ID) ?>"
             data-context="signin"
             data-ux_mode="popup"
             data-callback="handleCredentialResponse"
             data-auto_prompt="false">
        </div>

        <!-- Botón “Sign in with Google” -->
        <div class="google-btn-wrap">
          <div class="g_id_signin"
               data-type="standard"
               data-shape="rectangular"
               data-theme="outline"
               data-text="signin_with"
               data-size="large"
               data-logo_alignment="left">
          </div>
        </div>
      </form>
    </div>

    

    <!-- OVERLAY -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Bienvenido de nuevo!</h1>
          <p>Si ya tiene una cuenta registrada, inicie sesión con su cuenta</p>
          <button class="ghost" id="signIn" type="button">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hola, Amigo!</h1>
          <p>Ingrese sus datos para ser registrado de manera correcta</p>
          <button class="ghost" id="signUp" type="button">Sign Up</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/login_registrarse.js" defer></script>

  <!-- Auth handler using modular services/api.js -->
  <script type="module" src="../assets/js/auth_handler.js"></script>

  <!-- Callback GIS: tras ok => redirige a /admin/index.php -->
  <script>
    async function handleCredentialResponse(response) {
      const id_token = response.credential;
      if (!id_token) {
        alert('No se recibió el ID token de Google.');
        return;
      }
      try {
        const r = await fetch('google_login.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: 'id_token=' + encodeURIComponent(id_token)
        });

        const raw = await r.text();
        let data;
        try {
          data = JSON.parse(raw);
        } catch (e) {
          console.error('Respuesta no-JSON del servidor:', raw);
          alert('El servidor respondió algo inesperado (no JSON). Revisa la consola y el log de PHP.');
          return;
        }

        if (!r.ok) {
          alert(data.error || ('Error HTTP ' + r.status));
          return;
        }

        if (data.ok) {
          window.location.href = '/admin/index.php';
        } else {
          alert(data.error || 'No se pudo iniciar sesión con Google.');
        }
      } catch (err) {
        console.error(err);
        alert('Error de red al comunicar con el servidor.');
      }
    }
  </script>
</body>
</html>
