-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Creato il: Dic 03, 2023 alle 22:59
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
(3, 'One Piece vol.102', 'Eiichiro Oda', 'Star Comics', 4.25, 'Manga', 10),
(4, 'To Kill a Mockingbird', 'Harper Lee', 'Harper Perennial', 14.99, 'Fiction', 15),
(5, '1984', 'George Orwell', 'Penguin Books', 12.50, 'Dystopian', 20),
(6, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Scribner', 17.99, 'Classic', 12),
(7, 'The Catcher in the Rye', 'J.D. Salinger', 'Little, Brown and Company', 16.25, 'Fiction', 18),
(8, 'The Hobbit', 'J.R.R. Tolkien', 'Houghton Mifflin', 19.99, 'Fantasy', 25),
(9, 'To the Lighthouse', 'Virginia Woolf', 'Harvest Books', 13.75, 'Modernist', 8),
(10, 'Brave New World', 'Aldous Huxley', 'Harper Perennial Modern Classics', 15.50, 'Dystopian', 22),
(11, 'Pride and Prejudice', 'Jane Austen', 'Penguin Classics', 11.99, 'Romance', 30),
(12, 'The Lord of the Rings', 'J.R.R. Tolkien', 'Houghton Mifflin', 29.99, 'Fantasy', 14),
(13, 'The Odyssey', 'Homer', 'Penguin Classics', 10.75, 'Epic Poetry', 16),
(14, 'The Road', 'Cormac McCarthy', 'Vintage Books', 18.25, 'Post-apocalyptic', 10),
(15, 'The Alchemist', 'Paulo Coelho', 'HarperOne', 14.50, 'Fiction', 12);

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
  `salt` varchar(64) NOT NULL,
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

INSERT INTO `user` (`id`, `username`, `password`, `salt`, `email`, `name`, `surname`, `date_of_birth`, `isAdmin`) VALUES
(1, 'Fablan', 'fc698653e8a0556f417fd5edb6b195126b152ec6c87dd1da70cc0db00d55dbb8', '5f1d635bb003ac40556ed6e9518984b081a011dadc1c1570d6bd32ad9e82584b', 'f.lanzillo@studenti.unipi.it', 'Fabrizio', 'Lanzillo', '1998-04-25', 0),
(2, 'Tommib', 'd0dc8824d9f9464e031c1081fa1f6abb85f8b2b27f53b5f2ca7cf80f15cb22f5', 'ecbada38d5adabc2dc3dc308888a8005ada39c4b693e80c2a42e07c7009c960b', 't.bertini4@studenti.unipi.it', 'Tommaso ', 'Bertini', '1998-04-01', 0),
(3, 'Hfjqpowfjpq', 'ae3460e953a01a24c393f0a2b0742c4353c72c1a87cb379130903566904a62c2', '616762931bda4395cf7b3f211496224f299fa05cabb020b97e2da943717d2ad7', 'g.marrucci4@studenti.unipi.it', 'Giovanni', 'Marrucci', '1999-11-29', 0),
(4, 'NperNedo', 'ecb961192524aefc85a70d169cf5dfa33aa44796de670bb034907ff64bd23074', '0ec28037a19b7098460a560dc3ebc6e171584eeba7ec092ec7f250f5f94a1114', 'f.montini1@studenti.unipi.it', 'Federico', 'Montini', '1998-05-17', 1);

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
