<?php
// /src/admin/profile.php
declare(strict_types=1);
session_start();

// título de la página
$page_title = "Perfil de Usuario";

// --------- Resolver datos del usuario desde la sesión ----------
$firstname = $_SESSION['firstname'] ?? '';
$lastname  = $_SESSION['lastname'] ?? '';


$authType   = $_SESSION['auth_type']  ?? 'local';        // 'google' | 'local'
$username   = $_SESSION['username']   ?? 'Usuario';
$email      = $_SESSION['user_email'] ?? '';
$rutaFoto   = $_SESSION['ruta_foto']  ?? '';             // para cuentas locales
$gPicture   = $_SESSION['google_picture'] ?? '';         // para cuentas Google
$gSub       = $_SESSION['google_sub'] ?? '';             // id único Google
$gGiven     = $_SESSION['google_given_name'] ?? '';
$gFamily    = $_SESSION['google_family_name'] ?? '';

// Avatar: prioridad Google -> local -> default
$avatarUrl = '';
if ($authType === 'google' && $gPicture) {
  $avatarUrl = $gPicture; // URL absoluta de Google
} elseif ($rutaFoto) {
  // Normaliza a ruta absoluta web (/uploads/xxx) y verifica que exista
  $rel = (strpos($rutaFoto, '/') === 0) ? $rutaFoto : '/' . ltrim($rutaFoto, '/');
  $abs = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\') . $rel;
  $avatarUrl = (is_file($abs)) ? $rel : '/assets/img/user-avatar.png';
} else {
  $avatarUrl = '/assets/img/user-avatar.png';
}

// Nombre "bonito" basado en el tipo de autenticación
$fullName = $username;

// MongoDB
if ($authType === 'local_mongo' && ($firstname || $lastname)) {
  $fullName = trim($firstname . ' ' . $lastname);
}

// Google
if ($authType === 'google' && ($gGiven || $gFamily)) {
  $fullName = trim($gGiven . ' ' . $gFamily);
}


// ------------- Layout estándar AdminLTE -------------
include 'header.php';
include 'navbar.php';
include 'sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Perfil de Usuario</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active">Perfil</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-id-card-alt mr-1"></i> Detalles del Usuario</h3>
        </div>

        <div class="card-body">
          <div class="row">

            <!-- Columna Foto -->
            <div class="col-md-3 text-center">
              <img
                src="<?php echo htmlspecialchars($avatarUrl); ?>"
                alt="Foto de perfil"
                class="img-fluid img-circle elevation-2 mb-3"
                style="width: 180px; height: 180px; object-fit: cover;"
              >
              <div>
                <span class="badge badge-<?php echo ($authType === 'google' ? 'success' : 'info'); ?>">
                  <?php echo ($authType === 'google' ? 'Cuenta de Google' : 'Cuenta local'); ?>
                </span>
              </div>
            </div>

            <!-- Columna Datos -->
            <div class="col-md-9">
              <div class="table-responsive">
                <table class="table table-striped">
                  <tbody>
                    <tr>
                      <th style="width: 220px;">NOMBRE COMPLETO:</th>
                      <td><?php echo htmlspecialchars($fullName); ?></td>
                    </tr>

                    <tr>
                      <th>USUARIO (username):</th>
                      <td><?php echo htmlspecialchars($username); ?></td>
                    </tr>

                    <tr>
                      <th>E-MAIL:</th>
                      <td><?php echo $email ? htmlspecialchars($email) : '<span class="text-muted">—</span>'; ?></td>
                    </tr>

                    <tr>
                      <th>ÚLTIMO INICIO DE SESIÓN:</th>
                      <td><?php echo date('d/m/Y H:i'); ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="mt-3">
                <a href="../logout.php" class="btn btn-danger">
                  <i class="fas fa-sign-out-alt mr-1"></i> Cerrar sesión
                </a>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include 'footer.php'; ?>
