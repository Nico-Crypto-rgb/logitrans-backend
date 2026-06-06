<?php

use App\Controllers\VehiculoController;
use App\Middleware\AuthMiddleware;

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'service' => 'ms-vehiculos',
        'status'  => 'running',
        'port'    => 8003
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->group('/vehiculos', function ($group) {
    $group->get('',               [VehiculoController::class, 'index']);
    $group->get('/{id}',          [VehiculoController::class, 'show']);
    $group->post('',              [VehiculoController::class, 'store']);
    $group->put('/{id}',          [VehiculoController::class, 'update']);
    $group->delete('/{id}',       [VehiculoController::class, 'destroy']);
    $group->patch('/{id}/estado', [VehiculoController::class, 'cambiarEstado']);
})->add(new AuthMiddleware());