<?php

namespace App\Controllers;

use App\Models\SeguimientoViaje;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SeguimientoViajeController
{
    // Listar todos los seguimientos
    public function index(Request $request, Response $response): Response
    {
        $seguimientos = SeguimientoViaje::all();
        $response->getBody()->write(json_encode($seguimientos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Guardar un nuevo seguimiento
    public function store(Request $request, Response $response): Response
{
    // 1. Intentamos obtener los datos parseados por Slim (preferible)
    $data = $request->getParsedBody();

    // 2. Si es nulo, intentamos leer el cuerpo manualmente como respaldo
    if (empty($data)) {
        $rawBody = (string)$request->getBody();
        $data = json_decode($rawBody, true);
    }

    // 3. Verificamos si logramos obtener datos (validación de JSON)
    if (empty($data)) {
        $response->getBody()->write(json_encode([
            'error' => 'No se recibieron datos o formato JSON inválido'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // 4. Validación de campos obligatorios
    if (!isset($data['programacion_viaje_id'], $data['estado'])) {
        $response->getBody()->write(json_encode([
            'error' => 'Datos incompletos: programacion_viaje_id y estado son obligatorios'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // 5. Creación
    $seguimiento = SeguimientoViaje::create([
        'programacion_viaje_id' => $data['programacion_viaje_id'],
        'fecha'                 => $data['fecha'] ?? date('Y-m-d'),
        'hora'                  => $data['hora'] ?? date('H:i:s'),
        'estado'                => $data['estado'],
        'novedad'               => $data['novedad'] ?? null,
    ]);

    $response->getBody()->write(json_encode(['success' => true, 'data' => $seguimiento]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
}

    // Obtener seguimiento por ID
    public function show(Request $request, Response $response, array $args): Response
    {
        $seguimiento = SeguimientoViaje::find($args['id']);

        if (!$seguimiento) {
            $response->getBody()->write(json_encode(['message' => 'Seguimiento no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($seguimiento));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Obtener historial por ID de programación de viaje
    public function getByProgramacion(Request $request, Response $response, array $args): Response
    {
        $programacionId = $args['programacion_viaje_id'];
        
        $seguimientos = SeguimientoViaje::where('programacion_viaje_id', $programacionId)
                                        ->orderBy('fecha', 'desc')
                                        ->orderBy('hora', 'desc')
                                        ->get();

        $response->getBody()->write(json_encode($seguimientos));
        return $response->withHeader('Content-Type', 'application/json');
    }
}