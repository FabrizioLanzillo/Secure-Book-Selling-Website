-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Creato il: Dic 02, 2023 alle 19:00
-- Versione del server: 8.2.0
-- Versione PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS secure_book_selling_db;
-- CREATE USER 'SNH'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'SNH'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

use secure_book_selling_db;

--
-- Database: `secure_book_selling_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `book`
--

CREATE TABLE IF NOT EXISTS `book` (
  `id` smallint NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `author` varchar(64) DEFAULT NULL,
  `publisher` varchar(64) DEFAULT NULL,
  `price` float NOT NULL,
  `category` varchar(64) DEFAULT NULL,
  `stocks_number` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `book`
--

INSERT INTO `book` (`id`, `title`, `author`, `publisher`, `price`, `category`, `stocks_number`) VALUES
(1, 'Harry Potter e la PIetra Filosofale', 'J.K. Rowling', 'Salani Editore', 21.99, 'Fantasy', 4),
(2, 'Harry Potter ed il Principe Mezzosangue', 'J.K. Rowling', 'Salani Editore', 24.99, 'Fantasy', 8),
(3, 'One Piece vol.102', 'Eiichiro Oda', 'Star Comics', 4.25, 'Manga', 10);

-- --------------------------------------------------------

--
-- Struttura della tabella `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` smallint NOT NULL AUTO_INCREMENT,
  `id_user` smallint NOT NULL,
  `id_book` smallint NOT NULL,
  `amount` float NOT NULL,
  `status` smallint NOT NULL,
  `payment_method` smallint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_book_id_fk` (`id_book`),
  KEY `orders_user_id_fk` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `orders`
--

INSERT INTO `orders` (`id`, `id_user`, `id_book`, `amount`, `status`, `payment_method`) VALUES
(1, 1, 2, 24.99, 2, 0),
(2, 1, 3, 3.95, 1, 0),
(3, 2, 3, 4.25, 2, 0),
(4, 3, 1, 19.99, 0, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` smallint NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `surname` varchar(64) NOT NULL,
  `date_of_birth` date NOT NULL,
  `isAdmin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unique_email` (`email`),
  UNIQUE KEY `user_unique_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `name`, `surname`, `date_of_birth`, `isAdmin`) VALUES
(1, 'Fablan', 'prova', 'f.lanzillo@studenti.unipi.it', 'Fabrizio', 'Lanzillo', '1998-04-25', 1),
(2, 'Tommib', 'a800b7790368425cd3f341a67d86b4906232ad4e681531cff2766694c700b358', 't.bertini4@studenti.unipi.it', 'Tommaso ', 'Bertini', '1998-04-01', 1),
(3, 'Hfjqpowfjpq', 'cffa07d2ed62ecb040d57c6f4927f4672871c37bd7cbb09840529f7a306bd109', 'g.marrucci4@studenti.unipi.it', 'Giovanni', 'Marrucci', '1999-11-29', 1),
(4, 'NperNedo', '25af0924cf998b6c8c31f87999df481dc51ab94d92f81baee43a02aa5cdafb86', 'f.montini1@studenti.unipi.it', 'Federico', 'Montini', '1998-05-17', 0);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_book_id_fk` FOREIGN KEY (`id_book`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_user_id_fk` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
