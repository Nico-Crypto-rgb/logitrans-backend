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
        // Allow OPTIONS through
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
                       ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        // Accept both 'Bearer token' and raw token
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = trim(substr($authHeader, 7));
        } else {
            $token = trim($authHeader);
        }

        $msAuthUrl = $_ENV['MS_AUTH_URL'] ?? 'http://localhost:8001';

        // Consulta a ms-auth para validar el token
        $ch = curl_init("$msAuthUrl/auth/validate");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["Authorization: $token"],
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
                       ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        // Adjunta los datos del usuario al request para usarlos en el controlador
        $userData = json_decode($resultado, true);
        $request = $request->withAttribute('usuario', $userData['user'] ?? []);

        return $handler->handle($request);
    }
}