-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 31 jan. 2024 à 19:30
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `php_exam_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_auteur_id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` double NOT NULL,
  `date_publication` datetime NOT NULL,
  `quantite_en_stock` int NOT NULL,
  `lien_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_23A0E66E08ED3C1` (`id_auteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id`, `id_auteur_id`, `nom`, `description`, `prix`, `date_publication`, `quantite_en_stock`, `lien_image`) VALUES
(1, 2, 'Velo', 'A vendre vélo tout neuf', 50, '2024-01-31 19:11:48', 1, 'velo-65ba9b74031af.jpg'),
(2, 2, 'Superman', 'A vendre figurine superman de collection', 70, '2024-01-31 19:12:32', 2, 'superman-65ba9ba0489d3.jpg'),
(3, 3, 'T-shirt Santa cruz', 'A vendre t-shirt Santa Cruz peu utilisé', 20, '2024-01-31 19:16:23', 2, 'santacruz-65ba9c87303a6.jpg'),
(4, 3, 'Skate', 'A vendre Skate de la marche fucking awesome', 70, '2024-01-31 19:18:38', 1, 'skate-65ba9d0e1ca8a.jpg'),
(5, 4, 'Pc Portable gamer', 'Voir reférence sur https://www.boulanger.com/ref/1194130', 600, '2024-01-31 19:22:56', 1, 'MSI-GF76-12UGS-616FR-Katana-3-65ba9e10d9587.jpg'),
(6, 4, 'Piano', 'Piano droit de la marque Yamaha', 600, '2024-01-31 19:24:57', 1, 'piano-65ba9e89d022e.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user_id` int NOT NULL,
  `id_article_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BA388B779F37AE5` (`id_user_id`),
  KEY `IDX_BA388B7D71E064B` (`id_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id_id` int NOT NULL,
  `date_transaction` datetime NOT NULL,
  `montant` decimal(10,0) NOT NULL,
  `adresse_facturation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville_facturation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_postal_facturation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_906517449D86650F` (`user_id_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id_id` int NOT NULL,
  `nombre_article_stock` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4B3656608F3EC46` (`article_id_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id`, `article_id_id`, `nombre_article_stock`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 2),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solde` double DEFAULT NULL,
  `photo_profil` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `username`, `solde`, `photo_profil`) VALUES
(1, 'baptisterichetin@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$yu1gxgxv6xvuDbZ.80pIsOFL8Yl.3C.9.K0XmOlr/7gMv1hTl73fa', 'Baptiste', 0, 'over icon-65ba99e412a5c.png'),
(2, 'test@test.test', '[\"ROLE_USER\"]', '$2y$13$RzU.kqFBjpl9CrEUYoe6Uui1zPi5UTBlM2KJAk3qfkhZZmWaYwux2', 'test', 0, 'frankulin-65ba9a80e4afc.jpg'),
(3, 'pierre@pierre.pierre', '[\"ROLE_USER\"]', '$2y$13$YRBBmtABCYYU3cnC1dFEAeU8GGrcqSpHOwIZuGJarh4/1e.x2yAb6', 'pierre', 0, 'quack skate v2-65ba9bd9eaaf9.png'),
(4, 'enzo@enzo.enzo', '[\"ROLE_USER\"]', '$2y$13$OBB.Z4kVrZ0SLGKVis.Vb.IfWcqu2XXSK4.QxdgQQURrgOgws0mQS', 'enzo', 200, 'chat-65ba9d7e94f0a.jpg');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `FK_23A0E66E08ED3C1` FOREIGN KEY (`id_auteur_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `FK_BA388B779F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_BA388B7D71E064B` FOREIGN KEY (`id_article_id`) REFERENCES `article` (`id`);

--
-- Contraintes pour la table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `FK_906517449D86650F` FOREIGN KEY (`user_id_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `FK_4B3656608F3EC46` FOREIGN KEY (`article_id_id`) REFERENCES `article` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
