<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. Permitir preflight (CORS) sin autenticación
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeaderLine('Authorization');

        // 2. Verificar que exista el header
        if (empty($authHeader)) {
            return $this->errorResponse('Token no proporcionado', 401);
        }

        // 3. Limpiar el token
        $token = str_replace('Bearer ', '', $authHeader);

        // 4. Validar token con ms-auth
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => 'http://localhost:8001/auth/validate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer $token"], // Aseguramos formato Bearer
            CURLOPT_TIMEOUT        => 5,
        ]);
        
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        // 5. Verificar si hubo error de conexión (ms-auth caído) o respuesta no exitosa
        if ($curlErr || $httpCode !== 200) {
            return $this->errorResponse('Token inválido o servicio de auth no disponible', 401);
        }

        // 6. Si es válido, inyectamos los datos del usuario al request
        $userData = json_decode($result, true);
        $request  = $request->withAttribute('user', $userData);

        return $handler->handle($request);
    }

    /**
     * Helper para reducir repetición de código en las respuestas de error
     */
    private function errorResponse(string $message, int $status): ResponseInterface
    {
        $res = new Response();
        $res->getBody()->write(json_encode(['error' => $message]));
        return $res->withStatus($status)
                   ->withHeader('Content-Type', 'application/json')
                   ->withHeader('Access-Control-Allow-Origin', '*')
                   ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                   ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}