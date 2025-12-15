<?php
// admin/header.php
$page_title = $page_title ?? "AdminLTE 4 | Dashboard";

// Asegura sesión iniciada antes de cualquier salida
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// ===== CONTROL DE EXPIRACIÓN DE SESIÓN =====
// CONFIGURABLE: Tiempo de expiración en segundos (60 = 1 minuto)
define('SESSION_TIMEOUT', 120); // 👈 CAMBIA ESTE VALOR: 60=1min, 300=5min, 1800=30min, 3600=1hora

if (isset($_SESSION['user_id'])) {
  // Verificar si existe el timestamp de última actividad
  if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    
    // Si ha pasado el tiempo de expiración
    if ($inactive_time > SESSION_TIMEOUT) {
      // Guardar mensaje de expiración
      $_SESSION = array(); // Limpiar sesión
      session_destroy();
      
      // Redirigir a login con mensaje
      header('Location: /index.php?session_expired=1');
      exit;
    }
  }
  
  // Actualizar timestamp de última actividad
  $_SESSION['last_activity'] = time();
}

/**
 * Devuelve la URL del avatar a mostrar:
 * 1) Foto de Google si existe
 * 2) Foto local (ruta_foto) en /uploads
 * 3) Avatar por defecto
 */
if (!function_exists('normalize_avatar_url')) {
  function normalize_avatar_url(?string $rutaFotoLocal, ?string $googlePicture): string {
    // 1) Foto de Google (URL remota)
    if (!empty($googlePicture)) {
      return $googlePicture;
    }

    // 2) ruta_foto puede ser local (p.ej. "/uploads/xyz.jpg") o una URL externa
    if (!empty($rutaFotoLocal)) {
      $src = trim($rutaFotoLocal);

      // Si es URL absoluta, devolver tal cual
      if (preg_match('#^https?://#i', $src)) {
        return $src;
      }

      // Normalizar como ruta web absoluta comenzando por '/'
      if ($src[0] !== '/') {
        $src = '/' . ltrim($src, '/');
      }

      // Verificar existencia del archivo en el servidor y devolver fallback si no existe
      $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
      $absPath = $docRoot . $src;
      if ($docRoot !== '' && is_file($absPath)) {
        return $src; // p.ej. /uploads/archivo.webp
      }

      // Si no existe, volver al avatar por defecto
      return '/assets/img/user-avatar.png';
    }

    // 3) fallback por defecto
    return '/assets/img/user-avatar.png';
  }
}

if (!function_exists('current_username')) {
  function current_username(): string {

    // MongoDB login
    if (
      ($_SESSION['auth_type'] ?? '') === 'local_mongo'
      && (!empty($_SESSION['firstname']) || !empty($_SESSION['lastname']))
    ) {
      return trim(
        ($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? '')
      );
    }

    // Google login
    if (!empty($_SESSION['google_name'])) {
      return $_SESSION['google_name'];
    }

    // Fallbacks
    return $_SESSION['username']
      ?? $_SESSION['user_email']
      ?? 'Usuario';
  }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($page_title); ?></title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="/assets/img/favicon.png">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <!-- Custom CSS (ruta absoluta, válida en /admin y /modules/...) -->
  <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
