<?php

namespace src;

use App\DAO\MySQL\personalCard\UsuariosDAO;
use Tuupola\Middleware\JwtAuthentication;

function jwtAuth(): JwtAuthentication
{
    return new JwtAuthentication([
        'secret' => getenv('JWT_SECRET_KEY'),
        'attribute' => 'jwt',
        'before' => function ($request, $arguments) {
            $request = $request->withAttribute('jwt', $arguments['decoded']);

            // Obtenha o ID do usuário a partir do token
            $usuariosDAO = new UsuariosDAO();
            $usuarioId = $usuariosDAO->getUsuarioIdFromToken($arguments['decoded']);

            $request = $usuarioId;

            return $request;
        }
    ]);
}
