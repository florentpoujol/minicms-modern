-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Mar 04 Juillet 2017 à 10:35
-- Version du serveur :  5.7.14
-- Version de PHP :  7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `minicms_modern`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `creation_datetime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `categories`
--

INSERT INTO `categories` (`id`, `slug`, `title`, `creation_datetime`) VALUES
(1, 'category-1', 'Category 1', '2017-06-25 18:10:39'),
(5, 'category3', 'Category 3', '2017-06-25 18:51:50'),
(4, 'category-2', 'Category 2', '2017-06-25 18:28:40');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `comments`
--

INSERT INTO `comments` (`id`, `page_id`, `user_id`, `content`, `creation_datetime`, `post_id`) VALUES
(1, 5, 6, 'test comment\r\n1', '2017-01-01 00:00:00', NULL),
(2, 2, 2, 'comment 2', '2017-01-01 00:00:00', NULL),
(4, 11, 6, 'comment writer on sub 6\r\n', '2017-01-01 00:00:00', NULL),
(5, 11, 8, 'comenter on sub-6', '2017-01-01 00:00:00', NULL),
(7, 2, 8, 'commenter sub 1', '2017-01-01 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `medias`
--

CREATE TABLE `medias` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `medias`
--

INSERT INTO `medias` (`id`, `slug`, `filename`, `creation_datetime`, `user_id`) VALUES
(17, 'media-jpg', 'test800x600-media-jpg-2016-12-09.jpg', '2016-12-09 00:00:00', 2),
(18, 'media-pdf', 'test800x600-media-pdf-2016-12-09.pdf', '2016-12-09 00:00:00', 2),
(19, 'media-png', 'test800x600-media-png-2016-12-09.png', '2016-12-09 00:00:00', 2),
(20, 'media-zip', 'test800x600-media-zip-2016-12-09.zip', '2016-12-09 00:00:00', 6),
(22, 'media-jpeg', 'test800x600-media-jpeg-2016-12-09.jpeg', '2016-12-09 00:00:00', 6);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE `pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `parent_page_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  `allow_comments` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `content`, `parent_page_id`, `user_id`, `creation_datetime`, `published`, `allow_comments`) VALUES
(2, 'sub-1', 'sub 1', 'sub 1\r\n\r\n[manifesto media-jpg Vestibulum vestibulum, est viverra sagittis imperdiet, metus turpis finibus tellus, at aliquam est arcu id dui. Proin felis felis, ultrices nec turpis nec, lacinia tristique libero. <br>\r\nCurabitur ornare euismod pretium. Pellentesque commodo accumsan mi. Nunc gravida laoreet ligula, ac porta elit blandit quis. Nulla lorem urna, maximus eget interdum in, imperdiet et turpis. Mauris sed lectus vehicula nisl porttitor fringilla. Proin suscipit varius libero. Nam iaculis purus tempor orci vulputate aliquam. Nulla vitae justo faucibus, rhoncus justo tincidunt, ullamcorper turpis. Maecenas et lacus dignissim, condimentum est eu, ullamcorper sem. Nulla accumsan pulvinar diam, id viverra elit placerat sed. Aenean nec vulputate ligula, sed molestie risus.]', 5, 6, '2017-06-01 00:00:00', 1, 1),
(5, 'parent-1', 'parent 1', 'voici la liste de nos <strong>produits</strong>.\r\n<br>\r\n[img media-pnfgg 200]\r\n<br>\r\n[img media-jpg blabla]\r\n<br>\r\n[img media-jpeg bli bli]\r\n<br>\r\n[img media-jpg]\r\n<br>\r\n[img media-jpg title="blabla" alt="blibli" height="100px" width="300px"]', NULL, 6, '2016-12-05 00:00:00', 1, 1),
(6, 'carousel', 'Carousel', 'sub 2 <br>\r\n[carousel media-jpg media-jpeg media-png]', 5, 6, '2016-12-05 00:00:00', 1, 0),
(7, 'parent-2', 'parent 2', 'parent 2', NULL, 2, '2016-12-10 00:00:00', 1, 0),
(8, 'sub-3', 'sub 3', 'test', 7, 2, '2016-12-10 00:00:00', 1, 0),
(9, 'sub-4', 'Sub 4', 'sub 4', 7, 2, '2016-12-10 00:00:00', 0, 0),
(10, 'parent-darft', 'parent darft', 'parent darft', NULL, 2, '2016-12-10 00:00:00', 1, 0),
(11, 'sub-6', 'sub 6', 'sub 6', 10, 2, '2016-12-10 00:00:00', 1, 1),
(12, 'parent-4', 'parent 4', 'parent 4', NULL, 6, '2016-12-10 00:00:00', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  `allow_comments` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_token` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_token` varchar(255) DEFAULT NULL,
  `password_change_time` int(11) UNSIGNED DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `is_blocked` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_token`, `password_hash`, `password_token`, `password_change_time`, `role`, `creation_datetime`, `is_blocked`) VALUES
(2, 'Admin', 'florentpoujol@fastmail.com', '', '$2y$10$zfbhDR0GZWF5J10p8KToveJFMwWoXnxtaLNcfS/qXfqc/yBvE/HS6', '48292c5d87bed7bb3329268b0eb2c57e01217ca2', 1495357523, 'admin', '2016-11-27 00:00:00', 0),
(6, 'Writer', 'florent.poujol@gmail.com', '', '$2y$10$paY1NmbV7tUgkgGecchFDeYmj/dUaj0elb9N4cNQVVGfAJC2ax1Ue', '', 0, 'writer', '2016-11-30 00:00:00', 0),
(8, 'commenter', 'poujol.florent@wanadoo.fr', '', '$2y$10$vsYCo5XMrJ/NmmLj8JqJw.ts1J0ogE/gRYXXtB1Fc632EAysqrWKy', '', 0, 'commenter', '2017-04-17 00:00:00', 0);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `medias`
--
ALTER TABLE `medias`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `medias`
--
ALTER TABLE `medias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;
--
-- AUTO_INCREMENT pour la table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
