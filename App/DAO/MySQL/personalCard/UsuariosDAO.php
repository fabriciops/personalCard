<?php

namespace App\DAO\MySQL\personalCard;

use App\Models\MySQL\personalCard\LojaModel;
use App\Models\MySQL\personalCard\UsuarioModel;

class UsuariosDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUserByEmail(string $email): ?UsuarioModel
    {
        $statement = $this->pdo
            ->prepare('SELECT
                    id,
                    nome,
                    email,
                    senha,
                    statusConta,
                    codigoAtivacao
                FROM usuarios
                WHERE email = :email;
            ');
        $statement->bindParam('email', $email);
        $statement->execute();
        $usuarios = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if(count($usuarios) === 0)
            return null;
        $usuario = new UsuarioModel();
        $usuario->setId($usuarios[0]['id'])
            ->setNome($usuarios[0]['nome'])
            ->setEmail($usuarios[0]['email'])
            ->setStatusConta($usuarios[0]['statusConta'])
            ->setCodigoAtivacao($usuarios[0]['codigoAtivacao'])
            ->setSenha($usuarios[0]['senha']);
        return $usuario;
    }

    public function insertUser($usuario)
    {
        try{
            $sql = "INSERT INTO usuarios (nome, email, senha, idade, statusConta, codigoAtivacao)
                    VALUES (:nome, :email, :senha, :idade, :statusConta, :codigoAtivacao)";

            $statement = $this->pdo->prepare($sql);

            $statement->bindValue(':nome', $usuario->getNome());
            $statement->bindValue(':email', $usuario->getEmail());
            $statement->bindValue(':senha', $usuario->getSenha());
            $statement->bindValue(':idade', $usuario->getIdade());
            $statement->bindValue(':statusConta', $usuario->getStatusConta());
            $statement->bindValue(':codigoAtivacao', $usuario->getCodigoAtivacao());
            $statement->execute();

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getUsuarioByCodigoAtivacao(string $codigoAtivacao): ?UsuarioModel
    {
        $statement = $this->pdo
            ->prepare('SELECT
                    id,
                    nome,
                    email,
                    senha,
                    idade,
                    statusConta,
                    codigoAtivacao
                FROM usuarios
                WHERE codigoAtivacao = :codigoAtivacao;
            ');
        $statement->bindParam('codigoAtivacao', $codigoAtivacao);
        $statement->execute();
        $usuarios = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($usuarios) === 0) {
            return null;
        }
        $usuario = new UsuarioModel();
        $usuario->setId($usuarios[0]['id'])
            ->setNome($usuarios[0]['nome'])
            ->setEmail($usuarios[0]['email'])
            ->setSenha($usuarios[0]['senha'])
            ->setIdade($usuarios[0]['idade'])
            ->setStatusConta($usuarios[0]['statusConta'])
            ->setCodigoAtivacao($usuarios[0]['codigoAtivacao']);
        return $usuario;
    }

    public function ativarCadastro(UsuarioModel $usuario): bool
    {
        try {
            $sql = "UPDATE usuarios
                    SET statusConta = 'ativo'
                    WHERE id = :id";

            $statement = $this->pdo->prepare($sql);

            $statement->bindValue(':id', $usuario->getId());

            $statement->execute();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }



}
