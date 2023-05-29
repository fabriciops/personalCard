<?php

use PHPUnit\Framework\TestCase;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use App\DAO\MySQL\personalCard\LojasDAO;
use App\Models\MySQL\personalCard\LojaModel;
use App\Controllers\LojaController;

class LojaControllerTest extends TestCase
{
    private $container;
    private $request;
    private $response;
    private $lojasDAO;
    private $lojaController;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->request = Request::createFromEnvironment(Environment::mock());
        $this->response = new Response();
        $this->lojasDAO = $this->createMock(LojasDAO::class);

        $this->container[LojasDAO::class] = function ($container) {
            return $this->lojasDAO;
        };

        $this->lojaController = new LojaController($this->container);
    }

    public function testGetLojas(): void
    {
        $lojas = [
            new LojaModel(),
            new LojaModel(),
        ];

        $this->lojasDAO->expects($this->once())
            ->method('getAllLojas')
            ->willReturn($lojas);

        $response = $this->lojaController->getLojas($this->request, $this->response, []);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($lojas), (string)$response->getBody());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }

    // public function testInsertLoja(): void
    // {
    //     $data = [
    //         'nome' => 'Loja Teste',
    //         'endereco' => 'Rua Teste',
    //         'telefone' => '1234567890',
    //     ];

    //     $loja = new LojaModel();
    //     $loja->setNome($data['nome'])
    //         ->setEndereco($data['endereco'])
    //         ->setTelefone($data['telefone']);

    //     $this->lojasDAO->expects($this->once())
    //         ->method('insertLoja')
    //         ->with($this->equalTo($loja));

    //     $this->request = $this->request->withParsedBody($data);

    //     $response = $this->lojaController->insertLoja($this->request, $this->response, []);

    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertEquals(json_encode(['message' => 'Loja inserida com sucesso!']), (string)$response->getBody());
    //     $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    // }

    // public function testUpdateLoja(): void
    // {
    //     $data = [
    //         'id' => 1,
    //         'nome' => 'Loja Teste',
    //         'endereco' => 'Rua Teste',
    //         'telefone' => '1234567890',
    //     ];

    //     $loja = new LojaModel();
    //     $loja->setId($data['id'])
    //         ->setNome($data['nome'])
    //         ->setEndereco($data['endereco'])
    //         ->setTelefone($data['telefone']);

    //     $this->lojasDAO->expects($this->once())
    //         ->method('updateLoja')
    //         ->with($this->equalTo($loja));

    //     $this->request = $this->request->withParsedBody($data);

    //     $response = $this->lojaController->updateLoja($this->request, $this->response, []);

    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertEquals(json_encode(['message' => 'Loja alterada com sucesso!']), (string)$response->getBody());
    //     $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    // }

    // public function testDeleteLoja(): void
    // {
    //     $queryParams = [
    //         'id' => 1,
    //     ];

    //     $this->lojasDAO->expects($this->once())
    //         ->method('deleteLoja')
    //         ->with($this->equalTo($queryParams['id']));

    //     $this->request = $this->request->withQueryParams($queryParams);

    //     $response = $this->lojaController->deleteLoja($this->request, $this->response, []);

    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertEquals(json_encode(['message' => 'Loja excluída com sucesso!']), (string)$response->getBody());
    //     $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    // }
}
