-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 24 sep. 2024 à 18:22
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_stock`
--

-- --------------------------------------------------------

--
-- Structure de la table `fiches_stock`
--

DROP TABLE IF EXISTS `fiches_stock`;
CREATE TABLE IF NOT EXISTS `fiches_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `quantite` int DEFAULT NULL,
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

DROP TABLE IF EXISTS `fournisseurs`;
CREATE TABLE IF NOT EXISTS `fournisseurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `adresse` text,
  `contact` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `fournisseurs`
--

INSERT INTO `fournisseurs` (`id`, `nom`, `adresse`, `contact`) VALUES
(17, 'Akpona yao bienvenue', 'lome', '98801667'),
(16, 'AGLAN Yao Elom Elom', 'lome', '98801667');

-- --------------------------------------------------------

--
-- Structure de la table `mouvements`
--

DROP TABLE IF EXISTS `mouvements`;
CREATE TABLE IF NOT EXISTS `mouvements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int NOT NULL,
  `type_mouvement` enum('entrée','sortie') NOT NULL,
  `quantite` int NOT NULL,
  `date_mouvement` date DEFAULT NULL,
  `fournisseur_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `mouvements`
--

INSERT INTO `mouvements` (`id`, `produit_id`, `type_mouvement`, `quantite`, `date_mouvement`, `fournisseur_id`) VALUES
(1, 65, 'entrée', 15, '2024-09-21', 17),
(2, 64, 'entrée', 40, '2024-09-21', 16),
(3, 63, 'entrée', 45, '2024-09-21', 16),
(4, 62, 'entrée', 24, '2024-09-21', 16),
(5, 61, 'entrée', 30, '2024-09-21', 16),
(6, 66, 'entrée', 25, '2024-09-21', 17);

-- --------------------------------------------------------

--
-- Structure de la table `mouvements_stock`
--

DROP TABLE IF EXISTS `mouvements_stock`;
CREATE TABLE IF NOT EXISTS `mouvements_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `type_mouvement` enum('ajout','retrait','vente') NOT NULL,
  `quantite` int NOT NULL,
  `date_mouvement` datetime DEFAULT CURRENT_TIMESTAMP,
  `commentaire` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

DROP TABLE IF EXISTS `produits`;
CREATE TABLE IF NOT EXISTS `produits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text,
  `quantite` int NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `type_vente` varchar(50) DEFAULT NULL,
  `fournisseur_id` int DEFAULT NULL,
  `prix_unitaire` decimal(10,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `quantite`, `prix`, `type_vente`, `fournisseur_id`, `prix_unitaire`, `stock`) VALUES
(65, 'Dubonet', 'vin', 3, 4500.00, 'Unité', 17, NULL, 0),
(64, 'Duchesse', 'alocool', 40, 25000.00, 'Unité', 17, NULL, 0),
(63, 'peak lait ', 'lait', 20, 500.00, 'Unité', 17, NULL, 0),
(62, 'Coca-cola', 'Sucrerie', 24, 250.00, 'unite', 16, NULL, 0),
(61, 'rush', 'energisante', 27, 300.00, 'unite', 16, NULL, 0),
(66, 'Sprite', 'Sucrerie', 23, 200.00, 'unite', 17, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

DROP TABLE IF EXISTS `ventes`;
CREATE TABLE IF NOT EXISTS `ventes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int NOT NULL,
  `quantite` int NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `date_vente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `prix` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ventes`
--

INSERT INTO `ventes` (`id`, `produit_id`, `quantite`, `prix_total`, `date_vente`, `prix`) VALUES
(15, 66, 1, 200.00, '2024-09-21 00:00:00', NULL),
(16, 61, 3, 900.00, '2024-09-21 00:00:00', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
