-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 18, 2025 at 08:42 PM
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
CREATE DATABASE IF NOT EXISTS `curego` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `curego`;

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `DoctorID` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `reason` text,
  `status` enum('Pending','Confirmed','Done') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`id`, `PatientID`, `DoctorID`, `date`, `time`, `reason`, `status`) VALUES
(10, 1, 1, '2025-04-01', '10:00:00', 'فحص روتيني للأطفال', 'Pending'),
(11, 2, 2, '2025-04-02', '14:30:00', 'ألم في الركبة', 'Confirmed'),
(12, 3, 3, '2025-04-03', '09:15:00', 'فحص القلب السنوي', 'Done');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `uniqueFileName` varchar(255) DEFAULT NULL,
  `SpecialityID` int(11) DEFAULT NULL,
  `emailAddress` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `firstName`, `lastName`, `uniqueFileName`, `SpecialityID`, `emailAddress`, `password`) VALUES
(1, 'خالد', 'السعد', '65f8a2c3d4e5f.jpg', 1, 'khalid.alsaad@example.com', 'docpass123'),
(2, 'نورة', 'القحطاني', '65f8a2c3d4e6g.png', 2, 'noura.alqahtani@example.com', 'docpass456'),
(3, 'محمد', 'العلي', '65f8a2c3d4e7h.jpg', 3, 'mohammed.alali@example.com', 'docpass789');

-- --------------------------------------------------------

--
-- Table structure for table `medication`
--

CREATE TABLE `medication` (
  `id` int(11) NOT NULL,
  `MedicationName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `medication`
--

INSERT INTO `medication` (`id`, `MedicationName`) VALUES
(3, 'أتورفاستاتين'),
(2, 'أموكسيسيلين'),
(1, 'باراسيتامول');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `Gender` enum('Male','Female') DEFAULT NULL,
  `DoB` date DEFAULT NULL,
  `emailAddress` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `firstName`, `lastName`, `Gender`, `DoB`, `emailAddress`, `password`) VALUES
(1, 'أحمد', 'محمد', 'Male', '1990-05-15', 'ahmed.mohammed@example.com', 'password123'),
(2, 'سارة', 'عبدالله', 'Female', '1985-12-01', 'sara.abdullah@example.com', 'securepass456'),
(3, 'علي', 'حسن', 'Male', '2000-03-22', 'ali.hassan@example.com', 'mypassword789');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `id` int(11) NOT NULL,
  `AppointmentID` int(11) DEFAULT NULL,
  `MedicationID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`id`, `AppointmentID`, `MedicationID`) VALUES
(13, 10, 1),
(14, 11, 2),
(15, 12, 3);

-- --------------------------------------------------------

--
-- Table structure for table `speciality`
--

CREATE TABLE `speciality` (
  `id` int(11) NOT NULL,
  `speciality` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `speciality`
--

INSERT INTO `speciality` (`id`, `speciality`) VALUES
(2, 'جراحة العظام'),
(1, 'طب الأطفال'),
(3, 'طب القلب');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `DoctorID` (`DoctorID`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`),
  ADD KEY `SpecialityID` (`SpecialityID`);

--
-- Indexes for table `medication`
--
ALTER TABLE `medication`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `MedicationName` (`MedicationName`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `AppointmentID` (`AppointmentID`),
  ADD KEY `MedicationID` (`MedicationID`);

--
-- Indexes for table `speciality`
--
ALTER TABLE `speciality`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `speciality` (`speciality`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `medication`
--
ALTER TABLE `medication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `speciality`
--
ALTER TABLE `speciality`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patient` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `doctor` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `doctor_ibfk_1` FOREIGN KEY (`SpecialityID`) REFERENCES `speciality` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`AppointmentID`) REFERENCES `appointment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescription_ibfk_2` FOREIGN KEY (`MedicationID`) REFERENCES `medication` (`id`) ON DELETE CASCADE;
--
-- Database: `er`
--
CREATE DATABASE IF NOT EXISTS `er` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `er`;

-- --------------------------------------------------------

--
-- Table structure for table `erpatient`
--

CREATE TABLE `erpatient` (
  `ID` varchar(10) NOT NULL,
  `FirstName` varchar(20) NOT NULL,
  `LastName` varchar(20) NOT NULL,
  `Age` int(3) NOT NULL,
  `Priority` int(1) NOT NULL,
  `Admitted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `erpatient`
--

INSERT INTO `erpatient` (`ID`, `FirstName`, `LastName`, `Age`, `Priority`, `Admitted`) VALUES
('1', 'n', 'nn', 11, 1, 1),
('1111', 'Sarah', 'Ahmad', 25, 5, 1),
('2222', 'Ali', 'Saleh', 30, 3, 1),
('33', 'Sarah', 'Ahmad', 25, 5, 0),
('333', 'Ghada', 'Mohammad', 15, 1, 1),
('5555', 'Yaser', 'Ali', 10, 3, 1),
('6666', 'Hala', 'Waleed', 33, 5, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `erpatient`
--
ALTER TABLE `erpatient`
  ADD PRIMARY KEY (`ID`);
--
-- Database: `shopping`
--
CREATE DATABASE IF NOT EXISTS `shopping` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `shopping`;

-- --------------------------------------------------------

--
-- Table structure for table `shoppingwishlist`
--

CREATE TABLE `shoppingwishlist` (
  `ID` int(4) NOT NULL,
  `familyMember` varchar(50) NOT NULL,
  `item` varchar(200) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shoppingwishlist`
--

INSERT INTO `shoppingwishlist` (`ID`, `familyMember`, `item`, `category`, `price`) VALUES
(1111, 'Sarah', 'Rice Cooker', 'Kitchen', 100),
(2222, 'Ahmad', 'TV', 'Home', 1000),
(3333, 'Salem', 'PS5 controller', 'Electronics', 200),
(3334, '1111', 'TV', '3333', 250),
(3335, 'Sarah', 're', '1111', 250),
(3336, 'Sarah', 're', '1111', 250),
(3337, 'Ahmad', 'jjj', 'Kitchen', 77),
(3338, 'Sarah', 'hh', 'Home', 88);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shoppingwishlist`
--
ALTER TABLE `shoppingwishlist`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shoppingwishlist`
--
ALTER TABLE `shoppingwishlist`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3339;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
