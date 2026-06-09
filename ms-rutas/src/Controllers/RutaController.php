<?php

namespace App\Controllers;

use App\Models\Ruta;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RutaController
{
    public function index(Request $request, Response $response): Response
    {
        $rutas = Ruta::all();
        $response->getBody()->write(json_encode(['success' => true, 'data' => $rutas, 'total' => count($rutas)]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $ruta = Ruta::find($args['id']);
        if (!$ruta) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Ruta no encontrada']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['success' => true, 'data' => $ruta]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        // Cambiado a getParsedBody()
        $body = $request->getParsedBody();
        $body = is_array($body) ? $body : (array)$body;

        if (empty($body['nombre']) || empty($body['origen']) || empty($body['destino'])) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'nombre, origen y destino son requeridos']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $ruta = Ruta::create([
            'nombre'                => $body['nombre'],
            'origen'                => $body['origen'],
            'destino'               => $body['destino'],
            'distancia_km'          => $body['distancia_km']          ?? null,
            'tiempo_estimado_horas' => $body['tiempo_estimado_horas'] ?? null,
            'activa'                => $body['activa'] ?? 1,
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Ruta creada', 'data' => $ruta]));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $ruta = Ruta::find($args['id']);
        if (!$ruta) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Ruta no encontrada']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        // Cambiado a getParsedBody()
        $body = $request->getParsedBody();
        $body = is_array($body) ? $body : (array)$body;

        $ruta->update([
            'nombre'                => $body['nombre']                ?? $ruta->nombre,
            'origen'                => $body['origen']                ?? $ruta->origen,
            'destino'               => $body['destino']               ?? $ruta->destino,
            'distancia_km'          => $body['distancia_km']          ?? $ruta->distancia_km,
            'tiempo_estimado_horas' => $body['tiempo_estimado_horas'] ?? $ruta->tiempo_estimado_horas,
            'activa'                => $body['activa']                ?? $ruta->activa,
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Ruta actualizada', 'data' => $ruta->fresh()]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $ruta = Ruta::find($args['id']);
        if (!$ruta) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Ruta no encontrada']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $ruta->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Ruta eliminada']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}