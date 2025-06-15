-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 15 juin 2025 à 16:46
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
-- Base de données : `portfolio_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_size` varchar(50) DEFAULT NULL,
  `product_quantity` int(11) DEFAULT NULL,
  `product_title` varchar(255) DEFAULT NULL,
  `product_total` decimal(10,2) DEFAULT NULL,
  `status` enum('confirmed','canceled') DEFAULT 'confirmed',
  `color` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `diameter` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `product_id`, `customer_name`, `customer_phone`, `customer_address`, `order_date`, `product_size`, `product_quantity`, `product_title`, `product_total`, `status`, `color`, `type`, `diameter`) VALUES
(1, 3, 'فارس قويدرجلول', '07854985265', 'لاكادات', '2025-04-28 22:04:54', NULL, 1, 'التاست', 125468.00, 'confirmed', 'اصفر ', 'لا اعلم', '3سم'),
(2, 6, 'فارس قويدرجلول', '07854985265', 'لاكادات', '2025-04-30 04:27:47', NULL, 1, 'التاست', 1245.00, 'confirmed', ' ابيض  ', 'لا اعلم', '25 سم'),
(4, 7, 'فارس قويدرجلول', '07854985265', 'لاكادات', '2025-04-30 15:12:32', NULL, 4, 'المنتج الاول', 12000.00, 'confirmed', ' الاحمر', 'نوع خاص', '25 سم'),
(5, 8, 'فارس قويدرجلول', '07854985265', 'لاكادات', '2025-04-30 15:12:32', NULL, 2, 'المنتج 02', 10850.00, 'confirmed', ' برتقالي', '', ''),
(8, 13, 'test', '154885552', 'test ann', '2025-05-05 13:28:08', NULL, 15478, NULL, NULL, 'canceled', '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `pickup_offices`
--

CREATE TABLE `pickup_offices` (
  `id` int(11) NOT NULL,
  `wilaya` varchar(100) NOT NULL,
  `office_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_type` varchar(50) NOT NULL,
  `image_urls` text NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `color` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `diameter` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `shipping_prices`
--

CREATE TABLE `shipping_prices` (
  `id` int(11) NOT NULL,
  `wilaya` varchar(100) NOT NULL,
  `door_to_door_price` decimal(10,2) NOT NULL,
  `office_pickup_price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `pickup_offices`
--
ALTER TABLE `pickup_offices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wilaya` (`wilaya`);

--
-- Index pour la table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `shipping_prices`
--
ALTER TABLE `shipping_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wilaya` (`wilaya`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `pickup_offices`
--
ALTER TABLE `pickup_offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `shipping_prices`
--
ALTER TABLE `shipping_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `pickup_offices`
--
ALTER TABLE `pickup_offices`
  ADD CONSTRAINT `pickup_offices_ibfk_1` FOREIGN KEY (`wilaya`) REFERENCES `shipping_prices` (`wilaya`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
