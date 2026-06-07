<?php

namespace App\Controllers;

use App\Models\Novedad;
use App\Models\Viaje;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NovedadController
{
    public function index(Request $request, Response $response, array $args): Response
    {
        $viaje = Viaje::find($args['viaje_id']);

        if (!$viaje) {
            $response->getBody()->write(json_encode(['error' => 'Viaje no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $novedades = Novedad::where('viaje_id', $args['viaje_id'])->get();
        $response->getBody()->write(json_encode($novedades));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $novedad = Novedad::find($args['id']);

        if (!$novedad) {
            $response->getBody()->write(json_encode(['error' => 'Novedad no encontrada']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($novedad));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $user = $request->getAttribute('user');

        if (empty($data['viaje_id']) || empty($data['tipo']) || empty($data['descripcion'])) {
            $response->getBody()->write(json_encode(['error' => 'viaje_id, tipo y descripcion son requeridos']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $novedad = Novedad::create([
            'viaje_id'    => $data['viaje_id'],
            'tipo'        => $data['tipo'],
            'descripcion' => $data['descripcion'],
            'usuario_id'  => $data['usuario_id'] ?? ($user['id'] ?? 1),
        ]);

        $response->getBody()->write(json_encode($novedad));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $novedad = Novedad::find($args['id']);

        if (!$novedad) {
            $response->getBody()->write(json_encode(['error' => 'Novedad no encontrada']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $novedad->delete();
        $response->getBody()->write(json_encode(['mensaje' => 'Novedad eliminada correctamente']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}