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
    // Al tener $app->addBodyParsingMiddleware() en index.php,
    // Slim ya procesó el JSON. Obtenemos los datos con getParsedBody()
    $body = $request->getParsedBody();

    // Aseguramos que sea un array por si getParsedBody devuelve un objeto stdClass
    $body = is_array($body) ? $body : (array)$body;

    $usuario = $request->getAttribute('usuario');

    // Validación de campos requeridos
    if (empty($body['nombre']) || empty($body['cedula']) || empty($body['licencia'])) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'nombre, cedula y licencia son requeridos']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    // Verificación de existencia
    if (Conductor::where('cedula', $body['cedula'])->exists()) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Ya existe un conductor con esa cédula']));
        return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
    }

    // Creación del registro
    $conductor = Conductor::create([
        'nombre'     => $body['nombre'],
        'cedula'     => $body['cedula'],
        'licencia'   => $body['licencia'],
        'telefono'   => $body['telefono'] ?? null,
        'estado'     => $body['estado'] ?? 'disponible',
        'usuario_id' => $usuario['id'] ?? 1,
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

    // Usamos getParsedBody() gracias al middleware configurado en index.php
    $body = $request->getParsedBody();
    $body = is_array($body) ? $body : (array)$body;

    $conductor->update([
        'nombre'   => $body['nombre']   ?? $conductor->nombre,
        'cedula'   => $body['cedula']   ?? $conductor->cedula,
        'licencia' => $body['licencia'] ?? $conductor->licencia,
        'telefono' => $body['telefono'] ?? $conductor->telefono,
        'estado'   => $body['estado']   ?? $conductor->estado,
    ]);

    $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor actualizado', 'data' => $conductor->fresh()]));
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

    // Usamos getParsedBody() para obtener los datos procesados
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