

CREATE DATABASE Cadastro_teste;

USE Cadastro_teste; 



CREATE TABLE cliente(
	id INTERGER not null auto_increment PRIMARY KEY, 
    nome varchar(200) not null, 
    tel varchar(45),
    dt_nasc date,
    email varchar(45),
    endereco varchar(45),
);

SELECT * FROM CLIENTE;



CREATE TABLE produto(
	id INTEGER PRIMARY KEY AUTOINCREMENT, 
    nome TEXT not null, 
    preco_compra DECIMAL,
    preco_venda DECIMAL,
    dt_cadastro datetime,
    qtd INTEGER,
    descricao TEXT
);

SELECT * FROM PRODUTO;

INSERT INTO cliente (nome, tel, dt_nasc, email, endereco) 
             VALUES ('Julia', '11960575432', '13/04/1998', 'juliapaixao41@gmail.com', 'rua');

INSERT INTO produto (nome, preco_compra, preco_venda, dt_cadastro, qtd, descricao) 
             VALUES ('PenDrive', '40,00', '60,00', '10/12/2024', '12', 'PenDrive 60MB');



CREATE TABLE pedido (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_cliente INTEGER,
    id_produto INTEGER,
    qtd INTEGER NOT NULL,
    data_pedido date NOT NULL,
    valor decimal,
    obs TEXT,
    cliente_id INTEGER,
    produto_id INTEGER,
    FOREIGN KEY (cliente_id) REFERENCES cliente(id),
    FOREIGN KEY (produto_id) REFERENCES produto(id)
);

CREATE TABLE venda (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    dt_venda DATE,
    total FLOAT,
    obs TEXT,
    cliente_id INTEGER,
    FOREIGN KEY (cliente_id) REFERENCES cliente(id)
);

CREATE TABLE venda_item (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    preco_venda float,
    qtd float,
    total float,
    venda_id INTEGER,
    produto_id INTEGER,
    FOREIGN KEY (venda_id) REFERENCES venda(id),
    FOREIGN KEY (produto_id) REFERENCES produto(id)
);