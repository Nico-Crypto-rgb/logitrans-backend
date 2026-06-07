<?php

use App\Controllers\ViajeController;
use App\Controllers\NovedadController;
use App\Middleware\AuthMiddleware;

// Health check
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status' => 'ms-viajes activo',
        'puerto' => 8005
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// ── VIAJES ────────────────────────────────────────────
$app->get('/viajes',        [ViajeController::class, 'index'])  ->add(new AuthMiddleware());
$app->get('/viajes/{id}',   [ViajeController::class, 'show'])   ->add(new AuthMiddleware());
$app->post('/viajes',       [ViajeController::class, 'store'])  ->add(new AuthMiddleware());
$app->put('/viajes/{id}',   [ViajeController::class, 'update']) ->add(new AuthMiddleware());
$app->delete('/viajes/{id}',[ViajeController::class, 'destroy'])->add(new AuthMiddleware());

// ── NOVEDADES ─────────────────────────────────────────
$app->get('/viajes/{viaje_id}/novedades', [NovedadController::class, 'index']) ->add(new AuthMiddleware());
$app->post('/novedades',                  [NovedadController::class, 'store']) ->add(new AuthMiddleware());
$app->get('/novedades/{id}',              [NovedadController::class, 'show'])  ->add(new AuthMiddleware());
$app->delete('/novedades/{id}',           [NovedadController::class, 'destroy'])->add(new AuthMiddleware());