<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Nyholm\Psr7\Response;

class AuthMiddleware
{
    public function __invoke(Request $request, Handler $handler): \Psr\Http\Message\ResponseInterface
    {
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            $response = new Response(401);
            $response->getBody()->write(json_encode([
                'error' => 'Token requerido'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $user = \App\Models\Usuario::where('token', $token)
                                   ->where('activo', 1)
                                   ->first();

        if (!$user) {
            $response = new Response(401);
            $response->getBody()->write(json_encode([
                'error' => 'Token inválido o expirado'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $request = $request->withAttribute('user', [
            'id'     => $user->id,
            'nombre' => $user->nombre,
            'rol'    => $user->rol
        ]);

        return $handler->handle($request);
    }
}