<?php
// admin/navbar.php
// No session_start aquí. Ya se hace en header.php
$avatar = normalize_avatar_url($_SESSION['ruta_foto'] ?? null, $_SESSION['google_picture'] ?? null);
$nombre = current_username();
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="/admin/index.php" class="nav-link">Inicio</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Soporte</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge">15</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">15 Notificaciones</span>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-envelope mr-2"></i> 4 nuevos mensajes
          <span class="float-right text-muted text-sm">3 mins</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-users mr-2"></i> 8 solicitudes de amistad
          <span class="float-right text-muted text-sm">12 horas</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
      </div>
    </li>

    <!-- User Account Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
        <img src="<?php echo htmlspecialchars($avatar); ?>" class="img-circle elevation-1 mr-2" style="width:24px;height:24px;object-fit:cover;" alt="Avatar">
        <?php echo htmlspecialchars($nombre); ?>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header text-center">
          <img src="<?php echo htmlspecialchars($avatar); ?>" class="img-circle elevation-2" alt="User Image" width="60">
          <p class="mt-2 mb-0">
            <?php echo htmlspecialchars($nombre); ?>
          </p>
          <small>Miembro desde <?php echo date('M. Y'); ?></small>
        </span>
        <div class="dropdown-divider"></div>
        <a href="/admin/profile.php" class="dropdown-item">
          <i class="fas fa-user mr-2"></i> Mi Perfil
        </a>
        <a href="/modules/configuracion/general.php" class="dropdown-item">
          <i class="fas fa-cog mr-2"></i> Configuración
        </a>
        <div class="dropdown-divider"></div>
        <a href="/logout.php" class="dropdown-item text-danger">
          <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
        </a>
      </div>
    </li>

    <!-- Fullscreen Toggle -->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
