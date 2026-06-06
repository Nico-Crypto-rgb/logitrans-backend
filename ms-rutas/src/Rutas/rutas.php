<?php

use App\Controllers\RutaController;
use App\Controllers\ProgramacionController;
use App\Middleware\AuthMiddleware;

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'service' => 'ms-rutas',
        'status'  => 'running',
        'port'    => 8004
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Rutas
$app->group('/rutas', function ($group) {
    $group->get('',        [RutaController::class, 'index']);
    $group->get('/{id}',   [RutaController::class, 'show']);
    $group->post('',       [RutaController::class, 'store']);
    $group->put('/{id}',   [RutaController::class, 'update']);
    $group->delete('/{id}',[RutaController::class, 'destroy']);
})->add(new AuthMiddleware());

// Programación de viajes
$app->group('/programacion', function ($group) {
    $group->get('',        [ProgramacionController::class, 'index']);
    $group->get('/{id}',   [ProgramacionController::class, 'show']);
    $group->post('',       [ProgramacionController::class, 'store']);
    $group->put('/{id}',   [ProgramacionController::class, 'update']);
    $group->delete('/{id}',[ProgramacionController::class, 'destroy']);
})->add(new AuthMiddleware());