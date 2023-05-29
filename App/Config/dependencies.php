<?php

use DI\ContainerBuilder;
use App\Services\PostagemService;
use App\DAO\MySQL\personalCard\LojasDAO;
use App\DAO\MySQL\personalCard\PostagemDAO;
use App\DAO\MySQL\personalCard\UsuariosDAO;
use App\DAO\MySQL\personalCard\TokensDAO;

// Cria o ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Configura as definições de dependência
$containerBuilder->addDefinitions([
    // Adicione as definições das dependências aqui
    PostagemService::class => function ($container) {
        // Obtém as dependências necessárias do container
        $postagemDAO = $container->get(PostagemDAO::class);
        $usuarioDAO = $container->get(UsuariosDAO::class);

        // Cria e retorna a instância de PostagemService
        return new PostagemService($postagemDAO, $usuarioDAO);
    },
    PostagemDAO::class => function ($container) {
        // Obtém as dependências necessárias do container
        $pdo = $container->get(PDO::class); // Exemplo de dependência PDO

        // Cria e retorna a instância de PostagemDAO
        return new PostagemDAO($pdo);
    },
    UsuariosDAO::class => function ($container) {
        // Obtém as dependências necessárias do container
        $pdo = $container->get(PDO::class); // Exemplo de dependência PDO

        // Cria e retorna a instância de UsuariosDAO
        return new UsuariosDAO($pdo);
    },
    TokensDAO::class => function ($container) {
        // Obtém as dependências necessárias do container
        $pdo = $container->get(PDO::class); // Exemplo de dependência PDO

        // Cria e retorna a instância de TokensDAO
        return new TokensDAO($pdo);
    },
    PDO::class => function () {
        // Configuração do PDO para conexão com o banco de dados
        $dsn = 'mysql:host=seu_host;dbname=seu_banco;charset=utf8';
        $username = 'seu_usuario';
        $password = 'sua_senha';

        // Cria e retorna a instância de PDO
        return new PDO($dsn, $username, $password);
    },
]);

// Cria o container de dependências
$container = $containerBuilder->build();

// Retorna o container
return $container;
