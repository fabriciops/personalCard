<?php
namespace App\Models\MySQL\personalCard;

final class PostagemModel
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $titulo;

    /**
     * @var string
     */
    private $texto;

    /**
     * @var string
     */
    private $dataPostagem;

    /**
     * @var int
     */
    private $usuarioId;

    private $usuario;

    public function setUsuario(UsuarioModel $usuario)
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }
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
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     * @return self
     */
    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;
        return $this;
    }

    /**
     * @return string
     */
    public function getTexto(): string
    {
        return $this->texto;
    }

    /**
     * @param string $texto
     * @return self
     */
    public function setTexto(string $texto): self
    {
        $this->texto = $texto;
        return $this;
    }

    /**
     * @return string
     */
    public function getDataPostagem(): string
    {
        return $this->dataPostagem;
    }

    /**
     * @param string $dataPostagem
     * @return self
     */
    public function setDataPostagem(string $dataPostagem): self
    {
        $this->dataPostagem = $dataPostagem;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    /**
     * @param int $usuarioId
     * @return self
     */
    public function setUsuarioId(int $usuarioId): self
    {
        $this->usuarioId = $usuarioId;
        return $this;
    }
}
