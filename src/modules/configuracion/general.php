<?php
// modules/configuracion/general.php
declare(strict_types=1);
session_start();

$page_title = "Configuración del Sistema";

require_once __DIR__ . '/../../admin/header.php';
require_once __DIR__ . '/../../admin/navbar.php';
require_once __DIR__ . '/../../admin/sidebar.php';

// Variables de sesión para mostrar mensajes
$success_msg = $_SESSION['config_success'] ?? '';
$error_msg = $_SESSION['config_error'] ?? '';
unset($_SESSION['config_success'], $_SESSION['config_error']);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-cogs mr-2"></i>Configuración</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/admin/index.php">Inicio</a></li>
            <li class="breadcrumb-item active">Configuración</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <i class="fas fa-check-circle mr-1"></i> <?php echo htmlspecialchars($success_msg); ?>
        </div>
      <?php endif; ?>

      <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <i class="fas fa-exclamation-triangle mr-1"></i> <?php echo htmlspecialchars($error_msg); ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <!-- Perfil de Usuario -->
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-user-cog mr-1"></i> Mi Perfil</h3>
            </div>
            <div class="card-body">
              <div class="text-center mb-3">
                <img src="<?php echo normalize_avatar_url($_SESSION['ruta_foto'] ?? null, $_SESSION['google_picture'] ?? null); ?>" 
                     class="img-circle elevation-2" 
                     alt="Avatar" 
                     style="width: 100px; height: 100px; object-fit: cover;">
              </div>
              <table class="table table-sm">
                <tr>
                  <th style="width: 140px;">Usuario:</th>
                  <td><?php echo htmlspecialchars(current_username()); ?></td>
                </tr>
                <tr>
                  <th>Email:</th>
                  <td><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'No disponible'); ?></td>
                </tr>
                <tr>
                  <th>Tipo de cuenta:</th>
                  <td>
                    <span class="badge badge-<?php echo ($_SESSION['auth_type'] ?? '') === 'google' ? 'success' : 'info'; ?>">
                      <?php 
                        $authType = $_SESSION['auth_type'] ?? 'local';
                        echo $authType === 'google' ? 'Google' : ($authType === 'local_mongo' ? 'MongoDB' : 'Local'); 
                      ?>
                    </span>
                  </td>
                </tr>
              </table>
              <a href="/admin/profile.php" class="btn btn-primary btn-block">
                <i class="fas fa-eye mr-1"></i> Ver perfil completo
              </a>
            </div>
          </div>
        </div>

        <!-- Cambiar Contraseña (solo para usuarios locales) -->
        <?php if (($_SESSION['auth_type'] ?? '') === 'local_mongo'): ?>
        <div class="col-md-6">
          <div class="card card-warning">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-key mr-1"></i> Cambiar Contraseña</h3>
            </div>
            <form action="acciones/cambiar_password.php" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Contraseña Actual</label>
                  <input type="password" name="password_actual" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Nueva Contraseña</label>
                  <input type="password" name="password_nueva" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                  <label>Confirmar Nueva Contraseña</label>
                  <input type="password" name="password_confirmar" class="form-control" required minlength="6">
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                  <i class="fas fa-save mr-1"></i> Cambiar Contraseña
                </button>
              </div>
            </form>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Información del Sistema -->
      <div class="row">
        <div class="col-md-4">
          <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-database"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Base de Datos</span>
              <span class="info-box-number">MySQL</span>
              <small>Conexión activa</small>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fab fa-php"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">PHP Version</span>
              <span class="info-box-number"><?php echo phpversion(); ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-server"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Servidor</span>
              <span class="info-box-number">Apache</span>
              <small><?php echo php_uname('s'); ?></small>
            </div>
          </div>
        </div>
      </div>

      <!-- Acciones del Sistema -->
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-tools mr-1"></i> Herramientas del Sistema</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="card card-outline card-primary">
                <div class="card-body text-center">
                  <i class="fas fa-file-download fa-3x text-primary mb-3"></i>
                  <h5>Backup de Datos</h5>
                  <p class="text-muted">Descargar copia de seguridad de la base de datos</p>
                  <button class="btn btn-primary btn-sm" onclick="alert('Función de backup próximamente disponible')">
                    <i class="fas fa-download mr-1"></i> Descargar Backup
                  </button>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card card-outline card-success">
                <div class="card-body text-center">
                  <i class="fas fa-broom fa-3x text-success mb-3"></i>
                  <h5>Limpiar Caché</h5>
                  <p class="text-muted">Limpiar archivos temporales del sistema</p>
                  <form action="acciones/limpiar_cache.php" method="POST" style="display:inline;">
                    <button type="submit" class="btn btn-success btn-sm">
                      <i class="fas fa-sync mr-1"></i> Limpiar Caché
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card card-outline card-info">
                <div class="card-body text-center">
                  <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                  <h5>Estadísticas</h5>
                  <p class="text-muted">Ver estadísticas detalladas del sistema</p>
                  <a href="/modules/reportes/ventas.php" class="btn btn-info btn-sm">
                    <i class="fas fa-eye mr-1"></i> Ver Reportes
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Información de Sesión -->
      <div class="card card-dark collapsed-card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Información de Sesión (Debug)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <pre class="bg-dark p-3 text-light" style="font-size: 0.85rem; max-height: 300px; overflow-y: auto;"><?php print_r($_SESSION); ?></pre>
        </div>
      </div>

    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../../admin/footer.php'; ?>
