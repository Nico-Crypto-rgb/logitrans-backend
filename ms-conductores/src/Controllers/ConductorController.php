<?php

namespace App\Controllers;

use App\Models\Conductor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConductorController
{
    // GET /conductores
    public function index(Request $request, Response $response): Response
    {
        $conductores = Conductor::all();
        $response->getBody()->write(json_encode([
            'success' => true,
            'data'    => $conductores,
            'total'   => count($conductores)
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /conductores/{id}
    public function show(Request $request, Response $response, array $args): Response
    {
        $conductor = Conductor::find($args['id']);
        if (!$conductor) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Conductor no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['success' => true, 'data' => $conductor]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /conductores
    public function store(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $body = is_array($body) ? $body : (array)$body;

        // Validación de campos requeridos (según tu nuevo diseño de tabla)
        if (
            empty($body['nombres']) || 
            empty($body['apellidos']) || 
            empty($body['documento']) || 
            empty($body['telefono']) ||
            empty($body['correo']) ||
            empty($body['numero_licencia']) ||
            empty($body['categoria_licencia']) ||
            empty($body['fecha_vencimiento_licencia'])
        ) {
            $response->getBody()->write(json_encode([
                'success' => false, 
                'message' => 'Todos los campos son requeridos: nombres, apellidos, documento, telefono, correo, numero_licencia, categoria_licencia, fecha_vencimiento_licencia'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Verificación de existencia (usando el campo nuevo 'documento')
        if (Conductor::where('documento', $body['documento'])->exists()) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Ya existe un conductor con ese número de documento']));
            return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
        }

        // Creación del registro
        $conductor = Conductor::create([
            'nombres'                    => $body['nombres'],
            'apellidos'                  => $body['apellidos'],
            'documento'                  => $body['documento'],
            'telefono'                   => $body['telefono'],
            'correo'                     => $body['correo'],
            'numero_licencia'            => $body['numero_licencia'],
            'categoria_licencia'         => $body['categoria_licencia'],
            'fecha_vencimiento_licencia' => $body['fecha_vencimiento_licencia'],
            'estado'                     => $body['estado'] ?? 'disponible',
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor creado', 'data' => $conductor]));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    // PUT /conductores/{id}
    public function update(Request $request, Response $response, array $args): Response
    {
        $conductor = Conductor::find($args['id']);
        if (!$conductor) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Conductor no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $body = $request->getParsedBody();
        $body = is_array($body) ? $body : (array)$body;

        $conductor->update([
            'nombres'                    => $body['nombres'] ?? $conductor->nombres,
            'apellidos'                  => $body['apellidos'] ?? $conductor->apellidos,
            'documento'                  => $body['documento'] ?? $conductor->documento,
            'telefono'                   => $body['telefono'] ?? $conductor->telefono,
            'correo'                     => $body['correo'] ?? $conductor->correo,
            'numero_licencia'            => $body['numero_licencia'] ?? $conductor->numero_licencia,
            'categoria_licencia'         => $body['categoria_licencia'] ?? $conductor->categoria_licencia,
            'fecha_vencimiento_licencia' => $body['fecha_vencimiento_licencia'] ?? $conductor->fecha_vencimiento_licencia,
            'estado'                     => $body['estado'] ?? $conductor->estado,
        ]);

        $response->getBody()->write(json_encode([
            'success' => true, 
            'message' => 'Conductor actualizado', 
            'data' => $conductor->fresh()
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // DELETE /conductores/{id}
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $conductor = Conductor::find($args['id']);
        if (!$conductor) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Conductor no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $conductor->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor eliminado']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // PATCH /conductores/{id}/estado
    public function cambiarEstado(Request $request, Response $response, array $args): Response
    {
        $conductor = Conductor::find($args['id']);
        if (!$conductor) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Conductor no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $body = $request->getParsedBody();
        $body = is_array($body) ? $body : (array)$body;

        $validos = ['disponible', 'en_ruta', 'inactivo'];

        if (empty($body['estado']) || !in_array($body['estado'], $validos)) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Estado inválido. Use: disponible, en_ruta, inactivo']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $conductor->update(['estado' => $body['estado']]);
        
        $response->getBody()->write(json_encode([
            'success' => true, 
            'message' => "Estado cambiado a {$body['estado']}", 
            'data' => $conductor->fresh()
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}