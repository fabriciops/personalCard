<?php

use function src\{
    slimConfiguration,
    basicAuth,
    jwtAuth
};

use App\Controllers\{
    ProdutoController,
    LojaController,
    AuthController,
    ExceptionController,
    UsuarioController,
    PostagemController
};
use App\Middlewares\JwtDateTimeMiddleware;

require_once __DIR__ . '/../src/slimConfiguration.php';

$app = new \Slim\App(slimConfiguration());

$app->get('/', function () {
    echo 'oi';
});

$app->group('/v1', function () use ($app) {
    $app->get('/test-with-versions', function () {
        return "oi v1";
    });
});

$app->group('/v2', function () use ($app) {
    $app->get('/test-with-versions', function () {
        return "oi v2";
    });
});

$app->get('/exception-test', ExceptionController::class . ':test');

$app->post('/login', AuthController::class . ':login');

$app->post('/refresh-token', AuthController::class . ':refreshToken');

$app->get('/valideTimeToken', function ($request, $response) {
    var_dump($request->getAttribute('jwt'));
})->add(new JwtDateTimeMiddleware())->add(jwtAuth());

$app->get('/ativarCadastro', UsuarioController::class . ':ativarCadastro');
$app->post('/cadastro', UsuarioController::class . ':insertUsuario');

$app->group('', function () use ($app) {
    $app->post('/usuario', UsuarioController::class . ':insertUsuario');
    $app->post('/logout', AuthController::class . ':logout');

    $app->get('/postagem', PostagemController::class . ':getPostagens');
    $app->get('/postagem/{id}', PostagemController::class . ':getPostagemById');
    $app->post('/postagem', PostagemController::class . ':insertPostagem');
    $app->put('/postagem/{id}', PostagemController::class . ':updatePostagem');
    $app->delete('/postagem/{id}', PostagemController::class . ':deletePostagem');

    $app->get('/loja', LojaController::class . ':getLojas');
    $app->post('/loja', LojaController::class . ':insertLoja');
    $app->put('/loja', LojaController::class . ':updateLoja');
    $app->delete('/loja', LojaController::class . ':deleteLoja');

    $app->get('/produto', ProdutoController::class . ':getProdutos');
    $app->post('/produto', ProdutoController::class . ':insertProduto');
    $app->put('/produto', ProdutoController::class . ':updateProduto');
    $app->delete('/produto', ProdutoController::class . ':deleteProduto');

})->add(jwtAuth());


$app->run();
