<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PostagemService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class PostagemController
{
    /** @var PostagemService */
    private $postagemService;

    private $logger;

    public function __construct(PostagemService $postagemService)
    {
        $this->postagemService = $postagemService;
        $this->logger = new Logger('debug');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/debug.log', Logger::DEBUG));

    }

    public function getPostagemById(Request $request, Response $response, array $args): Response
    {
        $postagemId = $args['id'];
        $postagem = $this->postagemService->getPostagemById($postagemId);

        $data = [
                'id' => $postagem->getId(),
                'titulo' => $postagem->getTitulo(),
                'texto' => $postagem->getTexto(),
                'dataPostagem' => $postagem->getDataPostagem(),
                'usuarioId' => $postagem->getUsuarioId(),
                'usuario' => [
                    'id' => $postagem->getUsuario()->getId(),
                    'nome' => $postagem->getUsuario()->getNome(),
                    'email' => $postagem->getUsuario()->getEmail(),
                ],
            ];

        $response->getBody()->write(
            json_encode(['postagem' => $data], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function helloWorld(Request $request, Response $response): Response
    {
        $body = $response->getBody();
        $body->write('Hello, World!');
        return $response;
    }


    public function getPostagens(Request $request, Response $response): Response
    {
        $pagina = $request->getQueryParam('pagina', 1);
        $limite = $request->getQueryParam('limite', 10);

        $postagens = $this->postagemService->getPaginatedPostagens($pagina, $limite);

        $data = [];
        foreach ($postagens as $postagem) {
            $data[] = [
                'id' => $postagem->getId(),
                'titulo' => $postagem->getTitulo(),
                'texto' => $postagem->getTexto(),
                'dataPostagem' => $postagem->getDataPostagem(),
                'usuarioId' => $postagem->getUsuarioId(),
                'usuario' => [
                    'id' => $postagem->getUsuario()->getId(),
                    'nome' => $postagem->getUsuario()->getNome(),
                    'email' => $postagem->getUsuario()->getEmail(),
                ],
            ];
        }

        $response->getBody()->write(
            json_encode(['postagens' => $data], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function insertPostagem(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // Verificar se o usuário está autenticado
        $usuarioId = $request->getAttribute('jwt')['sub'];
        $postagem = $this->postagemService->createPostagem($data, $usuarioId);

        if($postagem != false){

            $response->getBody()->write(
                json_encode([
                    'mensagem' => 'Postagem Inserida com sucesso'
                ],
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
            );
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }

        $response->getBody()->write(
            json_encode([
                'mensagem' => 'Não foi possível salvar essa postagem'
            ],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);

    }

    public function updatePostagem(Request $request, Response $response, array $args): Response
    {
        $postagemId = $args['id'];
        $data = $request->getParsedBody();

        // Verificar se o usuário está autenticado
        $usuarioId = $request->getAttribute('jwt')['sub'];

        $postagem = $this->postagemService->updatePostagem($postagemId, $data, $usuarioId);

        $response->getBody()->write(
            json_encode([
                'mensagem' => $postagem['mensagem']
            ],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );
        return $response->withHeader('Content-Type', 'application/json')->withStatus($postagem['codigo']);

    }

    public function deletePostagem(Request $request, Response $response, array $args): Response
    {
        $postagemId = $args['id'];


        // Verificar se o usuário está autenticado
        $usuarioId = $request->getAttribute('jwt')['sub'];

        $this->postagemService->deletePostagem($postagemId, $usuarioId);

        return $response->withStatus(204);
    }
}
