<?php

namespace src;

use App\Controllers\PostagemController;
use App\DAO\MySQL\personalCard\LojasDAO;
use App\DAO\MySQL\personalCard\PostagemDAO;
use App\Services\PostagemService;
use Psr\Container\ContainerInterface;

function slimConfiguration(): \Psr\Container\ContainerInterface
{
    $configuration = [
        'settings' => [
            'displayErrorDetails' => getenv('DISPLAY_ERRORS_DETAILS'),
        ],
    ];

    $container = new \Slim\Container($configuration);

    $container->offsetSet(PostagemController::class, function ($container) {
        $postagemService = $container->get(PostagemService::class);
        return new PostagemController($postagemService);
    });

    $container->offsetSet(PostagemService::class, function ($container) {
        $postagemDAO = $container->get(PostagemDAO::class);
        return new PostagemService($postagemDAO);
    });

    $container->offsetSet(PostagemDAO::class, function () use ($container) {
        // Configurar e retornar a instância do PostagemDAO
        // caso ele tenha alguma dependência adicional
        return new PostagemDAO();
    });


    return $container;
}
