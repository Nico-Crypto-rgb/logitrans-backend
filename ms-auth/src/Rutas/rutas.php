<?php
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;

// Rutas públicas (sin token)
$app->post('/auth/login',   [AuthController::class, 'login']);
$app->get('/auth/validate', [AuthController::class, 'validate']);

// Rutas protegidas (requieren token)
$app->group('', function ($group) {
    $group->post('/auth/logout', [AuthController::class, 'logout']);
    $group->get('/usuarios',     [AuthController::class, 'index']);
    $group->post('/usuarios',    [AuthController::class, 'store']);
})->add(new AuthMiddleware());