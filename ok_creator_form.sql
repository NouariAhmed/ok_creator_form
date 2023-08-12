-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2023 at 06:42 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ok_creator_form`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `authorfullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `year_of_birth` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `authorAddress` varchar(255) NOT NULL,
  `author_type` varchar(50) NOT NULL,
  `book_type_id` int(11) NOT NULL,
  `book_level_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `authorfullname`, `email`, `year_of_birth`, `phone`, `authorAddress`, `author_type`, `book_type_id`, `book_level_id`, `subject_id`) VALUES
(28, 'ismail', 'ahmeddz411@gmail.com', 2000, '0665965125', 'ainelmelh', 'teacher', 2, 5, 1),
(29, 'ahmed', 'ahmeddz411@gmail.com', 2000, '0665965125', 'msila', 'teacher', 2, 3, 3),
(30, 'ahmed', 'ahmeddz411@gmail.com', 2000, '0665965125', 'msila', 'teacher', 2, 3, 3),
(31, 'ahmed', 'ahmeddz411@gmail.com', 2000, '0665965125', 'msila', 'teacher', 2, 3, 3),
(32, 'ahmed', 'ahmeddz411@gmail.com', 2000, '0665965125', 'msila', 'teacher', 2, 3, 3),
(33, 'ahmed', 'ahmeddz411@gmail.com', 2000, '0665965125', 'msila', 'student', 2, 3, 3),
(34, 'ahmed', 'ahmeddz411@gmail.com', 2000, '0665965125', 'msila', 'student', 2, 3, 3),
(35, 'ahmed', 'ahmeddz411@gmail.com', 2000, '0665965125', 'msila', 'agent', 2, 3, 3),
(36, 'mohamed', 'nouari@gmail.com', 1995, '066665998', 'djelfa', 'student', 3, 8, 1),
(37, 'mohamed', 'nouari@gmail.com', 1995, '066665998', 'djelfa', 'student', 3, 8, 1),
(38, 'ahmed', 'nouari@gmail.com', 1995, '066665998', 'djelfa', 'student', 3, 8, 1),
(39, 'ali', 'nouari@gmail.com', 1995, '066665998', 'djelfa', 'student', 3, 8, 1),
(40, 'mohamed', 'nouari@gmail.com', 1995, '066665998', 'djelfa', 'student', 3, 8, 1),
(41, 'mohamed', 'nouari@gmail.com', 1995, '066665998', 'djelfa', 'student', 3, 8, 1),
(42, 'mohamed', 'nouari@gmail.com', 1995, '066665998', 'djelfa', 'student', 3, 8, 1),
(43, 'mohamed', 'nouari@gmail.com', 1995, '066665998', 'djelfa', 'student', 3, 8, 1),
(44, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(45, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(46, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(47, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(48, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(49, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(50, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(51, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(52, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(53, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(54, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3),
(55, 'yahia', 'yahia@gmail.com', 2001, '066989865', 'boussada', 'student', 2, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `book_levels`
--

CREATE TABLE `book_levels` (
  `id` int(11) NOT NULL,
  `level_name` varchar(100) NOT NULL,
  `book_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_levels`
--

INSERT INTO `book_levels` (`id`, `level_name`, `book_type_id`) VALUES
(3, 'اولى ابتدائي', 2),
(4, 'ثانية ابتدائي', 2),
(5, 'ثالثة ابتدائي', 2),
(7, 'خامسة ابتدائي', 2),
(8, 'اولى متوسط', 3),
(9, 'ثانية متوسط', 3),
(10, 'ثالثة متوسط', 3);

-- --------------------------------------------------------

--
-- Table structure for table `book_types`
--

CREATE TABLE `book_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_types`
--

INSERT INTO `book_types` (`id`, `type_name`) VALUES
(2, 'ابتدائي'),
(3, 'متوسط'),
(4, 'ثانوي'),
(5, 'جامعي'),
(6, 'رواية'),
(7, 'قصص'),
(8, 'ديني'),
(9, 'طبي'),
(10, 'بيداغوجي'),
(11, 'متوسط غير مكرر'),
(12, 'ثانوي مكرر3'),
(20, 'متوسط مكرر 98'),
(21, 'متوسط مكرر'),
(22, 'متوسط غير مكرر');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_data`
--

CREATE TABLE `doctor_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_data`
--

CREATE TABLE `student_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `studentLevel` varchar(50) NOT NULL,
  `studentSpecialty` varchar(100) NOT NULL,
  `baccalaureateRate` varchar(50) NOT NULL,
  `baccalaureateYear` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `book_level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `book_level_id`) VALUES
(1, 'مادة العلوم مع الاستاذ اسماعيل قوادري', 3),
(2, 'مادة الدراسات الاسلامية ', 8),
(3, 'الرياضيات', 8),
(4, 'العلوم الفيزيائية التقنية', 8);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_data`
--

CREATE TABLE `teacher_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `teacherExperience` varchar(50) NOT NULL,
  `teacherCertificate` varchar(100) NOT NULL,
  `teacherRank` varchar(50) NOT NULL,
  `workFoundation` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_data`
--

INSERT INTO `teacher_data` (`id`, `user_id`, `teacherExperience`, `teacherCertificate`, `teacherRank`, `workFoundation`) VALUES
(2, 28, '15', 'google', 'good', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `role` enum('member','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `pass`, `role`) VALUES
(3, 'ahmed_elamine', 'ahmeddz411@gmail.com', '$2y$10$ejqFO7mN56RGcWr80qe/GuRJhaYp3rLJkd3M20X8iXckXRPzw7Joe', 'admin'),
(4, 'lamine', 'lamine@gmail.com', '$2y$10$rW.H610ZHPsonn1PvABcdOtlP.9l6eF/sRSo09abqmmOnXMYdtI0O', 'member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_type_id` (`book_type_id`),
  ADD KEY `book_level_id` (`book_level_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `book_levels`
--
ALTER TABLE `book_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_type_id` (`book_type_id`);

--
-- Indexes for table `book_types`
--
ALTER TABLE `book_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_data`
--
ALTER TABLE `doctor_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_data`
--
ALTER TABLE `student_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_level_id` (`book_level_id`);

--
-- Indexes for table `teacher_data`
--
ALTER TABLE `teacher_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `book_levels`
--
ALTER TABLE `book_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `book_types`
--
ALTER TABLE `book_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `doctor_data`
--
ALTER TABLE `doctor_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_data`
--
ALTER TABLE `student_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `teacher_data`
--
ALTER TABLE `teacher_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `authors`
--
ALTER TABLE `authors`
  ADD CONSTRAINT `authors_ibfk_1` FOREIGN KEY (`book_type_id`) REFERENCES `book_types` (`id`),
  ADD CONSTRAINT `authors_ibfk_2` FOREIGN KEY (`book_level_id`) REFERENCES `book_levels` (`id`),
  ADD CONSTRAINT `authors_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `book_levels`
--
ALTER TABLE `book_levels`
  ADD CONSTRAINT `book_levels_ibfk_1` FOREIGN KEY (`book_type_id`) REFERENCES `book_types` (`id`);

--
-- Constraints for table `doctor_data`
--
ALTER TABLE `doctor_data`
  ADD CONSTRAINT `doctor_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `authors` (`id`);

--
-- Constraints for table `student_data`
--
ALTER TABLE `student_data`
  ADD CONSTRAINT `student_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `authors` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`book_level_id`) REFERENCES `book_levels` (`id`);

--
-- Constraints for table `teacher_data`
--
ALTER TABLE `teacher_data`
  ADD CONSTRAINT `teacher_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `authors` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
