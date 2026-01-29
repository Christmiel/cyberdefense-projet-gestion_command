-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 28 jan. 2026 à 09:37
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `commandes`
--

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `sexe` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `marque` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `date_livraison` date NOT NULL,
  `commentaire` text NOT NULL,
  `created_at` date NOT NULL,
  `statut` enum('En attente','Traitée','Livrée') NOT NULL DEFAULT 'En attente',
  `role` enum('admin','manager','user') NOT NULL DEFAULT 'user',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `nom`, `prenom`, `sexe`, `age`, `email`, `marque`, `adresse`, `date_livraison`, `commentaire`, `created_at`, `statut`, `role`, `password`) VALUES
(7, 'Djohi', 'Alida', 'femme', 30, 'djohi@gmail.com', 'aucun', 'Ab-Cal', '2026-01-29', 'aucun', '2026-01-28', 'Livrée', 'admin', '$2y$10$3u8pmZ6YyF9xO1AwrzcLGOPSycmsgywr784ulTtuqR6Ly8ZRYQrSe'),
(8, 'DOE', 'John', 'homme', 30, 'doe@gmail.com', 'Iphone', 'Ab-Cal', '2026-01-31', 'Aucun', '2026-01-28', 'En attente', 'manager', '$2y$10$20guCnpArT9SfPNGwkNbbOyhy4jv16M9unb9QwZyMTMAapRLrwEjG'),
(9, 'fi', 'ila', 'femme', 18, 'dkj@gmail.com', 'iphone', '65131', '2026-01-29', 'njmkl', '2026-01-28', 'Traitée', 'user', '$2y$10$mv.7VOBQ2w85pVv4tjcPuuIppQaDGOH1qnu51QOzZMjOWTRYX3XVO');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
