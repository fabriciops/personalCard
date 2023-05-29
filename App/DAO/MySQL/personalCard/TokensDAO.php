<?php

namespace App\DAO\MySQL\personalCard;

use App\Models\MySQL\personalCard\TokenModel;

class TokensDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createToken(TokenModel $token): void
    {
        $statement = $this->pdo
            ->prepare('INSERT INTO tokens
                (
                    token,
                    refresh_token,
                    expired_at,
                    usuarios_id
                )
                VALUES
                (
                    :token,
                    :refresh_token,
                    :expired_at,
                    :usuarios_id
                );
            ');
        $statement->execute([
            'token' => $token->getToken(),
            'refresh_token' => $token->getRefresh_token(),
            'expired_at' => $token->getExpired_at(),
            'usuarios_id' => $token->getUsuarios_id()
        ]);
    }

    public function deleteTokenByToken(string $token): void
    {
        $statement = $this->pdo
            ->prepare('DELETE FROM tokens WHERE token = :token');
        $statement->bindParam('token', $token);
        $statement->execute();
    }

    public function verifyRefreshToken(string $refreshToken): bool
    {
        $statement = $this->pdo
            ->prepare('SELECT
                    id
                FROM tokens
                WHERE refresh_token = :refresh_token;
            ');
        $statement->bindParam('refresh_token', $refreshToken);
        $statement->execute();
        $tokens = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return count($tokens) === 0 ? false : true;
    }

    public function getUserIdFromToken(string $token): ?int
    {
        $statement = $this->pdo
            ->prepare('SELECT
                    usuarios_id
                FROM tokens
                WHERE token = :token;
            ');
        $statement->bindParam('token', $token);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return (int) $result['usuarios_id'];
    }
}
