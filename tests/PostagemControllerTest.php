<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use App\Controllers\PostagemController;
use App\DAO\MySQL\personalCard\PostagemDAO;
use App\Services\PostagemService;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;


class PostagemControllerTest extends TestCase
{
    private $postagemService;
    private $request;
    private $response;
    private $args;
    private $container;

    private $postagemDao;

    private $postagemController;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->request = Request::createFromEnvironment(Environment::mock());
        $this->response = new Response();
        $this->postagemDao = $this->createMock(PostagemDAO::class);
        $this->postagemService = $this->createMock(PostagemService::class);

        $this->container[PostagemDAO::class] = function ($container) {
            return $this->postagemDao;
        };

        $this->postagemController = new PostagemController($this->postagemService);
    }

    public function testGetPostagemById(): void
    {
        // Definir o comportamento esperado do PostagemService mock
        $postagem = new \App\Models\MySQL\personalCard\PostagemModel();
        $postagem->setId(1);
        $postagem->setTitulo('Título da Postagem');
        $postagem->setTexto('Texto da Postagem');
        $postagem->setDataPostagem('2023-05-28 10:00:00');
        $postagem->setUsuarioId(1);

        $usuario = new \App\Models\MySQL\personalCard\UsuarioModel();
        $usuario->setId(1);
        $usuario->setNome('Nome do Usuário');
        $usuario->setEmail('usuario@example.com');

        $postagem->setUsuario($usuario);

        $this->postagemService->expects($this->once())
            ->method('getPostagemById')
            ->with(1)
            ->willReturn($postagem);

        // Executar o método a ser testado
        $controller = $this->postagemController;
        $this->args = ['id' => 1]; // Definir o valor para $this->args
        $actualResponse = $controller->getPostagemById($this->request, $this->response, $this->args);

        // Verificar o resultado
        $expectedData = [
            'id' => 1,
            'titulo' => 'Título da Postagem',
            'texto' => 'Texto da Postagem',
            'dataPostagem' => '2023-05-28 10:00:00',
            'usuarioId' => 1,
            'usuario' => [
                'id' => 1,
                'nome' => 'Nome do Usuário',
                'email' => 'usuario@example.com',
            ],
        ];

        $expectedBody = json_encode(['postagem' => $expectedData], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals($expectedBody, (string)$actualResponse->getBody());
        $this->assertStringStartsWith('application/json', $actualResponse->getHeaderLine('Content-Type'));
    }


}
