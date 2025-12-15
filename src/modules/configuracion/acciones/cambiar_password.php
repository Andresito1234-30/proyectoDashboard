<?php
// modules/configuracion/acciones/cambiar_password.php
declare(strict_types=1);
session_start();

// Verificar que sea usuario MongoDB
if (($_SESSION['auth_type'] ?? '') !== 'local_mongo') {
    $_SESSION['config_error'] = 'Solo usuarios MongoDB pueden cambiar la contraseña desde aquí.';
    header('Location: ../general.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../general.php');
    exit;
}

$passwordActual = $_POST['password_actual'] ?? '';
$passwordNueva = $_POST['password_nueva'] ?? '';
$passwordConfirmar = $_POST['password_confirmar'] ?? '';

// Validaciones
if (empty($passwordActual) || empty($passwordNueva) || empty($passwordConfirmar)) {
    $_SESSION['config_error'] = 'Todos los campos son obligatorios.';
    header('Location: ../general.php');
    exit;
}

if ($passwordNueva !== $passwordConfirmar) {
    $_SESSION['config_error'] = 'Las contraseñas nuevas no coinciden.';
    header('Location: ../general.php');
    exit;
}

if (strlen($passwordNueva) < 6) {
    $_SESSION['config_error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
    header('Location: ../general.php');
    exit;
}

// Llamar al backend MongoDB para cambiar contraseña
$email = $_SESSION['user_email'] ?? '';
$accessToken = $_SESSION['access_token'] ?? '';

if (empty($email) || empty($accessToken)) {
    $_SESSION['config_error'] = 'Sesión inválida. Por favor inicia sesión nuevamente.';
    header('Location: ../general.php');
    exit;
}

// Hacer request al backend (ajusta la URL según tu backend)
$backendUrl = 'http://localhost:3977/api/v1/auth/change-password';

$ch = curl_init($backendUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => $email,
    'currentPassword' => $passwordActual,
    'newPassword' => $passwordNueva
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    $_SESSION['config_error'] = 'No se pudo conectar al servidor. Asegúrate de que el backend esté corriendo en el puerto 3977.';
    header('Location: ../general.php');
    exit;
}

$result = json_decode($response, true);

if ($httpCode === 200 && ($result['ok'] ?? false)) {
    $_SESSION['config_success'] = 'Contraseña cambiada exitosamente.';
} else {
    $errorMsg = $result['error'] ?? $result['message'] ?? 'Error al cambiar la contraseña.';
    $_SESSION['config_error'] = $errorMsg;
}

header('Location: ../general.php');
exit;
