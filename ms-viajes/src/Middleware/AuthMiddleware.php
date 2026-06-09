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
        // Allow preflight requests through without authentication
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            $res = new Response();
            $res->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $res->withStatus(401)
                       ->withHeader('Content-Type', 'application/json')
                       ->withHeader('Access-Control-Allow-Origin', '*')
                       ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                       ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        // Remove optional "Bearer " prefix
        $token = str_replace('Bearer ', '', $authHeader);

        // Validate token with ms-auth (send token in Authorization header)
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => 'http://localhost:8001/auth/validate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: $token"],
            CURLOPT_TIMEOUT        => 5,
        ]);
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $res = new Response();
            $res->getBody()->write(json_encode(['error' => 'Token inválido o expirado']));
            return $res->withStatus(401)
                       ->withHeader('Content-Type', 'application/json')
                       ->withHeader('Access-Control-Allow-Origin', '*')
                       ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                       ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        $userData = json_decode($result, true);
        $request  = $request->withAttribute('user', $userData);

        return $handler->handle($request);
    }
}