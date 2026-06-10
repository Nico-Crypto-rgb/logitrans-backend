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
        $body = $request->getParsedBody();
        $body = is_array($body) ? $body : (array)$body;

        // Validaciones contra las columnas reales de la tabla rutas
        if (empty($body['ciudad_origen']) || empty($body['ciudad_destino']) || empty($body['distancia']) || empty($body['tiempo_estimado'])) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'ciudad_origen, ciudad_destino, distancia y tiempo_estimado son requeridos']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $ruta = Ruta::create([
            'ciudad_origen'   => $body['ciudad_origen'],
            'ciudad_destino'  => $body['ciudad_destino'],
            'distancia'       => $body['distancia'],
            'tiempo_estimado' => $body['tiempo_estimado'],
            'observaciones'   => $body['observaciones'] ?? null,
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

        $body = $request->getParsedBody();
        $body = is_array($body) ? $body : (array)$body;

        $ruta->update([
            'ciudad_origen'   => $body['ciudad_origen']   ?? $ruta->ciudad_origen,
            'ciudad_destino'  => $body['ciudad_destino']  ?? $ruta->ciudad_destino,
            'distancia'       => $body['distancia']       ?? $ruta->distancia,
            'tiempo_estimado' => $body['tiempo_estimado'] ?? $ruta->tiempo_estimado,
            'observaciones'   => $body['observaciones']   ?? $ruta->observaciones,
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