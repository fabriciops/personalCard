<?php

namespace App\DAO\MySQL\personalCard;


use App\Models\User;

class UserDaos extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllUsers(): array
    {
        $usuarios = $this->pdo
            ->query('SELECT
                    "*"
                FROM usuarios;')
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $usuarios;
    }

    public function insertUser($user)
    {
        $statement = $this->pdo
            ->prepare('INSERT INTO usuarios VALUES(
                null,
                :nome,
                :email,
                :senha,
                :status_conta
            )');
        $statement->execute([
            'nome' => $user->getNome(),
            'email' => $user->getEmail(),
            'senha' => $user->getSenha(),
            'status_conta' => $user->getstatus_conta()
        ]);
    }



}
