<?php
// api/auth_login.php
// Receives login request, forwards to MongoDB backend, creates PHP session

declare(strict_types=1);

// 🔴 FORZAR COOKIE DE SESIÓN GLOBAL
session_set_cookie_params([
  'path' => '/',        // ← CLAVE
  'httponly' => true,
  'samesite' => 'Lax'
]);


session_start();
header('Content-Type: application/json; charset=utf-8');

// URL fija del backend MongoDB (Node.js)
$AUTH_BACKEND_LOGIN = 'http://host.docker.internal:3977/api/v1/auth/login';

// Receive JSON payload
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'JSON inválido']);
  exit;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Email y contraseña requeridos']);
  exit;
}

// Forward login request to backend
$ch = curl_init($AUTH_BACKEND_LOGIN);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
  'email' => $email,
  'password' => $password
]));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

if ($resp === false) {
  http_response_code(502);
  echo json_encode(['ok' => false, 'error' => 'No se pudo contactar al backend: ' . $curlErr]);
  exit;
}

$json = json_decode($resp, true);

if (!is_array($json)) {
  http_response_code(502);
  echo json_encode(['ok' => false, 'error' => 'Respuesta inválida del backend']);
  exit;
}

// Backend returned error?
if ($code < 200 || $code >= 300) {
  http_response_code($code >= 400 ? $code : 401);
  $err = $json['error'] ?? $json['msg'] ?? $json['message'] ?? 'Credenciales inválidas';
  echo json_encode(['ok' => false, 'error' => $err]);
  exit;
}

// Extract user + tokens
$user = $json['user'] ?? null;
$accessToken = $json['access'] ?? $json['access_token'] ?? $json['token'] ?? null;

if (!$user || !$accessToken) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Respuesta incompleta del backend']);
  exit;
}

// Normalize user data
$userId    = $user['_id'] ?? $user['id'] ?? null;
$firstname = $user['firstname'] ?? '';
$lastname  = $user['lastname'] ?? '';
$userEmail = $user['email'] ?? $email;
$avatar    = $user['avatar'] ?? null;

// -------------------------------
// CREATE PHP SESSION
// -------------------------------
$_SESSION['auth_type']   = 'local_mongo';
$_SESSION['access_token'] = $accessToken;

// User ID
$_SESSION['user_id'] = $userId
  ? 'mongo_' . (string)$userId
  : 'mongo_' . bin2hex(random_bytes(6));

// Save user profile values
$_SESSION['firstname']  = $firstname;
$_SESSION['lastname']   = $lastname;
$_SESSION['user_email'] = $userEmail;

// Visible username in navbar
$_SESSION['username'] = trim($firstname . ' ' . $lastname);
if ($_SESSION['username'] === '') {
  $_SESSION['username'] = $userEmail; // fallback
}

// Avatar handling
if ($avatar && preg_match('#^https?://#i', $avatar)) {
  // External avatar (rare in your case)
  $_SESSION['google_picture'] = $avatar;
  $_SESSION['ruta_foto'] = '';
} else {
  // Local avatar or default
  $_SESSION['google_picture'] = '';
  $_SESSION['ruta_foto'] = $avatar ?: '/assets/img/user-avatar.png';
}

// -------------------------------
// SAVE SESSION AND RETURN RESPONSE
// -------------------------------
// Set last_activity for session timeout tracking
$_SESSION['last_activity'] = time();

session_write_close(); // Force session write before response

echo json_encode([
  'ok'     => true,
  'access' => $accessToken,
  'user'   => $user
]);
exit;
