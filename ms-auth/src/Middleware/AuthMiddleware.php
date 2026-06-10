<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Nyholm\Psr7\Response;
use App\Models\Usuario;

class AuthMiddleware
{
    public function __invoke(
        Request $request,
        Handler $handler
    ): \Psr\Http\Message\ResponseInterface
    {
        // Permitir preflight CORS
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $handler->handle($request);
        }

        $header = $request->getHeaderLine('Authorization');

        if (empty($header)) {
            $response = new Response(401);

            $response->getBody()->write(json_encode([
                'error' => 'Token requerido'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        // Extraer token Bearer
        $token = $header;

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            $token = trim($matches[1]);
        }

        $user = Usuario::where('token', $token)
            ->where('estado', 'activo')
            ->where('sesion_activa', true)
            ->first();

        if (!$user) {
            $response = new Response(401);

            $response->getBody()->write(json_encode([
                'error' => 'Token inválido o sesión cerrada'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        $request = $request->withAttribute('user', [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'rol' => $user->rol
        ]);

        return $handler->handle($request);
    }
}