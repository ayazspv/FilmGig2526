-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 06, 2025 at 09:53 PM
-- Server version: 11.7.2-MariaDB-ubu2404
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `developmentdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `freelancers`
--

CREATE TABLE `freelancers` (
  `freelancerId` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `isProjectOwner` tinyint(1) DEFAULT 0,
  `portfolioLink` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gigs`
--

CREATE TABLE `gigs` (
  `gigId` int(11) NOT NULL,
  `projectId` int(11) DEFAULT NULL,
  `roleName` varchar(255) NOT NULL,
  `rateType` enum('hourly','fixed') NOT NULL,
  `payRate` decimal(10,2) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('active','closed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `gigs`
--

INSERT INTO `gigs` (`gigId`, `projectId`, `roleName`, `rateType`, `payRate`, `createdAt`, `status`) VALUES
(1, 1, 'Camera man', 'hourly', 25.00, '2025-04-06 10:34:24', 'active'),
(12, NULL, 'Editor', 'fixed', 34.00, '2025-04-06 23:01:36', 'active'),
(13, NULL, 'Production Designer', 'hourly', 60.00, '2025-04-06 23:03:45', 'closed'),
(14, NULL, 'Director', 'fixed', 540.00, '2025-04-06 23:04:50', 'active'),
(15, NULL, 'Sound Engineer', 'hourly', 150.00, '2025-04-06 23:05:07', 'closed'),
(16, NULL, 'Gaffer', 'fixed', 300.00, '2025-04-06 23:05:58', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `productionHouses`
--

CREATE TABLE `productionHouses` (
  `productionHouseId` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `companyName` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contactId` int(11) DEFAULT NULL,
  `contactEmail` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `productionHouses`
--

INSERT INTO `productionHouses` (`productionHouseId`, `userId`, `companyName`, `website`, `contactId`, `contactEmail`, `createdAt`) VALUES
(1, 10, 'Test PH', 'example.com', 9, NULL, '2025-04-06 10:33:22');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `projectId` int(11) NOT NULL,
  `ownerId` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `status` enum('in planning','in execution','closed') NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`projectId`, `ownerId`, `title`, `description`, `startDate`, `endDate`, `status`, `createdAt`) VALUES
(1, 10, 'project 1', 'des', NULL, NULL, 'in execution', '2025-04-06 10:33:54');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submissionId` int(11) NOT NULL,
  `gigId` int(11) DEFAULT NULL,
  `freelancerId` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL,
  `submittedAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`submissionId`, `gigId`, `freelancerId`, `status`, `submittedAt`) VALUES
(1, 1, 9, 'pending', '2025-04-06 13:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','productionHouse','freelance') NOT NULL,
  `kvkNr` int(8) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `name`, `email`, `password`, `role`, `kvkNr`, `createdAt`) VALUES
(10, 'Ayaz test 5', 'test@test.com', '$2y$12$SziAFoX3tI2R3GqZ.yxY1OjUfJeF/gXlTLIJUI9DIVqC4xBth12F6', 'productionHouse', 987654321, '2025-04-06 11:19:38'),
(13, 'Ayaz', 'ayaz@gmail.com', '$2y$12$JSE0v9Aa3L4Q9DvjoXx0KOdKepddnpHAaPI/VKaNyU/1w5M.DxVqW', 'freelance', 123456789, '2025-04-06 23:30:51'),
(14, 'James', 'james@gmail.com', '$2y$12$0GfwG3cqA8ik2/hqmSaVVu28hd9XZjjjb3uzvSK7MToTrdWdRzU3q', 'admin', 987654321, '2025-04-06 23:31:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `freelancers`
--
ALTER TABLE `freelancers`
  ADD PRIMARY KEY (`freelancerId`);

--
-- Indexes for table `gigs`
--
ALTER TABLE `gigs`
  ADD PRIMARY KEY (`gigId`),
  ADD KEY `projectId` (`projectId`);

--
-- Indexes for table `productionHouses`
--
ALTER TABLE `productionHouses`
  ADD PRIMARY KEY (`productionHouseId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `contactId` (`contactId`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`projectId`),
  ADD KEY `ownerId` (`ownerId`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submissionId`),
  ADD KEY `gigId` (`gigId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `freelancers`
--
ALTER TABLE `freelancers`
  MODIFY `freelancerId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gigs`
--
ALTER TABLE `gigs`
  MODIFY `gigId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `productionHouses`
--
ALTER TABLE `productionHouses`
  MODIFY `productionHouseId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `projectId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submissionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gigs`
--
ALTER TABLE `gigs`
  ADD CONSTRAINT `gigs_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`projectId`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`gigId`) REFERENCES `gigs` (`gigId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
