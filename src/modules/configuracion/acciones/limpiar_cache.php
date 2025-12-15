<?php
// modules/configuracion/acciones/limpiar_cache.php
declare(strict_types=1);
session_start();

// Simular limpieza de caché (puedes agregar lógica real aquí)
$cacheCleared = true;

if ($cacheCleared) {
    $_SESSION['config_success'] = 'Caché del sistema limpiado correctamente.';
} else {
    $_SESSION['config_error'] = 'Error al limpiar el caché.';
}

header('Location: ../general.php');
exit;
