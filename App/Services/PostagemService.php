<?php
namespace App\Services;

use App\DAO\MySQL\personalCard\PostagemDAO;
use App\Models\MySQL\personalCard\PostagemModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class PostagemService
{
    /** @var PostagemDAO */
    private $postagemDAO;

    private $logger;


    private const RETURN_MESSAGES = [
        201 => [
            'mensagem' => 'Requisição realizada com sucesso.',
        ],

        204 => [
            'mensagem' => 'Postagem excluida com sucesso',
        ],

        404 => [
            'mensagem' => 'Postagem não encontrada.',
            'codigo' => 404
        ],

        403 => [
            'mensagem' => 'Você não tem permissão para atualizar esta postagem.',
            'codigo' => 403
        ],

        500 => [
            'mensagem' => 'Erro no servidor.',
            'codigo' => 500

        ],
    ];

    public function __construct(PostagemDAO $postagemDAO)
    {
        $this->postagemDAO = $postagemDAO;
        $this->logger = new Logger('debug');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/debug.log', Logger::DEBUG));

    }


    public function getPaginatedPostagens(int $pagina, int $limite): array
    {
        return $this->postagemDAO->getPaginatedPostagens($pagina, $limite);
    }
    public function createPostagem(array $data, int $usuarioId): PostagemModel
    {
        $usuario = $usuarioId;

        $postagem = new PostagemModel();
        $postagem->setTitulo($data['titulo'])
            ->setTexto($data['texto'])
            ->setDataPostagem(date('Y-m-d H:i:s'))
            ->setUsuarioId($usuarioId);

        if($this->postagemDAO->insertPostagem($postagem)){
            return $postagem; // Retornar a instância de PostagemModel
        }

        throw new \Exception('Falha ao inserir a postagem.'); // Lançar uma exceção em caso de falha
    }

    public function getPostagemById(int $postId): ?PostagemModel
    {
        $postagem = $this->postagemDAO->getPostagemById($postId);

        if (!$postagem) {
            return $this->getMessage(404);
        }

        return $postagem;
    }

    private function getMessage(int $code): array
    {
        return self::RETURN_MESSAGES[$code] ?? '';
    }


    public function updatePostagem(int $postagemId, array $data, int $usuarioId): array
    {
        // Verificar se o usuário pode gerenciar postagens próprias
        $postagem = $this->postagemDAO->getPostagemById($postagemId);

        if (!$postagem) {
            return $this->getMessage(404);
        }

        if ($postagem->getUsuarioId() !== $usuarioId) {
            return $this->getMessage(403);
        }

        $postagem->setTitulo($data['titulo'])->setTexto($data['texto']);

        if ($this->postagemDAO->updatePostagem($postagem)) {
            return $this->getMessage(201);
        }

        return $this->getMessage(500);
    }


    public function deletePostagem(int $postagemId, int $usuarioId)
    {
        // Verificar se o usuário pode gerenciar postagens próprias
        $postagem = $this->postagemDAO->getPostagemById($postagemId);

        if (!$postagem) {
            return $this->getMessage(404);
        }

        if ($postagem->getUsuarioId() !== $usuarioId) {
            return $this->getMessage(403);

        }

        $this->postagemDAO->deletePostagem($postagemId);
        return $this->getMessage(204);

    }

}
