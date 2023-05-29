<?php

namespace App\Controllers;

use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\DAO\MySQL\personalCard\UsuariosDAO;
use App\Helpers\Hash;
use App\Models\MySQL\personalCard\UsuarioModel;
use App\Services\UserService;
use Slim\Views\Twig;
use DateTime;
use Slim\Container;


class UsuarioController
{
    /** @var UsuariosDAO $usuariosDAO */
    private $usuariosDAO;

    public function __construct()
    {
        $this->usuariosDAO = new UsuariosDAO();
    }

    public function insertUsuario(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        // Check if email already exists
        $existingUser = $this->usuariosDAO->getUserByEmail($data['email']);
        if ($existingUser !== null) {
            $response->getBody()->write(
                json_encode([
                    'mensagem' => 'Este e-mail já está cadastrado, favor recupere sua senha.'
                ],
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        if($data['idade'] < getenv('personalCard_IDADE_MINIMA')){
            $response->getBody()->write(
                json_encode([
                    'mensagem' => "Usuario não possui idade igual ou superior a ".getenv('personalCard_IDADE_MINIMA')
                ],
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);

        }

        $linkAtivacao = $this->gerarLinkAtivacao();

        $usuario = new UsuarioModel();
        $usuario->setNome($data['nome'])
            ->setEmail($data['email'])
            ->setSenha(password_hash($data['senha'], PASSWORD_ARGON2I))
            ->setIdade($data['idade'])
            ->setStatusConta('inativo')
            ->setCodigoAtivacao($linkAtivacao['codigoAtivacao']);

        $this->usuariosDAO->insertUser($usuario);

        $response->getBody()->write(
            json_encode([
                'url_ativacao'=> $linkAtivacao['link'],
                'codigoAtivacao'=> $linkAtivacao['codigoAtivacao'],
                'mensagem' => 'Usuario Cadastrado com sucesso'
            ],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    private function gerarLinkAtivacao()
    {
        // Gerar um código único para o link de ativação
        $codigoAtivacao = bin2hex(random_bytes(16));

        // Retornar o link de ativação com o código
        $ativacao = [
            'link' => getenv('personalCard_URL') ."ativarCadastro?codigo=". $codigoAtivacao,
            'codigoAtivacao' => $codigoAtivacao,

        ];
        return $ativacao;
    }

    public function ativarCadastro(Request $request, Response $response): Response
    {
        $codigoAtivacao = $request->getQueryParam('codigo');

        // Buscar usuário pelo código de ativação
        $usuario = $this->usuariosDAO->getUsuarioByCodigoAtivacao($codigoAtivacao);

        if ($usuario === null) {
            $response->getBody()->write(
                json_encode([
                    'mensagem' => 'Código de ativação inválido.'
                ],
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Ativar cadastro do usuário
        $this->usuariosDAO->ativarCadastro($usuario);

        $response->getBody()->write(
            json_encode([
                'mensagem' => 'Cadastro ativado com sucesso.'
            ],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function log($response, array $data){


        $response->getBody()->write(
            json_encode([
                'data' => $data
            ],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

}
