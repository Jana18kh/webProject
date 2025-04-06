-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 05, 2025 at 08:03 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

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
-- Table structure for table `Appointment`
--

CREATE TABLE `Appointment` (
  `id` int NOT NULL,
  `PatientID` int NOT NULL,
  `DoctorID` int NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Confirmed','Done') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Appointment`
--

INSERT INTO `Appointment` (`id`, `PatientID`, `DoctorID`, `date`, `time`, `reason`, `status`) VALUES
(1, 1, 1, '2025-06-15', '10:00:00', 'Heart Checkup', 'Pending'),
(2, 2, 2, '2025-07-20', '14:00:00', 'Headache', 'Confirmed'),
(3, 3, 3, '2025-08-25', '11:00:00', 'Routine Checkup', 'Done');

-- --------------------------------------------------------

--
-- Table structure for table `Doctor`
--

CREATE TABLE `Doctor` (
  `id` int NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `uniqueFileName` varchar(255) NOT NULL,
  `SpecialityID` int NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Doctor`
--

INSERT INTO `Doctor` (`id`, `firstName`, `lastName`, `uniqueFileName`, `SpecialityID`, `emailAddress`, `password`) VALUES
(1, 'Mona', 'Ali', 'dr.mona_ali.jpg', 1, 'dr.mona@example.com', '$2a$12$ccxWg8NYiKT.h.aieX6ApubPV0yTZh.3WVS2tdAK8w2bCpaJOmSAS'),
(2, 'Omar', 'Saleh', 'dr.omar_saleh.jpg', 1, 'dr.omar@example.com', '$2a$12$cYrJgnyaNB8Sr.vqnCItlenWmgWEq9MW3hKIGPkINo.Vziu5iLJgu'),
(3, 'Saleh', 'Ibrahim', 'dr.saleh_ibrahim.jpg', 1, 'dr.saleh@example.com', '$2a$12$MUnsMs4Js3VYNHoAV1rWGux3bN63Csr3LUG3aKCs3OSCNu7QxXZIe'),
(4, 'Khalid', 'Jamal', 'dr.khalid_jamal.jpg', 2, 'dr.khalid@example.com', '$2a$12$ekPN/kd7WrOV6CTskpv4yej155BFPFzx4bN5B5ON42mRwri8U8tTy'),
(5, 'Hadi', 'Mansour', 'dr.hadi_mansour.jpg', 2, 'dr.hadi@example.com', '$2a$12$71ngQOrjb/usPsG3P4Vm9.x4xBHgiXx6GVmN3X.67S1a1jDO5F6py'),
(6, 'Majed', 'Nashit', 'dr.majed_nashit.jpg', 2, 'dr.majed@example.com', '$2a$12$CQ7/Zvm0c/1Xhpp3YitS8OiPdZz1otQTGhK5DkvGnBC64bYMomJ.e'),
(8, 'Fatima', 'Nasser', 'dr.fatima_nasser.jpg', 3, 'dr.fatima@example.com', '$2a$12$Fa8hoS3nSNiqxDAXUuI2r.vpJBbcOxw.3qk2/rNl1i6hKV5Aroi4e'),
(9, 'Ali', 'Nader', 'dr.ali_nader.jpg', 3, 'dr.ali@example.com', '$2a$12$bsy.1dPqVMDpR99YGzDaUOPEQBa9Sz70Ghr4RsIt8tJIIXxyuMJYS');

-- --------------------------------------------------------

--
-- Table structure for table `Medication`
--

CREATE TABLE `Medication` (
  `id` int NOT NULL,
  `MedicationName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Medication`
--

INSERT INTO `Medication` (`id`, `MedicationName`) VALUES
(1, 'Aspirin'),
(2, 'Ibuprofen'),
(3, 'Paracetamol');

-- --------------------------------------------------------

--
-- Table structure for table `Patient`
--

CREATE TABLE `Patient` (
  `id` int NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `DoB` date NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Patient`
--

INSERT INTO `Patient` (`id`, `firstName`, `lastName`, `gender`, `DoB`, `emailAddress`, `password`) VALUES
(1, 'John', 'Doe', 'Male', '1990-05-15', 'johndoe@example.com', 'password123'),
(2, 'Jane', 'Smith', 'Female', '1985-08-22', 'janesmith@example.com', 'password456'),
(3, 'Mary', 'Johnson', 'Female', '2000-01-30', 'maryjohnson@example.com', 'password789');

-- --------------------------------------------------------

--
-- Table structure for table `Prescription`
--

CREATE TABLE `Prescription` (
  `id` int NOT NULL,
  `AppointmentID` int NOT NULL,
  `MedicationID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Prescription`
--

INSERT INTO `Prescription` (`id`, `AppointmentID`, `MedicationID`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `Speciality`
--

CREATE TABLE `Speciality` (
  `id` int NOT NULL,
  `speciality` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Speciality`
--

INSERT INTO `Speciality` (`id`, `speciality`) VALUES
(1, 'Cardiology'),
(2, 'Neurology'),
(3, 'Pediatrics'),
(4, 'Orthopedics'),
(5, 'Dermatology'),
(6, 'Psychiatry'),
(7, 'Oncology'),
(8, 'Gynecology');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Appointment`
--
ALTER TABLE `Appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `DoctorID` (`DoctorID`);

--
-- Indexes for table `Doctor`
--
ALTER TABLE `Doctor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`),
  ADD KEY `SpecialityID` (`SpecialityID`);

--
-- Indexes for table `Medication`
--
ALTER TABLE `Medication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Patient`
--
ALTER TABLE `Patient`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`);

--
-- Indexes for table `Prescription`
--
ALTER TABLE `Prescription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `AppointmentID` (`AppointmentID`),
  ADD KEY `MedicationID` (`MedicationID`);

--
-- Indexes for table `Speciality`
--
ALTER TABLE `Speciality`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Appointment`
--
ALTER TABLE `Appointment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Doctor`
--
ALTER TABLE `Doctor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `Medication`
--
ALTER TABLE `Medication`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Patient`
--
ALTER TABLE `Patient`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Prescription`
--
ALTER TABLE `Prescription`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Speciality`
--
ALTER TABLE `Speciality`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Appointment`
--
ALTER TABLE `Appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `Patient` (`id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `Doctor` (`id`);

--
-- Constraints for table `Doctor`
--
ALTER TABLE `Doctor`
  ADD CONSTRAINT `doctor_ibfk_1` FOREIGN KEY (`SpecialityID`) REFERENCES `Speciality` (`id`);

--
-- Constraints for table `Prescription`
--
ALTER TABLE `Prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`AppointmentID`) REFERENCES `Appointment` (`id`),
  ADD CONSTRAINT `prescription_ibfk_2` FOREIGN KEY (`MedicationID`) REFERENCES `Medication` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
