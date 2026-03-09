-- creazione database se non esiste
CREATE DATABASE IF NOT EXISTS 5cinf;

-- utilizzo del database
USE 5cinf;

-- tabella utenti
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL
);

-- inserimento dati di esempio
INSERT INTO users (nome, email) VALUES
("Mario Rossi","mario.rossi@gmail.com"),
("Frida Valecchi","frida.valecchi@gmail.com"),
("Luca Bianchi","luca.bianchi@gmail.com");