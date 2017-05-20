-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Sam 20 Mai 2017 à 09:07
-- Version du serveur :  5.7.14
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `minicms_mvc`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `creation_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `comments`
--

INSERT INTO `comments` (`id`, `page_id`, `user_id`, `text`, `creation_time`) VALUES
(1, 5, 6, 'test comment\r\n1', 0),
(2, 2, 2, 'comment 2', 1492440914),
(4, 11, 6, 'comment writer on sub 6\r\n', 1492546884),
(5, 11, 8, 'comenter on sub-6', 1492546964),
(7, 2, 8, 'commenter sub 1', 1492620477);

-- --------------------------------------------------------

--
-- Structure de la table `medias`
--

CREATE TABLE `medias` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `creation_date` date NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `medias`
--

INSERT INTO `medias` (`id`, `slug`, `filename`, `creation_date`, `user_id`) VALUES
(17, 'media-jpg', 'test800x600-media-jpg-2016-12-09.jpg', '2016-12-09', 2),
(18, 'media-pdf', 'test800x600-media-pdf-2016-12-09.pdf', '2016-12-09', 2),
(19, 'media-png', 'test800x600-media-png-2016-12-09.png', '2016-12-09', 2),
(20, 'media-zip', 'test800x600-media-zip-2016-12-09.zip', '2016-12-09', 6),
(22, 'media-jpeg', 'test800x600-media-jpeg-2016-12-09.jpeg', '2016-12-09', 6);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `session_id` varchar(255) NOT NULL
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
  `creation_date` date NOT NULL,
  `published` tinyint(4) NOT NULL,
  `allow_comments` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `content`, `parent_page_id`, `user_id`, `creation_date`, `published`, `allow_comments`) VALUES
(2, 'sub-1', 'sub 1', 'sub 1\r\n\r\n[manifesto media-jpg Vestibulum vestibulum, est viverra sagittis imperdiet, metus turpis finibus tellus, at aliquam est arcu id dui. Proin felis felis, ultrices nec turpis nec, lacinia tristique libero. <br>\r\nCurabitur ornare euismod pretium. Pellentesque commodo accumsan mi. Nunc gravida laoreet ligula, ac porta elit blandit quis. Nulla lorem urna, maximus eget interdum in, imperdiet et turpis. Mauris sed lectus vehicula nisl porttitor fringilla. Proin suscipit varius libero. Nam iaculis purus tempor orci vulputate aliquam. Nulla vitae justo faucibus, rhoncus justo tincidunt, ullamcorper turpis. Maecenas et lacus dignissim, condimentum est eu, ullamcorper sem. Nulla accumsan pulvinar diam, id viverra elit placerat sed. Aenean nec vulputate ligula, sed molestie risus.]', 5, 6, '0000-00-00', 1, 1),
(5, 'parent-1', 'parent 1', 'voici la liste de nos <strong>produits</strong>.\r\n<br>\r\n[img media-pnfgg 200]\r\n<br>\r\n[img media-jpg blabla]\r\n<br>\r\n[img media-jpeg bli bli]\r\n<br>\r\n[img media-jpg]\r\n<br>\r\n[img media-jpg title="blabla" alt="blibli" height="100px" width="300px"]', NULL, 6, '2016-12-05', 1, 1),
(6, 'carousel', 'Carousel', 'sub 2 <br>\r\n[carousel media-jpg media-jpeg media-png]', 5, 6, '2016-12-05', 1, 0),
(7, 'parent-2', 'parent 2', 'parent 2', NULL, 2, '2016-12-10', 1, 0),
(8, 'sub-3', 'sub 3', 'test', 7, 2, '2016-12-10', 1, 0),
(9, 'sub-4', 'Sub 4', 'sub 4', 7, 2, '2016-12-10', 0, 0),
(10, 'parent-darft', 'parent darft', 'parent darft', NULL, 2, '2016-12-10', 1, 0),
(11, 'sub-6', 'sub 6', 'sub 6', 10, 2, '2016-12-10', 1, 1),
(12, 'parent-4', 'parent 4', 'parent 4', NULL, 6, '2016-12-10', 1, 0);

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
  `creation_time` int(11) NOT NULL,
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
  `creation_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_token`, `password_hash`, `password_token`, `password_change_time`, `role`, `creation_date`) VALUES
(2, 'Admin', 'florentpoujol@fastmail.com', '', '$2y$10$zfbhDR0GZWF5J10p8KToveJFMwWoXnxtaLNcfS/qXfqc/yBvE/HS6', '', 0, 'admin', '2016-11-27'),
(6, 'Writer', 'florent.poujol@gmail.com', '', '$2y$10$FG9UGDAGugNl.ruXUEThX.zbGjefL8dJkSTr1zcOj.RX8xHUTeMHu', '', 0, 'writer', '2016-11-30'),
(8, 'commenter', 'poujol.florent@wanadoo.fr', '', '$2y$10$vsYCo5XMrJ/NmmLj8JqJw.ts1J0ogE/gRYXXtB1Fc632EAysqrWKy', '', 0, 'commenter', '2017-04-17'),
(9, 'florent', 'florent.poujol+1@gmail.com', '', '$2y$10$IpS6wpk.fvAf6bvPiZV2PeGz1AnTxQE08dMYtzTBTnU8hzcA88kwS', NULL, NULL, 'commenter', '2017-04-28');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;