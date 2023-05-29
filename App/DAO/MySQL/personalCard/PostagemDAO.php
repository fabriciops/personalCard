<?php

namespace App\DAO\MySQL\personalCard;

use App\Models\MySQL\personalCard\PostagemModel;
use App\Models\MySQL\personalCard\UsuarioModel;


use PDO;

class PostagemDAO extends Conexao
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertPostagem(PostagemModel $postagem)
    {
        try {
            $statement = $this->pdo
            ->prepare('INSERT INTO postagens
                (
                    titulo,
                    texto,
                    data_postagem,
                    usuario_id
                )
                VALUES
                (
                    :titulo,
                    :texto,
                    :data_postagem,
                    :usuario_id
                );
            ');
            $statement->execute([
                'titulo' => $postagem->getTitulo(),
                'texto' => $postagem->getTexto(),
                'data_postagem' => $postagem->getDataPostagem(),
                'usuario_id' => $postagem->getUsuarioId()
            ]);

            return true;
        } catch (\Throwable $th) {
            return false;        }

    }

    public function updatePostagem(PostagemModel $postagem)
    {
        try {
            $statement = $this->pdo
            ->prepare('UPDATE postagens SET
                    titulo = :titulo,
                    texto = :texto,
                    data_postagem = :data_postagem,
                    usuario_id = :usuario_id
                WHERE id = :id;
            ');
            $statement->execute([
                'id' => $postagem->getId(),
                'titulo' => $postagem->getTitulo(),
                'texto' => $postagem->getTexto(),
                'data_postagem' => $postagem->getDataPostagem(),
                'usuario_id' => $postagem->getUsuarioId()
            ]);
            return true;

        } catch (\Throwable $th) {
            return false;
        }

    }

    public function deletePostagem(int $postId)
    {
        $statement = $this->pdo
            ->prepare('DELETE FROM postagens WHERE id = :id;
            ');
        $statement->execute([
            'id' => $postId
        ]);
    }

    public function getPostagemById(int $postId): ?PostagemModel
    {
        $statement = $this->pdo->prepare('
            SELECT p.*, u.nome AS nomeUsuario, u.email AS emailUsuario
            FROM postagens p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = :id
        ');
        $statement->execute(['id' => $postId]);
        $postagem = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$postagem) {
            return null;
        }

        $usuario = new UsuarioModel();
        $usuario->setId($postagem['usuario_id']);
        $usuario->setNome($postagem['nomeUsuario']);
        $usuario->setEmail($postagem['emailUsuario']);

        return (new PostagemModel())
            ->setId($postagem['id'])
            ->setTitulo($postagem['titulo'])
            ->setTexto($postagem['texto'])
            ->setDataPostagem($postagem['data_postagem'])
            ->setUsuarioId(intval($postagem['usuario_id']))
            ->setUsuario($usuario);
    }

    public function getPostagens(int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;

        $statement = $this->pdo
            ->prepare('SELECT * FROM postagens LIMIT :limit OFFSET :offset;
            ');
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $postagens = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($postagens as $postagem) {
            $result[] = (new PostagemModel())
                ->setId($postagem['id'])
                ->setTitulo($postagem['titulo'])
                ->setTexto($postagem['texto'])
                ->setDataPostagem($postagem['data_postagem'])
                ->setUsuarioId($postagem['usuario_id']);
        }

        return $result;
    }


    public function getPaginatedPostagens(int $pagina, int $limite): array
    {
        $offset = ($pagina - 1) * $limite;

        $statement = $this->pdo->prepare('
            SELECT p.*, u.nome AS nomeUsuario, u.email AS emailUsuario
            FROM postagens p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY p.data_postagem DESC
            LIMIT :limite OFFSET :offset
        ');
        $statement->bindValue('limite', $limite, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $postagens = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($postagens as $postagem) {
            $usuario = new UsuarioModel();
            $usuario->setId($postagem['usuario_id']);
            $usuario->setNome($postagem['nomeUsuario']);
            $usuario->setEmail($postagem['emailUsuario']);

            $result[] = (new PostagemModel())
                ->setId($postagem['id'])
                ->setTitulo($postagem['titulo'])
                ->setTexto($postagem['texto'])
                ->setDataPostagem($postagem['data_postagem'])
                ->setUsuarioId($postagem['usuario_id'])
                ->setUsuario($usuario);
        }

        return $result;
    }

}
