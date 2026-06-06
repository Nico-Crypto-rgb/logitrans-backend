<?php

namespace App\Controllers;

use App\Models\Vehiculo;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class VehiculoController
{
    public function index(Request $request, Response $response): Response
    {
        $vehiculos = Vehiculo::all();
        $response->getBody()->write(json_encode([
            'success' => true,
            'data'    => $vehiculos,
            'total'   => count($vehiculos)
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $vehiculo = Vehiculo::find($args['id']);
        if (!$vehiculo) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Vehículo no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['success' => true, 'data' => $vehiculo]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true);

        if (empty($body['placa']) || empty($body['tipo']) || empty($body['capacidad_kg'])) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'placa, tipo y capacidad_kg son requeridos']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (Vehiculo::where('placa', $body['placa'])->exists()) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Ya existe un vehículo con esa placa']));
            return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
        }

        $vehiculo = Vehiculo::create([
            'placa'        => strtoupper($body['placa']),
            'tipo'         => $body['tipo'],
            'capacidad_kg' => $body['capacidad_kg'],
            'marca'        => $body['marca']  ?? null,
            'modelo'       => $body['modelo'] ?? null,
            'anio'         => $body['anio']   ?? null,
            'estado'       => $body['estado'] ?? 'disponible',
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Vehículo creado', 'data' => $vehiculo]));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $vehiculo = Vehiculo::find($args['id']);
        if (!$vehiculo) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Vehículo no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $body = json_decode($request->getBody()->getContents(), true);
        $vehiculo->update([
            'placa'        => isset($body['placa'])        ? strtoupper($body['placa']) : $vehiculo->placa,
            'tipo'         => $body['tipo']         ?? $vehiculo->tipo,
            'capacidad_kg' => $body['capacidad_kg'] ?? $vehiculo->capacidad_kg,
            'marca'        => $body['marca']        ?? $vehiculo->marca,
            'modelo'       => $body['modelo']       ?? $vehiculo->modelo,
            'anio'         => $body['anio']         ?? $vehiculo->anio,
            'estado'       => $body['estado']       ?? $vehiculo->estado,
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Vehículo actualizado', 'data' => $vehiculo->fresh()]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $vehiculo = Vehiculo::find($args['id']);
        if (!$vehiculo) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Vehículo no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $vehiculo->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Vehículo eliminado']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cambiarEstado(Request $request, Response $response, array $args): Response
    {
        $vehiculo = Vehiculo::find($args['id']);
        if (!$vehiculo) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Vehículo no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $body    = json_decode($request->getBody()->getContents(), true);
        $validos = ['disponible', 'en_ruta', 'mantenimiento', 'inactivo'];

        if (empty($body['estado']) || !in_array($body['estado'], $validos)) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Estado inválido. Use: ' . implode(', ', $validos)]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $vehiculo->update(['estado' => $body['estado']]);
        $response->getBody()->write(json_encode(['success' => true, 'message' => "Estado cambiado a {$body['estado']}", 'data' => $vehiculo->fresh()]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}