-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Creato il: Gen 09, 2024 alle 18:35
-- Versione del server: 8.2.0
-- Versione PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secure_book_selling_db`
--
CREATE DATABASE IF NOT EXISTS `secure_book_selling_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `secure_book_selling_db`;

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
  `ebook_name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `book`
--

INSERT INTO `book` (`id`, `title`, `author`, `publisher`, `price`, `category`, `stocks_number`, `ebook_name`) VALUES
(1, 'Harry Potter e la Pietra Filosofale', 'J.K. Rowling', 'Salani Editore', 21.99, 'Fantasy', 4, '1_unix.pdf'),
(2, 'Harry Potter ed il Principe Mezzosangue', 'J.K. Rowling', 'Salani Editore', 24.99, 'Fantasy', 8, '2_shell.pdf'),
(3, 'One Piece vol.102', 'Eiichiro Oda', 'Star Comics', 4.25, 'Manga', 10, '3_env-exploits.pdf'),
(4, 'To Kill a Mockingbird', 'Harper Lee', 'Harper Perennial', 14.99, 'Fiction', 15, '4_symlinks.pdf'),
(5, '1984', 'George Orwell', 'Penguin Books', 12.5, 'Dystopian', 20, '5_code-injection.pdf'),
(6, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Scribner', 17.99, 'Classic', 12, '6_brute-force.pdf'),
(7, 'The Catcher in the Rye', 'J.D. Salinger', 'Little, Brown and Company', 16.25, 'Fiction', 18, '7_canaries.pdf'),
(8, 'The Hobbit', 'J.R.R. Tolkien', 'Houghton Mifflin', 19.99, 'Fantasy', 25, '8_format-strings.pdf'),
(9, 'To the Lighthouse', 'Virginia Woolf', 'Harvest Books', 13.75, 'Modernist', 8, '9_dynamic-libraries.pdf'),
(10, 'Brave New World', 'Aldous Huxley', 'Harper Perennial Modern Classics', 15.5, 'Dystopian', 22, '10_non-exec-data.pdf'),
(11, 'Pride and Prejudice', 'Jane Austen', 'Penguin Classics', 11.99, 'Romance', 30, '11_code-reuse.pdf'),
(12, 'The Lord of the Rings', 'J.R.R. Tolkien', 'Houghton Mifflin', 29.99, 'Fantasy', 14, '12_aslr-pie.pdf'),
(13, 'The Odyssey', 'Homer', 'Penguin Classics', 10.75, 'Epic Poetry', 16, '13_heap.pdf'),
(14, 'The Road', 'Cormac McCarthy', 'Vintage Books', 18.25, 'Post-apocalyptic', 10, '14_pointers.pdf'),
(15, 'The Alchemist', 'Paulo Coelho', 'HarperOne', 14.5, 'Fiction', 12, '15_kernel.pdf');

-- --------------------------------------------------------

--
-- Struttura della tabella `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id_user` smallint NOT NULL,
  `id_book` smallint NOT NULL,
  `time` timestamp NOT NULL,
  `amount` float NOT NULL,
  `quantity` int NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  PRIMARY KEY (`id_user`, `id_book`, `time`),
  KEY `orders_book_id_fk` (`id_book`),
  KEY `orders_user_id_fk` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `orders`
--

INSERT INTO `orders` ( `id_user`, `id_book`, `time`, `amount`, `quantity`, `payment_method`) VALUES
(1, 2, now(), 24.99, 1, 'Card'),
(1, 3, now(), 3.95, 1, 'Card'),
(2, 3, now(), 4.25, 1, 'Card'),
(3, 1, now(), 19.99, 1, 'Card');

-- --------------------------------------------------------

--
-- Struttura della tabella `shopping_cart`
--

CREATE TABLE IF NOT EXISTS `shopping_cart` (
  `email` varchar(64) NOT NULL,
  `id_book` smallint NOT NULL,
  `title` varchar(64) NOT NULL,
  `author` varchar(64) NOT NULL,
  `publisher` varchar(64) NOT NULL,
  `price` float NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`email`,`id_book`),
  KEY `shopping_cart_book_id_fk` (`id_book`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `failedAccesses` smallint NOT NULL,
  `blockedUntil` timestamp NULL DEFAULT NULL,
  `otp` varchar(64) DEFAULT NULL,
  `lastOtp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unique_email` (`email`),
  UNIQUE KEY `user_unique_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `salt`, `email`, `name`, `surname`, `date_of_birth`, `isAdmin`, `failedAccesses`, `blockedUntil`, `otp`, `lastOtp`) VALUES
(1, 'Fablan', 'fc698653e8a0556f417fd5edb6b195126b152ec6c87dd1da70cc0db00d55dbb8', '5f1d635bb003ac40556ed6e9518984b081a011dadc1c1570d6bd32ad9e82584b', 'f.lanzillo@studenti.unipi.it', 'Fabrizio', 'Lanzillo', '1998-04-25', 0, 0, NULL, NULL, '2024-01-09 18:29:29'),
(2, 'Tommib', 'd0dc8824d9f9464e031c1081fa1f6abb85f8b2b27f53b5f2ca7cf80f15cb22f5', 'ecbada38d5adabc2dc3dc308888a8005ada39c4b693e80c2a42e07c7009c960b', 't.bertini4@studenti.unipi.it', 'Tommaso ', 'Bertini', '1998-04-01', 0, 0, NULL, NULL, '2024-01-09 18:29:29'),
(3, 'Hfjqpowfjpq', 'ae3460e953a01a24c393f0a2b0742c4353c72c1a87cb379130903566904a62c2', '616762931bda4395cf7b3f211496224f299fa05cabb020b97e2da943717d2ad7', 'g.marrucci4@studenti.unipi.it', 'Giovanni', 'Marrucci', '1999-11-29', 0, 0, NULL, NULL, '2024-01-09 18:29:29'),
(4, 'NperNedo', 'ecb961192524aefc85a70d169cf5dfa33aa44796de670bb034907ff64bd23074', '0ec28037a19b7098460a560dc3ebc6e171584eeba7ec092ec7f250f5f94a1114', 'f.montini1@studenti.unipi.it', 'Federico', 'Montini', '1998-05-17', 1, 0, NULL, NULL, '2024-01-09 18:29:29');

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_book_id_fk` FOREIGN KEY (`id_book`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_user_id_fk` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_book_id_fk` FOREIGN KEY (`id_book`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shopping_cart_user_email_fk` FOREIGN KEY (`email`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
