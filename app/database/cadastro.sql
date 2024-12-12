

CREATE DATABASE Cadastro_teste;

USE Cadastro_teste; 



CREATE TABLE cliente(
	id INTEGER PRIMARY KEY AUTOINCREMENT, 
    nome text, 
    tel varchar(45),
    dt_nasc date,
    email varchar(45),
    endereco varchar(45)
);

SELECT * FROM CLIENTE;



CREATE TABLE produto(
	id INTEGER PRIMARY KEY AUTOINCREMENT, 
    nome TEXT NULL, 
    preco_compra float,
    preco_venda float,
    dt_cadastro datetime,
    qtd float,
    descricao TEXT
);

SELECT * FROM PRODUTO;

INSERT INTO cliente (nome, tel, dt_nasc, email, endereco) 
             VALUES ('Julia', '11960575432', '13/04/1998', 'juliapaixao41@gmail.com', 'rua');

INSERT INTO produto (nome, preco_compra, preco_venda, dt_cadastro, qtd, descricao) 
             VALUES (NULL, '40,00', '60,00', '10/12/2024', '12', 'PenDrive 60MB');

INSERT INTO produto_novo (id, nome, preco_compra,preco_venda,dt_cadastro,qtd,descricao )
SELECT id, nome, preco_compra,preco_venda,dt_cadastro, qtd, descricao   FROM produto;




CREATE TABLE venda (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    dt_venda DATE,
    valor float,
    qtd float,
    obs TEXT,
    produto_id INTEGER,
    cliente_id INTEGER,
    FOREIGN KEY (produto_id) REFERENCES produto(id),
    FOREIGN KEY (cliente_id) REFERENCES cliente(id)
);

