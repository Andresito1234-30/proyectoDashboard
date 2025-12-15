<?php
// api/auth_register.php
// Receives registration request, forwards to MongoDB backend, attempts auto-login

declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

$AUTH_BACKEND_LOGIN = 'http://host.docker.internal:3977/api/v1/auth/login';
$AUTH_BACKEND_REGISTER = 'http://host.docker.internal:3977/api/v1/auth/register';

// Get JSON payload
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'JSON inválido']);
  exit;
}

$firstname = trim($data['firstname'] ?? '');
$lastname = trim($data['lastname'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validate required fields
if (empty($firstname)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'El nombre es obligatorio']);
  exit;
}
if (empty($lastname)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'El apellido es obligatorio']);
  exit;
}
if (empty($email)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'El correo electrónico es obligatorio']);
  exit;
}
if (empty($password)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'La contraseña es obligatoria']);
  exit;
}

// Prepare payload to backend register
$payload = [
  'firstname' => $firstname,
  'lastname' => $lastname,
  'email' => $email,
  'password' => $password
];

// Send to backend register
$ch = curl_init($AUTH_BACKEND_REGISTER);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($resp === false) {
  http_response_code(502);
  echo json_encode(['ok' => false, 'error' => 'No se pudo contactar al backend: ' . $err]);
  exit;
}

$json = json_decode($resp, true);

// Check for successful registration (2xx status)
if ($code < 200 || $code >= 300) {
  $e = is_array($json) ? ($json['error'] ?? $json['message'] ?? $json['msg'] ?? 'Error al registrar') : 'Error al registrar';
  http_response_code($code >= 400 ? $code : 400);
  echo json_encode(['ok' => false, 'error' => $e]);
  exit;
}

// Registration successful - attempt auto-login
$ch2 = curl_init($AUTH_BACKEND_LOGIN);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode(['email' => $email, 'password' => $password]));
curl_setopt($ch2, CURLOPT_TIMEOUT, 10);

$resp2 = curl_exec($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

if ($resp2 === false || $code2 < 200 || $code2 >= 300) {
  // Registration ok, but auto-login failed
  echo json_encode(['ok' => true, 'warning' => 'Registrado. Por favor inicia sesión manualmente.']);
  exit;
}

$json2 = json_decode($resp2, true);
$user = $json2['user'] ?? null;
$accessToken = $json2['access'] ?? $json2['access_token'] ?? $json2['token'] ?? null;

if (!$user || !$accessToken) {
  echo json_encode(['ok' => true, 'warning' => 'Registrado. Por favor inicia sesión manualmente.']);
  exit;
}

// Create PHP session
$userId = $user['_id'] ?? $user['id'] ?? null;
$_SESSION['user_id'] = $userId ? 'mongo_' . (string)$userId : ('mongo_' . bin2hex(random_bytes(6)));
$_SESSION['username'] = trim($firstname . ' ' . $lastname);
$_SESSION['firstname'] = $firstname;
$_SESSION['lastname'] = $lastname;
$_SESSION['user_email'] = $email;
$_SESSION['auth_type'] = 'local_mongo';
$_SESSION['access_token'] = $accessToken;
$_SESSION['ruta_foto'] = '/assets/img/user-avatar.png';
$_SESSION['google_picture'] = '';

// Save session before response
session_write_close();

// Return success with user data and token
echo json_encode([
  'ok' => true,
  'access' => $accessToken,
  'user' => $user
]);
exit;
