-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 
-- Généré le :  jeu. 24 oct. 2019 à 20:03
-- Version du serveur :  5.6.43-log
-- Version de PHP :  7.0.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  
--

-- --------------------------------------------------------

--
-- Structure de la table `INVITATION`
--

CREATE TABLE `INVITATION` (
  `MEMBER_ID` int(11) NOT NULL,
  `PROJECT_ID` int(11) NOT NULL,
  `DATE` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `KEY` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `INVITATION`
--
ALTER TABLE `INVITATION`
  ADD PRIMARY KEY `PROJECT_MEMBER_ID` (`MEMBER_ID`,`PROJECT_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
