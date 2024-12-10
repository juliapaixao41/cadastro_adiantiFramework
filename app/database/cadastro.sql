

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