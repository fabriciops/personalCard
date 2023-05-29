<?php

namespace App\Models\MySQL\personalCard;

final class UsuarioModel
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $nome;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $senha;

    /**
     * @var int
     */
    private $idade;

    /**
     * @var string
     */
    private $statusConta;

    /**
     * @var string
     */
    private $codigoAtivacao;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return self
     */
    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenha(): string
    {
        return $this->senha;
    }

    /**
     * @param string $senha
     * @return self
     */
    public function setSenha(string $senha): self
    {
        $this->senha = $senha;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatusConta(): string
    {
        return $this->statusConta;
    }

    /**
     * @param string $statusConta
     * @return self
     */
    public function setStatusConta(string $statusConta): self
    {
        $this->statusConta = $statusConta;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoAtivacao(): string
    {
        return $this->codigoAtivacao;
    }

    /**
     * @param string $codigoAtivacao
     * @return self
     */
    public function setCodigoAtivacao(string $codigoAtivacao): self
    {
        $this->codigoAtivacao = $codigoAtivacao;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdade(): string
    {
        return $this->idade;
    }

    /**
     * @param int $idade
     * @return self
     */
    public function setIdade(string $idade): self
    {
        $this->idade = $idade;
        return $this;
    }
}
