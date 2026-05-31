<?php
namespace App\Controllers;

use App\Models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    // Login
    public function login(Request $request, Response $response): Response
    {
        $body  = $request->getParsedBody();
        $email = $body['email'] ?? '';
        $pass  = $body['password'] ?? '';

        $user = Usuario::where('email', $email)
                       ->where('activo', 1)
                       ->first();

        if (!$user || !password_verify($pass, $user->password)) {
            $response->getBody()->write(json_encode([
                'error' => 'Credenciales inválidas'
            ]));
            return $response->withStatus(401)
                           ->withHeader('Content-Type', 'application/json');
        }

        $token = bin2hex(random_bytes(32));
        $user->update(['token' => $token]);

        $response->getBody()->write(json_encode([
            'token' => $token,
            'user'  => [
                'id'     => $user->id,
                'nombre' => $user->nombre,
                'rol'    => $user->rol
            ]
        ]));
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json');
    }

    // Validar token (usado por otros microservicios)
    public function validate(Request $request, Response $response): Response
    {
        $token = $request->getHeaderLine('Authorization');
        $user  = Usuario::where('token', $token)
                        ->where('activo', 1)
                        ->first();

        if (!$user) {
            $response->getBody()->write(json_encode(['valid' => false]));
            return $response->withStatus(401)
                           ->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'valid' => true,
            'user'  => [
                'id'     => $user->id,
                'nombre' => $user->nombre,
                'rol'    => $user->rol
            ]
        ]));
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json');
    }

    // Logout
    public function logout(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        Usuario::where('id', $user['id'])->update(['token' => null]);

        $response->getBody()->write(json_encode([
            'message' => 'Sesión cerrada correctamente'
        ]));
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json');
    }

    // Listar usuarios
    public function index(Request $request, Response $response): Response
    {
        $usuarios = Usuario::select('id','nombre','email','rol','activo','created_at')
                           ->get();
        $response->getBody()->write($usuarios->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Crear usuario
    public function store(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        if (!in_array($body['rol'] ?? '', Usuario::ROLES)) {
            $response->getBody()->write(json_encode(['error' => 'Rol inválido']));
            return $response->withStatus(400)
                           ->withHeader('Content-Type', 'application/json');
        }

        $usuario = Usuario::create([
            'nombre'   => $body['nombre'],
            'email'    => $body['email'],
            'password' => password_hash($body['password'], PASSWORD_BCRYPT),
            'rol'      => $body['rol'],
            'activo'   => 1,
        ]);

        $response->getBody()->write($usuario->toJson());
        return $response->withStatus(201)
                        ->withHeader('Content-Type', 'application/json');
    }
}