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
        // Permitir solicitudes OPTIONS (CORS preflight)
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            $res = new NyholmResponse(401);
            $res->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token no proporcionado'
            ]));
            
            return $res->withHeader('Content-Type', 'application/json')
                       ->withHeader('Access-Control-Allow-Origin', '*')
                       ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                       ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        }

        // Extracción robusta del token
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = trim($matches[1]);
        } else {
            $token = trim($authHeader);
        }

        $msAuthUrl = $_ENV['MS_AUTH_URL'] ?? 'http://localhost:8001';

        // Consulta a ms-auth para validar el token
        $ch = curl_init("$msAuthUrl/auth/validate");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token" // Se agregó el prefijo 'Bearer ' que faltaba
            ],
            CURLOPT_TIMEOUT => 5,
        ]);
        
        $resultado  = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $res = new NyholmResponse(401);
            $res->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ]));
            
            return $res->withHeader('Content-Type', 'application/json')
                       ->withHeader('Access-Control-Allow-Origin', '*')
                       ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                       ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        }

        // Adjunta los datos del usuario al request
        $userData = json_decode($resultado, true);
        $request = $request->withAttribute('usuario', $userData['user'] ?? []);

        return $handler->handle($request);
    }
}