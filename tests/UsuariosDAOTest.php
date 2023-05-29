<?php

use PHPUnit\Framework\TestCase;
use App\DAO\MySQL\personalCard\UsuariosDAO;
use App\Models\MySQL\personalCard\UsuarioModel;

class UsuariosDAOTest extends TestCase
{
    private $usuariosDAO;

    protected function setUp(): void
    {
        $this->usuariosDAO = new UsuariosDAO();
    }

    public function testGetUsuarioById(): void
    {
        $usuarioId = 1;

        $usuario = new UsuarioModel();
        $usuario->setId($usuarioId)
            ->setNome('John Doe')
            ->setEmail('john@example.com')
            ->setSenha('password123')
            ->setStatusConta('ativo')
            ->setCodigoAtivacao('123456');

        $expectedResult = $usuario;

        $actualResult = $this->usuariosDAO->getUsuarioById($usuarioId);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetUsuarioIdFromToken(): void
    {
        $decodedToken = [
            'sub' => 1
        ];

        $expectedResult = 1;

        $actualResult = $this->usuariosDAO->getUsuarioIdFromToken($decodedToken);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetUserByEmail(): void
    {
        $email = 'john@example.com';

        $usuario = new UsuarioModel();
        $usuario->setId(1)
            ->setNome('John Doe')
            ->setEmail($email)
            ->setSenha('password123')
            ->setStatusConta('ativo')
            ->setCodigoAtivacao('123456');

        $expectedResult = $usuario;

        $actualResult = $this->usuariosDAO->getUserByEmail($email);

        $this->assertEquals($expectedResult, $actualResult);
    }

    // Implemente os outros testes para os demais métodos da classe UsuariosDAO

    // ...
}
