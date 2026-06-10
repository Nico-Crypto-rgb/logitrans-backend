<?php

use App\Controllers\SeguimientoViajeController;
use App\Middleware\AuthMiddleware;

// Health check
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status' => 'ms-viajes activo',
        'puerto' => 8005
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// ── SEGUIMIENTOS DE VIAJE ────────────────────────────

// Listar todos los seguimientos
$app->get('/seguimientos', [SeguimientoViajeController::class, 'index'])->add(new AuthMiddleware());

// Crear un nuevo registro de seguimiento (estado o novedad)
$app->post('/seguimientos', [SeguimientoViajeController::class, 'store'])->add(new AuthMiddleware());

// Obtener un seguimiento específico por ID
$app->get('/seguimientos/{id}', [SeguimientoViajeController::class, 'show'])->add(new AuthMiddleware());

// Opcional: Si necesitas buscar todo el historial de un viaje específico
// (Este reemplaza la lógica de /viajes/{viaje_id}/novedades)
$app->get('/seguimientos/programacion/{programacion_viaje_id}', [SeguimientoViajeController::class, 'getByProgramacion'])->add(new AuthMiddleware());