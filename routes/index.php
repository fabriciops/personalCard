<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

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

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->post('/books', function () {
    return 'book';
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

})->add(jwtAuth());

$app->run();
