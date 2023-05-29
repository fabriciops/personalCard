// app/Controllers/HomeController.php
<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    protected $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function index(Request $request, Response $response)
    {
        $data = ['name' => 'John'];

        return $this->view->render($response, 'hello.twig', $data);
    }
}
