<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PostagemService;

class PostagemController
{
    /** @var PostagemService */
    private $postagemService;

    public function __construct(PostagemService $postagemService)
    {
        $this->postagemService = $postagemService;
    }

    public function getPostagens(Request $request, Response $response): Response
    {
        $pagina = $request->getQueryParam('pagina', 1);
        $limite = $request->getQueryParam('limite', 10);

        $postagens = $this->postagemService->getPaginatedPostagens($pagina, $limite);

        $response->getBody()->write(json_encode($postagens));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function insertPostagem(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // Verificar se o usuário está autenticado
        $usuarioId = $request->getAttribute('jwt')['id'];

        $postagem = $this->postagemService->createPostagem($data, $usuarioId);

        $response->getBody()->write(json_encode($postagem));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function updatePostagem(Request $request, Response $response, array $args): Response
    {
        $postagemId = $args['id'];
        $data = $request->getParsedBody();

        // Verificar se o usuário está autenticado
        $usuarioId = $request->getAttribute('jwt')['id'];

        $postagem = $this->postagemService->updatePostagem($postagemId, $data, $usuarioId);

        $response->getBody()->write(json_encode($postagem));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deletePostagem(Request $request, Response $response, array $args): Response
    {
        $postagemId = $args['id'];

        // Verificar se o usuário está autenticado
        $usuarioId = $request->getAttribute('jwt')['id'];

        $this->postagemService->deletePostagem($postagemId, $usuarioId);

        return $response->withStatus(204);
    }
}
