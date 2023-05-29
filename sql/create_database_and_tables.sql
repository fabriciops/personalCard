CREATE DATABASE personalCard CHARACTER SET utf8 COLLATE utf8_general_ci;

USE personalCard;

CREATE TABLE lojas (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(13) NOT NULL,
    endereco VARCHAR(200) NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE produtos (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    loja_id INT UNSIGNED NOT NULL,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL NOT NULL,
    quantidade INT UNSIGNED NOT NULL,
    PRIMARY KEY(id),
    CONSTRAINT fk_produtos_loja_id_lojas_id
		FOREIGN KEY (loja_id) REFERENCES lojas(id)
);
