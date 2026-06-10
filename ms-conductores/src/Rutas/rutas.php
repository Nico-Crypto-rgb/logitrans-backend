<?php
use App\Controllers\ConductorController;
use App\Middleware\AuthMiddleware;

// Rutas protegidas para el microservicio de conductores
$app->group('/conductores', function ($group) {
    $group->get('', [ConductorController::class, 'index']);
    $group->get('/{id}', [ConductorController::class, 'show']);
    $group->post('', [ConductorController::class, 'store']);
    $group->put('/{id}', [ConductorController::class, 'update']);
    $group->delete('/{id}', [ConductorController::class, 'destroy']);
})->add(new AuthMiddleware());
