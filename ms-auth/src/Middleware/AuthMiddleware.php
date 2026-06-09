<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Nyholm\Psr7\Response;

class AuthMiddleware
{
    public function __invoke(Request $request, Handler $handler): \Psr\Http\Message\ResponseInterface
    {
        // Allow OPTIONS through (preflight)
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $handler->handle($request);
        }

        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            $response = new Response(401);
            $response->getBody()->write(json_encode([
                'error' => 'Token requerido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withHeader('Access-Control-Allow-Origin', '*')
                            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        // Accept raw token or 'Bearer ' prefixed
        if (str_starts_with($token, 'Bearer ')) {
            $token = trim(substr($token, 7));
        }

        $user = \App\Models\Usuario::where('token', $token)
                                   ->where('activo', 1)
                                   ->first();

        if (!$user) {
            $response = new Response(401);
            $response->getBody()->write(json_encode([
                'error' => 'Token inválido o expirado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withHeader('Access-Control-Allow-Origin', '*')
                            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        $request = $request->withAttribute('user', [
            'id'     => $user->id,
            'nombre' => $user->nombre,
            'rol'    => $user->rol
        ]);

        return $handler->handle($request);
    }
}