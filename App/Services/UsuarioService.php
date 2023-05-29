<?php

namespace App\Services;

use App\DAO\MySQL\personalCard\UsuariosDAO;
use App\Models\MySQL\personalCard\UsuarioModel;

class UsuarioService
{
    /** @var UsuariosDAO $usuariosDAO */
    private $usuariosDAO;

    public function __construct()
    {
        $this->usuariosDAO = new UsuariosDAO();
    }

    public function cadastrarUsuario(array $data)
    {
        // Check if email already exists
        $existingUser = $this->usuariosDAO->getUserByEmail($data['email']);
        if ($existingUser !== null) {
            return [
                'mensagem' => 'Este e-mail já está cadastrado, favor recupere sua senha.'
            ];
        }

        if ($data['idade'] < getenv('personalCard_IDADE_MINIMA')) {
            return [
                'mensagem' => "Usuário não possui idade igual ou superior a " . getenv('personalCard_IDADE_MINIMA')
            ];
        }

        $linkAtivacao = $this->gerarLinkAtivacao();

        $usuario = new UsuarioModel();
        $usuario->setNome($data['nome'])
            ->setEmail($data['email'])
            ->setSenha(password_hash($data['senha'], PASSWORD_ARGON2I))
            ->setIdade($data['idade'])
            ->setStatusConta('inativo')
            ->setCodigoAtivacao($linkAtivacao['codigoAtivacao']);

        $this->usuariosDAO->insertUser($usuario);

        return [
            'url_ativacao' => $linkAtivacao['link'],
            'codigoAtivacao' => $linkAtivacao['codigoAtivacao'],
            'mensagem' => 'Usuário cadastrado com sucesso'
        ];
    }

    private function gerarLinkAtivacao()
    {
        // Gerar um código único para o link de ativação
        $codigoAtivacao = bin2hex(random_bytes(16));

        // Retornar o link de ativação com o código
        return [
            'link' => getenv('personalCard_URL') . "ativarCadastro?codigo=" . $codigoAtivacao,
            'codigoAtivacao' => $codigoAtivacao,
        ];
    }

    private function gerarLinkAtivacaoCodigo($codigo)
    {
        // Retornar o link de ativação com o código
        return getenv('personalCard_URL') . "ativarCadastro?codigo=" . $codigo;

    }

    public function ativarCadastro(string $codigoAtivacao)
    {
        // Buscar usuário pelo código de ativação
        $usuario = $this->usuariosDAO->getUsuarioByCodigoAtivacao($codigoAtivacao);

        if ($usuario === null) {
            return [
                'mensagem' => 'Código de ativação inválido.'
            ];
        }

        // Ativar cadastro do usuário
        $this->usuariosDAO->ativarCadastro($usuario);

        return [
            'mensagem' => 'Cadastro ativado com sucesso.'
        ];
    }
}
