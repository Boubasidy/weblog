-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 01 juin 2025 à 14:44
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `auth`
--

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `slug`, `views`, `image`, `body`, `published`, `created_at`, `updated_at`) VALUES
(2, 1, 'Second post', 'second-post', 0, 'banner.jpg', 'This is the body of the second post on this site', 0, '2018-02-02 10:40:14', '2018-02-01 12:04:36'),
(10, 17, 'Bienvenue à Angers', 'bienvenue-angers', 0, 'Capture d’écran 2025-03-31 à 23.42.11 (2).png', '0', 1, '2025-05-25 15:20:01', '2025-05-25 15:20:01'),
(17, 17, 'Les conséquences de l\'utilisation des réseaux sociaux', 'les-cons-quences-de-l-utilisation-des-r-seaux-sociaux', 0, 'Capture 4.png', '0', 1, '2025-05-25 15:22:11', '2025-05-25 15:22:11');

-- --------------------------------------------------------

--
-- Structure de la table `post_topic`
--

CREATE TABLE `post_topic` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post_topic`
--

INSERT INTO `post_topic` (`id`, `post_id`, `topic_id`) VALUES
(2, 2, 2),
(3, 10, 3),
(5, 17, 3);

-- --------------------------------------------------------

--
-- Structure de la table `post_user`
--

CREATE TABLE `post_user` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post_user`
--

INSERT INTO `post_user` (`post_id`, `user_id`) VALUES
(10, 31),
(17, 20),
(2, 20);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Author'),
(3, 'Subscriber');

-- --------------------------------------------------------

--
-- Structure de la table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(17, 1),
(19, 1),
(20, 2),
(25, 3),
(27, 3),
(31, 2),
(32, 3),
(33, 3);

-- --------------------------------------------------------

--
-- Structure de la table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `topics`
--

INSERT INTO `topics` (`id`, `name`, `slug`) VALUES
(1, 'Inspiration', 'inspiration'),
(2, 'Motivation', 'motivation'),
(3, 'Journal', 'journal'),
(4, 'conseil', 'conseil');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'matthieu', 'info@sti.com', '81dc9bdb52d04dc20036dbd8313ed055', '2018-01-08 11:52:58', '2018-01-08 11:52:58'),
(17, 'user', 'user@gmail.com', '$2y$10$cvI0U9.mkQA4wr7CNc51le4zQhvPdkTcJjb2S8JG5lnCpKwJ.l8n6', '2025-05-20 07:39:17', NULL),
(19, 'dev2', 'dev@gmail.com', '$2y$10$gtB9yG667p3uFYWjLbyOLei9mSDi95DTV1vXsleYkpOXxhFkNbXta', '2025-05-23 14:30:14', NULL),
(20, 'youssef', 'youssef@gmail.com', '$2y$10$MiPGu8dYvGn2GGGWdZsC0.Y.ibFk3r.howU1qi/swlkrXDmOhRg12', '2025-05-23 15:16:11', NULL),
(24, 'abonne1', 'ab@gmail.com', '$2y$10$MJSFCaTXULnS3kIMnHoMxu5SX.r1cGDkX/Za.o9e9WKt23lfm9D2S', '2025-05-26 11:15:40', NULL),
(25, 'abonne', 'abonne@gmail.com', '$2y$10$3qpbZfearenNQUUCQZPyoeVEJPdsq6SjEiwstPehE51wNDQYhc7i6', '2025-05-26 22:17:22', NULL),
(27, 'Bouba', 'boubasidy18@gmail.com', '$2y$10$PK/6n1NYo8fb4iZWKDItKOSZYrBPkYtv2qeJdPY02R9acXdwRDITS', '2025-05-26 23:06:03', NULL),
(31, 'marc', 'marc@gmail.com', '$2y$10$ckWKW.JeauwbbtyX3vamhuqAGPAfudJtSSeUrYyWPEgU1MKFI71IO', '2025-05-27 07:41:40', NULL),
(32, 'jack', 'jack@gmail.com', '$2y$10$j/GbUQJD38cwJAVHBrxrse87z7ZU2wLVD.2seyXUOBxJ19g3z9tee', '2025-05-27 08:56:17', NULL),
(33, 'pierre', 'pierre@gmail.com', '$2y$10$MTDVDaqk/foTSo2wKndSxeBVPchoBGFbqdUDHoXMrgWgi3tztO8OC', '2025-05-27 09:16:33', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `post_topic`
--
ALTER TABLE `post_topic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_POST_ID` (`post_id`),
  ADD KEY `FK_TOPIC_ID` (`topic_id`);

--
-- Index pour la table `post_user`
--
ALTER TABLE `post_user`
  ADD PRIMARY KEY (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Index pour la table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `post_topic`
--
ALTER TABLE `post_topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `post_topic`
--
ALTER TABLE `post_topic`
  ADD CONSTRAINT `FK_POST_ID` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TOPIC_ID` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `post_user`
--
ALTER TABLE `post_user`
  ADD CONSTRAINT `post_user_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
