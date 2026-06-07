<?php

namespace App\Controllers;

use App\Models\Viaje;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ViajeController
{
    public function index(Request $request, Response $response): Response
    {
        $viajes = Viaje::with('novedades')->get();
        $response->getBody()->write(json_encode($viajes));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $viaje = Viaje::with('novedades')->find($args['id']);

        if (!$viaje) {
            $response->getBody()->write(json_encode(['error' => 'Viaje no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($viaje));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (empty($data['programacion_id'])) {
            $response->getBody()->write(json_encode(['error' => 'programacion_id es requerido']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $viaje = Viaje::create([
            'programacion_id'  => $data['programacion_id'],
            'estado'           => $data['estado']           ?? 'en_transito',
            'ubicacion_actual' => $data['ubicacion_actual'] ?? null,
            'observaciones'    => $data['observaciones']    ?? null,
            'fecha_inicio'     => $data['fecha_inicio']     ?? null,
            'fecha_fin'        => $data['fecha_fin']        ?? null,
        ]);

        $response->getBody()->write(json_encode($viaje));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $viaje = Viaje::find($args['id']);

        if (!$viaje) {
            $response->getBody()->write(json_encode(['error' => 'Viaje no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $data = $request->getParsedBody();

        $viaje->update([
            'programacion_id'  => $data['programacion_id']  ?? $viaje->programacion_id,
            'estado'           => $data['estado']           ?? $viaje->estado,
            'ubicacion_actual' => $data['ubicacion_actual'] ?? $viaje->ubicacion_actual,
            'observaciones'    => $data['observaciones']    ?? $viaje->observaciones,
            'fecha_inicio'     => $data['fecha_inicio']     ?? $viaje->fecha_inicio,
            'fecha_fin'        => $data['fecha_fin']        ?? $viaje->fecha_fin,
        ]);

        $response->getBody()->write(json_encode($viaje));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $viaje = Viaje::find($args['id']);

        if (!$viaje) {
            $response->getBody()->write(json_encode(['error' => 'Viaje no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $viaje->delete();
        $response->getBody()->write(json_encode(['mensaje' => 'Viaje eliminado correctamente']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}