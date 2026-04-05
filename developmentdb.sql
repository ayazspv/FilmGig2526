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

INSERT INTO `user` (`userId`, `username`, `name`, `email`, `password`, `role`, `address`, `bio`, `kvkNr`, `createdAt`) VALUES
(1, 'filmgig_admin', 'FilmGig Studios BV', 'admin@filmgig.nl', '$2y$12$wH0YxjQ9JpY5j6n9Q8m2C.3g6pV8gDg1nJx7b5Q3t5x5G4f6m2y4O', 'productionHouse', 'Rotterdam Media Park, NL', 'Niche film production marketplace.', 12345678, '2026-04-05 09:00:00'),
(2, 'samira_pro', 'Samira Jansen', 'samira@filmgig.nl', '$2y$12$wH0YxjQ9JpY5j6n9Q8m2C.3g6pV8gDg1nJx7b5Q3t5x5G4f6m2y4O', 'freelance', 'Utrecht Creative District, NL', 'Freelance camera operator and editor.', 22345678, '2026-04-05 09:05:00'),
(3, 'james_admin', 'James Vermeer', 'james@filmgig.nl', '$2y$12$wH0YxjQ9JpY5j6n9Q8m2C.3g6pV8gDg1nJx7b5Q3t5x5G4f6m2y4O', 'admin', 'Amsterdam, NL', 'Platform administrator.', 32345678, '2026-04-05 09:10:00');

INSERT INTO `productionHouse` (`productionHouseId`, `userId`, `companyName`, `website`, `createdAt`) VALUES
(1, 1, 'FilmGig Studios BV', 'https://filmgig.nl', '2026-04-05 09:00:00');

INSERT INTO `freelancer` (`freelancerId`, `userId`, `dateOfBirth`, `createdAt`) VALUES
(1, 2, '1996-09-14', '2026-04-05 09:05:00');

INSERT INTO `gig` (`gigId`, `ownerId`, `title`, `description`, `category`, `location`, `startDate`, `rateType`, `payRate`, `status`, `createdAt`) VALUES
(1, 1, 'Documentary Camera Operator', 'Capture interviews and b-roll footage for a 3-day documentary production.', 'Camera', 'Amsterdam, Netherlands', '2026-04-12', 'hourly', 55.00, 'active', '2026-04-05 09:15:00'),
(2, 1, 'Commercial Video Editor', 'Edit a set of social-first ad videos with a fast turnaround.', 'Editing', 'The Hague, Netherlands', '2026-04-14', 'hourly', 60.00, 'active', '2026-04-05 09:20:00');

INSERT INTO `submission` (`submissionId`, `gigId`, `freelancerId`, `status`, `submittedAt`) VALUES
(1, 1, 1, 'pending', '2026-04-05 09:30:00'),
(2, 2, 1, 'accepted', '2026-04-05 09:35:00');

SET foreign_key_checks = 1;
COMMIT;
