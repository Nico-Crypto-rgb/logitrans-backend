<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Nyholm\Psr7\Response as NyholmResponse;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Permitir OPTIONS para CORS
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            return $this->errorResponse(401, 'Token no proporcionado');
        }

        $token = str_starts_with($authHeader, 'Bearer ') 
            ? trim(substr($authHeader, 7)) 
            : trim($authHeader);

        $msAuthUrl = $_ENV['MS_AUTH_URL'] ?? 'http://localhost:8001';

        $ch = curl_init("$msAuthUrl/auth/validate");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer $token"], // Aseguramos formato Bearer
            CURLOPT_TIMEOUT        => 5,
        ]);

        $resultado = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Si cURL falla al conectar con ms-auth
        if ($resultado === false) {
            return $this->errorResponse(503, 'Servicio de autenticación no disponible: ' . $curlError);
        }

        if ($httpCode !== 200) {
            return $this->errorResponse(401, 'Token inválido o expirado');
        }

        $userData = json_decode($resultado, true);
        $request  = $request->withAttribute('usuario', $userData['user'] ?? []);
        
        return $handler->handle($request);
    }

    /**
     * Helper para mantener el código limpio y los headers de CORS consistentes
     */
    private function errorResponse(int $status, string $message): Response
    {
        $res = new NyholmResponse($status);
        $res->getBody()->write(json_encode(['success' => false, 'message' => $message]));
        
        return $res->withHeader('Content-Type', 'application/json')
                   ->withHeader('Access-Control-Allow-Origin', '*')
                   ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                   ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    }
}