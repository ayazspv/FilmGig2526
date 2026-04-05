-- FilmGig model-driven MariaDB schema
-- Import this file into phpMyAdmin to create the tables defined in app/src/Models.

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `submission`;
DROP TABLE IF EXISTS `gig`;
DROP TABLE IF EXISTS `freelancer`;
DROP TABLE IF EXISTS `productionHouse`;
DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','productionHouse','freelance') NOT NULL,
  `address` varchar(255),
  `bio` text,
  `kvkNr` int(8) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`userId`),
  UNIQUE KEY `uniq_users_email` (`email`),
  UNIQUE KEY `uniq_users_kvk` (`kvkNr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `productionHouse` (
  `productionHouseId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `companyName` varchar(255),
  `website` varchar(255),
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`productionHouseId`),
  KEY `idx_productionHouses_userId` (`userId`),
  CONSTRAINT `fk_productionHouses_userId` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `freelancer` (
  `freelancerId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `dateOfBirth` date,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`freelancerId`),
  KEY `idx_freelancers_userId` (`userId`),
  CONSTRAINT `fk_freelancers_userId` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gig` (
  `gigId` int(11) NOT NULL AUTO_INCREMENT,
  `ownerId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `startDate` date NOT NULL,
  `rateType` enum('hourly','fixed') NOT NULL,
  `payRate` decimal(10,2) NOT NULL,
  `status` enum('active','closed') NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`gigId`),
  KEY `idx_gigs_ownerId` (`ownerId`),
  CONSTRAINT `fk_gigs_ownerId` FOREIGN KEY (`ownerId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `submission` (
  `submissionId` int(11) NOT NULL AUTO_INCREMENT,
  `gigId` int(11) NOT NULL,
  `freelancerId` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL,
  `submittedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`submissionId`),
  KEY `idx_submissions_gigId` (`gigId`),
  KEY `idx_submissions_freelancerId` (`freelancerId`),
  CONSTRAINT `fk_submissions_gigId` FOREIGN KEY (`gigId`) REFERENCES `gig` (`gigId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_submissions_freelancerId` FOREIGN KEY (`freelancerId`) REFERENCES `freelancer` (`freelancerId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET foreign_key_checks = 1;
COMMIT;
