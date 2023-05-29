<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use App\Controllers\PostagemController;
use App\DAO\MySQL\personalCard\PostagemDAO;
use App\Models\MySQL\personalCard\PostagemModel;
use App\Models\MySQL\personalCard\UsuarioModel;
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

    private $usuarioModel;
    private $postagemModel;


    protected function setUp(): void
    {
        $this->container = new Container();
        $this->request = Request::createFromEnvironment(Environment::mock());
        $this->response = new Response();
        $this->postagemDao = $this->createMock(PostagemDAO::class);
        $this->postagemService = $this->createMock(PostagemService::class);

        $this->postagemModel = new PostagemModel();
        $this->usuarioModel = new UsuarioModel();

        $this->container[PostagemDAO::class] = function ($container) {
            return $this->postagemDao;
        };

        $this->postagemController = new PostagemController($this->postagemService);
    }

    public function testGetPostagemById(): void
    {
        // Definir o comportamento esperado do PostagemService mock
        $postagem = $this->postagemModel;
        $postagem->setId(1);
        $postagem->setTitulo('Título da Postagem');
        $postagem->setTexto('Texto da Postagem');
        $postagem->setDataPostagem('2023-05-28 10:00:00');
        $postagem->setUsuarioId(1);

        $usuario = $this->usuarioModel;
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

    public function testGetPostagens(): void
    {
        $pagina = 1;
        $limite = 10;
        // Definir o comportamento esperado do PostagemService mock
        $postagem1 = (new PostagemModel())
            ->setId(1)
            ->setTitulo('Título da Postagem 1')
            ->setTexto('Texto da Postagem 1')
            ->setDataPostagem('2023-05-28 10:00:00')
            ->setUsuarioId(1)
            ->setUsuario(
                ($this->usuarioModel)
                    ->setId(1)
                    ->setNome('Nome do Usuário 1')
                    ->setEmail('usuario1@example.com')
            );

        $postagem2 = (new PostagemModel())
            ->setId(2)
            ->setTitulo('Título da Postagem 2')
            ->setTexto('Texto da Postagem 2')
            ->setDataPostagem('2023-05-29 12:00:00')
            ->setUsuarioId(2)
            ->setUsuario(
                ($this->usuarioModel)
                    ->setId(2)
                    ->setNome('Nome do Usuário 2')
                    ->setEmail('usuario2@example.com')
            );

        $postagens = [$postagem1, $postagem2];

        $this->postagemService->expects($this->once())
            ->method('getPaginatedPostagens')
            ->withConsecutive([$pagina, $limite])
            ->willReturn($postagens);

        // Executar o método a ser testado
        $controller = $this->postagemController;
        $actualResponse = $controller->getPostagens($this->request, $this->response);

        // Verificar o resultado
        $expectedBody = json_encode(['postagens' => $postagens], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertStringStartsWith('application/json', $actualResponse->getHeaderLine('Content-Type'));
    }

    public function testInsertPostagem(): void
    {
        // Dados de exemplo para a postagem
        $postData = [
            'titulo' => 'Título da Postagem',
            'texto' => 'Texto da Postagem'
        ];

        // Criar uma instância simulada de PostagemModel
        $postagemModel = (new PostagemModel())
            ->setId(1)
            ->setTitulo($postData['titulo'])
            ->setTexto($postData['texto'])
            ->setDataPostagem(date('Y-m-d H:i:s'))
            ->setUsuarioId(1)
            ->setUsuario($this->usuarioModel);

        // Definir o comportamento esperado do PostagemService mock
        $usuarioId = 1;
        $this->postagemService->expects($this->once())
            ->method('createPostagem')
            ->with($postData, $usuarioId)
            ->willReturn($postagemModel);

        // Executar o método a ser testado
        $controller = $this->postagemController;
        $request = $this->createRequestWithJwt(['sub' => $usuarioId]); // Simular usuário autenticado
        $response = $this->createResponse();
        $actualResponse = $controller->insertPostagem($request, $response);

        // Verificar o resultado
        $expectedBody = json_encode(['mensagem' => 'Postagem Inserida com sucesso'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->assertEquals(201, $actualResponse->getStatusCode());
        $this->assertEquals($expectedBody, (string)$actualResponse->getBody());
        $this->assertStringStartsWith('application/json', $actualResponse->getHeaderLine('Content-Type'));
    }



}
