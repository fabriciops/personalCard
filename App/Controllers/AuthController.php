<?php

namespace App\Controllers;

use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\DAO\MySQL\personalCard\UsuariosDAO;
use App\DAO\MySQL\personalCard\TokensDAO;
use App\Models\MySQL\personalCard\TokenModel;
use App\Services\UsuarioService;
use DateTime;

final class AuthController
{

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $senha = $data['senha'];

        $usuario = $this->getUserByEmail($email);

        if (is_null($usuario) || !password_verify($senha, $usuario->getSenha())) {
            return $response->withStatus(401);
        } else if ($usuario->getStatusConta() === 'inativo') {
            $response->getBody()->write(
                json_encode([
                    'mensagem' => "Você ainda não realizou a ativação da sua conta. Segue o link para ativação: " . $this->gerarLinkAtivacaoCodigo($usuario->getCodigoAtivacao())
                ],
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $expireDate = $this->getExpireDate();
        $token = $this->generateToken($usuario, $expireDate);
        $refreshToken = $this->generateRefreshToken($usuario);

        $tokenModel = new TokenModel();
        $tokenModel->setExpired_at($expireDate)
            ->setRefresh_token($refreshToken)
            ->setToken($token)
            ->setUsuarios_id($usuario->getId());

        $tokensDAO = new TokensDAO();
        $tokensDAO->createToken($tokenModel);

        $response = $response->withJson([
            "token" => $token,
            "refresh_token" => $refreshToken
        ]);

        return $response;
    }

    private function gerarLinkAtivacaoCodigo($codigo)
    {
        // Retornar o link de ativação com o código
        return getenv('personalCard_URL') . "ativarCadastro?codigo=" . $codigo;

    }

    public function logout(Request $request, Response $response): Response
    {
        // Recupera o token do cabeçalho Authorization
        $authorizationHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authorizationHeader);

        // Remove o token do banco de dados
        $tokensDAO = new TokensDAO();
        $tokensDAO->deleteTokenByToken($token);

        return $response->withJson(['message' => 'Logout realizado com sucesso']);
    }

    public function activate(Request $request, Response $response): Response
    {
        $code = $request->getQueryParams()['code'];

        $user = $this->getUserByActivationCode($code);

        if (!$user) {
            return $response->withJson(['error' => 'Código de ativação inválido'], 400);
        }

        $user->setStatus('active');
        $user->setActivationCode(null);

        // Salvar usuário no banco de dados
        // ...

        // Retornar resposta
        return $response->withJson(['message' => 'Cadastro ativado com sucesso']);
    }

    public function refreshToken(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $refreshToken = $data['refresh_token'];
        $expireDate = $data['expire_date'];

        $refreshTokenDecoded = $this->decodeRefreshToken($refreshToken);

        $refreshTokenExists = $this->verifyRefreshToken($refreshToken);
        if (!$refreshTokenExists) {
            return $response->withStatus(401);
        }

        $usuario = $this->getUserByEmail($refreshTokenDecoded->email);
        if (is_null($usuario)) {
            return $response->withStatus(401);
        }

        $tokenPayload = [
            'sub' => $usuario->getId(),
            'name' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'expired_at' => $expireDate
        ];

        $token = $this->generateToken($usuario, $expireDate);
        $refreshToken = $this->generateRefreshToken($usuario);

        $tokenModel = new TokenModel();
        $tokenModel->setExpired_at($expireDate)
            ->setRefresh_token($refreshToken)
            ->setToken($token)
            ->setUsuarios_id($usuario->getId());

        $tokensDAO = new TokensDAO();
        $tokensDAO->createToken($tokenModel);

        $response = $response->withJson([
            "token" => $token,
            "refresh_token" => $refreshToken
        ]);

        return $response;
    }

    private function getUserByEmail(string $email)
    {
        $usuariosDAO = new UsuariosDAO();
        return $usuariosDAO->getUserByEmail($email);
    }

    private function getExpireDate()
    {
        date_default_timezone_set('America/Sao_Paulo');
        return date('Y-m-d H:i:s', strtotime('+60 minutes', strtotime(date('Y-m-d H:i:s'))));
    }

    private function generateToken($usuario, $expireDate)
    {
        $tokenPayload = [
            'sub' => $usuario->getId(),
            'name' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'exp' => (new DateTime($expireDate))->getTimestamp()
        ];

        return JWT::encode($tokenPayload, getenv('JWT_SECRET_KEY'));
    }

    private function generateRefreshToken($usuario)
    {
        $refreshTokenPayload = [
            'email' => $usuario->getEmail(),
            'ramdom' => uniqid()
        ];

        return JWT::encode($refreshTokenPayload, getenv('JWT_SECRET_KEY'));
    }

    private function getUserByActivationCode(string $code)
    {
        // Implementação da busca do usuário pelo código de ativação
        // ...
    }

    private function decodeRefreshToken(string $refreshToken)
    {
        return JWT::decode($refreshToken, getenv('JWT_SECRET_KEY'), ['HS256']);
    }

    private function verifyRefreshToken(string $refreshToken)
    {
        $tokensDAO = new TokensDAO();
        return $tokensDAO->verifyRefreshToken($refreshToken);
    }
}
