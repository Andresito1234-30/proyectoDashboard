<?php
// /src/google_login.php
declare(strict_types=1);
session_start();

// Fuerza JSON SIEMPRE y evita que warnings/HTML se mezclen con la respuesta
@ini_set('display_errors', '0');
@error_reporting(0);

if (!headers_sent()) {
  header('Content-Type: application/json; charset=utf-8');
}
// Vacía cualquier salida previa en buffers (BOM, avisos, etc.)
while (ob_get_level() > 0) { ob_end_clean(); }

/**
 * IMPORTANTE: Cambia a tu Client ID real si no coincide.
 * Debe terminar con ".apps.googleusercontent.com"
 */
$CLIENT_ID = '43219663738-4a1q985vnjmfr87back9i52v7fjbk2lt.apps.googleusercontent.com';

function send_json(array $payload, int $http = 200): void {
  if (!headers_sent()) {
    http_response_code($http);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  }
  while (ob_get_level() > 0) { ob_end_clean(); }
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

// ---- Autotest opcional ----
// Visita /src/google_login.php?selftest=1 para ver diagnóstico rápido del entorno PHP
if (isset($_GET['selftest'])) {
  $hasCurl  = function_exists('curl_init');
  $allowFgc = filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOL) ?: false;
  send_json([
    'ok'              => true,
    'selftest'        => true,
    'php_version'     => PHP_VERSION,
    'has_curl'        => $hasCurl,
    'allow_url_fopen' => $allowFgc,
    'client_id_set'   => ($CLIENT_ID !== '' && str_ends_with($CLIENT_ID, '.apps.googleusercontent.com')),
  ]);
}

// ---------- Flujo principal ----------
$idToken = $_POST['id_token'] ?? '';
if ($idToken === '') {
  send_json(['ok' => false, 'error' => 'Falta id_token'], 400);
}

$verifyUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);

// Llama a Google (usa cURL si existe; si no, file_get_contents si allow_url_fopen=On)
$response = false;
$httpCode = 0;

if (function_exists('curl_init')) {
  $ch = curl_init($verifyUrl);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
  ]);
  $response = curl_exec($ch);
  $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curlErr  = curl_error($ch);
  curl_close($ch);

  if ($response === false) {
    send_json(['ok' => false, 'error' => 'No se pudo contactar a Google (cURL): ' . $curlErr], 502);
  }
} else {
  $allowFgc = filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOL) ?: false;
  if (!$allowFgc) {
    send_json(['ok' => false, 'error' => 'Sin cURL y allow_url_fopen=Off. Activa uno de los dos.'], 500);
  }
  $ctx = stream_context_create([
    'http' => ['method' => 'GET', 'timeout' => 15],
    'ssl'  => ['verify_peer' => true, 'verify_peer_name' => true],
  ]);
  $response = @file_get_contents($verifyUrl, false, $ctx);
  if (isset($http_response_header) && is_array($http_response_header)) {
    foreach ($http_response_header as $h) {
      if (preg_match('#^HTTP/\d+\.\d+\s+(\d{3})#', $h, $m)) { $httpCode = (int)$m[1]; break; }
    }
  }
  if ($response === false) {
    send_json(['ok' => false, 'error' => 'No se pudo contactar a Google (file_get_contents).'], 502);
  }
}

if ($httpCode !== 200) {
  $errJson = json_decode((string)$response, true);
  $desc = $errJson['error_description'] ?? $errJson['error'] ?? ('HTTP ' . $httpCode);
  send_json(['ok' => false, 'error' => 'Google rechazó el token: ' . $desc], 401);
}

$data = json_decode((string)$response, true);
if (!is_array($data)) {
  send_json(['ok' => false, 'error' => 'Respuesta inválida de Google (no JSON).'], 502);
}

// Validaciones mínimas
if (($data['aud'] ?? '') !== $CLIENT_ID) {
  send_json(['ok' => false, 'error' => 'El token no pertenece a esta app (aud inválido).'], 401);
}
if (isset($data['exp']) && time() > (int)$data['exp']) {
  send_json(['ok' => false, 'error' => 'Token expirado.'], 401);
}

// Extrae datos útiles
$googleSub = $data['sub']     ?? '';
$name      = $data['name']    ?? '';
$email     = $data['email']   ?? null;
$picture   = $data['picture'] ?? null;

// Nombre de pantalla
$username = $name ?: ($email ? preg_replace('/@.*$/', '', $email) : 'UsuarioGoogle');

// Guarda sesión (NO escribe en BD; sólo sesión)
$_SESSION['user_id']      = 'google_' . ($googleSub ?: bin2hex(random_bytes(5)));
$_SESSION['username']     = $username;
$_SESSION['ruta_foto']    = '';         // se ignora si hay google_picture
$_SESSION['auth_type']    = 'google';
$_SESSION['user_email']   = $email;

// NUEVO: explícitos para navbar/sidebar (mostrar foto de Google)
$_SESSION['google_name']    = $name ?: $username;
$_SESSION['google_picture'] = $data['picture'] ?? null;
$_SESSION['google_sub'] = $googleSub;
$_SESSION['google_given_name'] = $data['given_name'] ?? '';
$_SESSION['google_family_name'] = $data['family_name'] ?? '';

// Éxito
send_json(['ok' => true], 200);
