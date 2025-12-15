<?php
// debug_session.php - Verificar contenido de la sesión
session_start();
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
  'session_id' => session_id(),
  'session_data' => $_SESSION,
  'has_user_id' => isset($_SESSION['user_id']),
  'has_firstname' => isset($_SESSION['firstname']),
  'has_lastname' => isset($_SESSION['lastname']),
  'has_username' => isset($_SESSION['username']),
  'username_value' => $_SESSION['username'] ?? 'NOT SET',
  'firstname_value' => $_SESSION['firstname'] ?? 'NOT SET',
  'lastname_value' => $_SESSION['lastname'] ?? 'NOT SET',
  'auth_type' => $_SESSION['auth_type'] ?? 'NOT SET'
], JSON_PRETTY_PRINT);
