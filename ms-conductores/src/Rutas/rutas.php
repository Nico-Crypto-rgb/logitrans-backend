<?php

use App\Controllers\ConductorController;
use App\Middleware\AuthMiddleware;

// Agregar esta ruta para el Health Check
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status' => 'ok', 
        'service' => 'ms-conductores',
        'message' => 'Servicio operativo'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Tus rutas de conductores
$app->group('/conductores', function ($group) {
    $group->get('', [ConductorController::class, 'index']);
    $group->get('/{id}', [ConductorController::class, 'show']);
    $group->post('', [ConductorController::class, 'store']);
    $group->put('/{id}', [ConductorController::class, 'update']);
    $group->delete('/{id}', [ConductorController::class, 'destroy']);
    $group->patch('/{id}/estado', [ConductorController::class, 'cambiarEstado']);
})->add(new AuthMiddleware());