-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 03, 2025 at 06:02 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `curego`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `uniqueFileName` varchar(255) DEFAULT NULL,
  `SpecialityID` int(11) DEFAULT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `firstName`, `lastName`, `uniqueFileName`, `SpecialityID`, `emailAddress`, `password`) VALUES
(1, 'Mona', 'Al-Saud', '65f8a2c3d4e5f.jpg', 1, 'mona.alsaud@curego.com', '$2y$10$examplehashedpassword1'),
(2, 'Omar', 'Al-Khalid', '65f8a2c3d4e6g.jpg', 1, 'omar.alkhalid@curego.com', '$2y$10$examplehashedpassword2'),
(3, 'Saleh', 'Al-Mansour', '65f8a2c3d4e7h.jpg', 1, 'saleh.almansour@curego.com', '$2y$10$examplehashedpassword3'),
(4, 'Khalid', 'Al-Nasser', '65f8a2c3d4e8i.jpg', 2, 'khalid.alnasser@curego.com', '$2y$10$examplehashedpassword4'),
(5, 'Hadi', 'Al-Salem', '65f8a2c3d4e9j.jpg', 2, 'hadi.alsalem@curego.com', '$2y$10$examplehashedpassword5'),
(6, 'Majed', 'Al-Ghamdi', '65f8a2c3d4e0k.jpg', 2, 'majed.alghamdi@curego.com', '$2y$10$examplehashedpassword6'),
(7, 'Sami', 'Al-Harbi', '65f8a2c3d4e1l.jpg', 3, 'sami.alharbi@curego.com', '$2y$10$examplehashedpassword7'),
(8, 'Fatima', 'Al-Zahrani', '65f8a2c3d4e2m.jpg', 3, 'fatima.alzahrani@curego.com', '$2y$10$examplehashedpassword8'),
(9, 'Ali', 'Al-Qahtani', '65f8a2c3d4e3n.jpg', 3, 'ali.alqahtani@curego.com', '$2y$10$examplehashedpassword9'),
(10, 'Ibrahim', 'Al-Faisal', '65f8a2c3d4e4o.jpg', 4, 'ibrahim.alfaisal@curego.com', '$2y$10$examplehashedpassword10'),
(11, 'Hassan', 'Al-Mutairi', '65f8a2c3d4e5p.jpg', 4, 'hassan.almutairi@curego.com', '$2y$10$examplehashedpassword11'),
(12, 'Nasser', 'Al-Dosari', '65f8a2c3d4e6q.jpg', 4, 'nasser.aldosari@curego.com', '$2y$10$examplehashedpassword12'),
(13, 'Ahmad', 'Al-Sharif', '65f8a2c3d4e7r.jpg', 5, 'ahmad.alsharif@curego.com', '$2y$10$examplehashedpassword13'),
(14, 'Taraf', 'Al-Ghanim', '65f8a2c3d4e8s.jpg', 5, 'taraf.alghanim@curego.com', '$2y$10$examplehashedpassword14'),
(15, 'Majd', 'Al-Otaibi', '65f8a2c3d4e9t.jpg', 5, 'majd.alotaibi@curego.com', '$2y$10$examplehashedpassword15'),
(16, 'Reem', 'Al-Rashid', '65f8a2c3d4e0u.jpg', 6, 'reem.alrashid@curego.com', '$2y$10$examplehashedpassword16'),
(17, 'Fares', 'Al-Majed', '65f8a2c3d4e1v.jpg', 6, 'fares.almajed@curego.com', '$2y$10$examplehashedpassword17'),
(18, 'Manar', 'Al-Sulaiman', '65f8a2c3d4e2w.jpg', 6, 'manar.alsulaiman@curego.com', '$2y$10$examplehashedpassword18'),
(19, 'Tariq', 'Al-Hamdan', '65f8a2c3d4e3x.jpg', 7, 'tariq.alhamdan@curego.com', '$2y$10$examplehashedpassword19'),
(20, 'Fares', 'Al-Yousef', '65f8a2c3d4e4y.jpg', 7, 'fares.alyousef@curego.com', '$2y$10$examplehashedpassword20'),
(21, 'Mohammad', 'Al-Abdullah', '65f8a2c3d4e5z.jpg', 7, 'mohammad.alabdullah@curego.com', '$2y$10$examplehashedpassword21'),
(22, 'Amal', 'Al-Khaled', '65f8a2c3d4e6a.jpg', 8, 'amal.alkhaled@curego.com', '$2y$10$examplehashedpassword22'),
(23, 'Nora', 'Al-Sheikh', '65f8a2c3d4e7b.jpg', 8, 'nora.alsheikh@curego.com', '$2y$10$examplehashedpassword23'),
(24, 'Sara', 'Al-Mohammed', '65f8a2c3d4e8c.jpg', 8, 'sara.almohammed@curego.com', '$2y$10$examplehashedpassword24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`),
  ADD KEY `SpecialityID` (`SpecialityID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `doctor_ibfk_1` FOREIGN KEY (`SpecialityID`) REFERENCES `speciality` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
