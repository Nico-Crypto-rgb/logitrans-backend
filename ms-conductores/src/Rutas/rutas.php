<?php

use App\Controllers\ConductorController;
use App\Middleware\AuthMiddleware;

// Health check — sin autenticación
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'service' => 'ms-conductores',
        'status'  => 'running',
        'port'    => 8002
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Todas las rutas de conductores requieren token
$app->group('/conductores', function ($group) {
    $group->get('',                   [ConductorController::class, 'index']);
    $group->get('/{id}',              [ConductorController::class, 'show']);
    $group->post('',                  [ConductorController::class, 'store']);
    $group->put('/{id}',              [ConductorController::class, 'update']);
    $group->delete('/{id}',           [ConductorController::class, 'destroy']);
    $group->patch('/{id}/estado',     [ConductorController::class, 'cambiarEstado']);
})->add(new AuthMiddleware());