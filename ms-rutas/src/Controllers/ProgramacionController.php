<?php

namespace App\Controllers;

use App\Models\ProgramacionViaje;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProgramacionController
{
    public function index(Request $request, Response $response): Response
    {
        $programaciones = ProgramacionViaje::all();
        $response->getBody()->write(json_encode(['success' => true, 'data' => $programaciones, 'total' => count($programaciones)]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $prog = ProgramacionViaje::find($args['id']);
        if (!$prog) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Programación no encontrada']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['success' => true, 'data' => $prog]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true);

        if (empty($body['ruta_id']) || empty($body['conductor_id']) || empty($body['vehiculo_id']) || empty($body['fecha_salida'])) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'ruta_id, conductor_id, vehiculo_id y fecha_salida son requeridos']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $prog = ProgramacionViaje::create([
            'ruta_id'               => $body['ruta_id'],
            'conductor_id'          => $body['conductor_id'],
            'vehiculo_id'           => $body['vehiculo_id'],
            'fecha_salida'          => $body['fecha_salida'],
            'fecha_llegada_estimada'=> $body['fecha_llegada_estimada'] ?? null,
            'estado'                => $body['estado'] ?? 'programado',
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Viaje programado', 'data' => $prog]));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $prog = ProgramacionViaje::find($args['id']);
        if (!$prog) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Programación no encontrada']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $body = json_decode($request->getBody()->getContents(), true);
        $prog->update([
            'ruta_id'                => $body['ruta_id']                ?? $prog->ruta_id,
            'conductor_id'           => $body['conductor_id']           ?? $prog->conductor_id,
            'vehiculo_id'            => $body['vehiculo_id']            ?? $prog->vehiculo_id,
            'fecha_salida'           => $body['fecha_salida']           ?? $prog->fecha_salida,
            'fecha_llegada_estimada' => $body['fecha_llegada_estimada'] ?? $prog->fecha_llegada_estimada,
            'estado'                 => $body['estado']                 ?? $prog->estado,
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Programación actualizada', 'data' => $prog->fresh()]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $prog = ProgramacionViaje::find($args['id']);
        if (!$prog) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Programación no encontrada']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $prog->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Programación eliminada']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}