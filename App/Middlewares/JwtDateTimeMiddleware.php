<?php

namespace App\Middlewares;

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

final class JwtDateTimeMiddleware{

    public function __invoke(Resquest $request, Response $response, callable $next){
        $token = $request->getAttribute('jwt');
        $expireDate = new \DateTime($token['expired_at']);

        if($expireDate < new \DateTime())
            return $response->withStatus(401);

        return $next($request, $response);




    }
}
