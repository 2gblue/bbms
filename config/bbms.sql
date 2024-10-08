-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2024 at 12:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bbms`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `id` int(5) NOT NULL,
  `bookTitle` varchar(255) NOT NULL,
  `pagesNumber` int(5) NOT NULL,
  `authorName` varchar(255) NOT NULL,
  `quantity` int(5) NOT NULL,
  `isbn` varchar(255) NOT NULL,
  `genre` varchar(255) NOT NULL,
  `publicationCompany` varchar(255) NOT NULL,
  `bookCover` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`id`, `bookTitle`, `pagesNumber`, `authorName`, `quantity`, `isbn`, `genre`, `publicationCompany`, `bookCover`) VALUES
(1, 'Java Distributed Computing', 384, 'Jim Farley', 1, '978-1-56592-206-8', 'Educational', 'O\'Reilly Media', '../uploads/books/1714994041_javacomputing.jpg'),
(2, '.NET Common Language Runtime Unleashed 2-volume set', 1024, 'Kevin Burton', 1, '978-0-672-32124-5', 'Educational', 'Sams', '../uploads/books/1714994048_netcommon.jpg'),
(3, '14th Symposium on Logic in Computer Science Proceedings July 2-5, 1999, Trento, Italy (Symposium on Logic in Computer Science//Proceedings)', 478, 'IEEE Computer Society', 3, '978-0-7695-0158-1', 'Educational', 'Institute of Electrical & Electronics Engineers (IEEE)', '../uploads/books/1714994114_14symposium.jpg'),
(4, 'One Piece, Vol. 59', 208, 'Eiichiro Oda', 2, '978-1-4215-3959-1', 'Fiction', 'VIZ Media', '../uploads/books/1714994258_onepiece59.jpg'),
(5, 'A Brief Introduction to Web3 Decentralized Web Fundamentals for App Development', 150, 'Shashank Mohan Jain', 2, '978-1-4842-8974-7', 'Educational', 'Apress', '../uploads/books/1714994312_web3.jpg'),
(6, '10 Minute Cuisine Good Fresh Food Very Fast', 128, 'MARIE-PIERRE MOINE HENRIETTA GREEN', 1, '978-1-85029-283-8', 'Cooking', 'Conran Octopus', '../uploads/books/1714994513_10mincuisine.jpg'),
(7, '101 Facts You Should Know About Food', 224, 'John Farndon', 4, '978-1-84046-767-3', 'Cooking', 'Icon Books', '../uploads/books/1714994538_101factsfood.jpg'),
(8, 'My Neighbor Totoro (Tokuma\'s Magical Adventure)', 111, 'Hayao Miyazaki', 1, '978-4-19-086971-5', 'Fiction', 'Tokuma Shoten', '../uploads/books/1714994830_totoro.jpg'),
(9, '\'J.R.R.TOLKIEN THE MAN WHO CREATED \'\'THE LORD OF THE RINGS\'\'\'', 144, 'Michael Coren', 1, '978-0-7522-6167-6', 'Biographies', 'Boxtree', '../uploads/books/1714994890_tolkien.jpg'),
(10, 'Biker\'s Guide to the Open Road, A Ride It Like You Stole It', 128, 'Chuck Hays, Anne Mitchell, Penny Powers', 2, '978-1-58685-238-2', 'Non-Fiction', 'Gibbs Smith', '../uploads/books/1714995017_biker.jpg'),
(11, 'Joan Crawford Hollywood Martyr', 320, 'David Bret', 1, '978-1-906217-37-2', 'Biographies', 'JR Books', '../uploads/books/1714995168_joan.jpg'),
(12, 'Car Audio for Dummies', 310, 'Doug Newcomb', 2, '978-0-470-15158-7', 'Non-Fiction', 'For Dummies', '../uploads/books/1714995338_caraudio.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `borrow`
--

CREATE TABLE `borrow` (
  `borrowID` int(5) NOT NULL,
  `bookID` int(5) NOT NULL,
  `userID` int(5) NOT NULL,
  `time` varchar(4) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow`
--

INSERT INTO `borrow` (`borrowID`, `bookID`, `userID`, `time`, `date`) VALUES
(1, 1, 1, '1405', '2024-05-18'),
(2, 4, 1, '0915', '2024-05-18'),
(3, 2, 1, '1212', '2024-05-18'),
(8, 12, 1, '0244', '2024-05-20');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `rental_ID` int(5) NOT NULL,
  `id` int(5) NOT NULL,
  `borrowID` int(5) NOT NULL,
  `status_ID` int(5) NOT NULL,
  `rental_deadline` date NOT NULL,
  `rental_remark` varchar(255) DEFAULT NULL,
  `archived` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`rental_ID`, `id`, `borrowID`, `status_ID`, `rental_deadline`, `rental_remark`, `archived`) VALUES
(1, 1, 3, 2, '2024-05-24', 'ahuuuu', 1),
(3, 1, 1, 2, '2024-05-25', 'lulu', 0),
(23, 1, 2, 3, '2024-05-25', 'lop', 1),
(55, 1, 8, 1, '2024-05-27', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `history_status`
--

CREATE TABLE `history_status` (
  `status_ID` int(5) NOT NULL,
  `status_name` varchar(10) NOT NULL,
  `status_desc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_status`
--

INSERT INTO `history_status` (`status_ID`, `status_name`, `status_desc`) VALUES
(1, 'Borrowing', 'User is borrowing the book. The book is in the user\'s care/hand'),
(2, 'Returned', 'User have return the book to the library.'),
(3, 'Missing', 'User lost the book.'),
(4, 'Damaged', 'The book is faulty/damaged/lost some pages because of the user\'s carelessness');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(5) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` tinyint(5) DEFAULT NULL,
  `user_fullName` varchar(255) NOT NULL,
  `user_matric` varchar(7) DEFAULT NULL,
  `user_faculty` varchar(255) DEFAULT NULL,
  `user_phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `role`, `user_fullName`, `user_matric`, `user_faculty`, `user_phone`) VALUES
(1, 'user', 'test', 1, 'FARHAN MAHDINI', 'TA19023', 'FACULTY OF MECHANICAL AND ENGINEERING', '010-809 8366'),
(2, 'staff', 'test', 2, 'ANIS NABIHAH', '', '', '011-2757 2455'),
(3, 'aniz', 'test', 1, 'ANI ZAHIRAH', 'CB21102', 'FACULTY OF COMPUTING', '011-6955 1942');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrow`
--
ALTER TABLE `borrow`
  ADD PRIMARY KEY (`borrowID`),
  ADD KEY `bookID` (`bookID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`rental_ID`),
  ADD UNIQUE KEY `rental_ID` (`rental_ID`),
  ADD UNIQUE KEY `rental_ID_2` (`rental_ID`),
  ADD UNIQUE KEY `rental_ID_3` (`rental_ID`),
  ADD KEY `id` (`id`) USING BTREE,
  ADD KEY `rental_status` (`status_ID`) USING BTREE,
  ADD KEY `borrowID` (`borrowID`),
  ADD KEY `status_ID` (`status_ID`);

--
-- Indexes for table `history_status`
--
ALTER TABLE `history_status`
  ADD PRIMARY KEY (`status_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `borrow`
--
ALTER TABLE `borrow`
  MODIFY `borrowID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `rental_ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `history_status`
--
ALTER TABLE `history_status`
  MODIFY `status_ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow`
--
ALTER TABLE `borrow`
  ADD CONSTRAINT `borrow_ibfk_1` FOREIGN KEY (`bookID`) REFERENCES `book` (`id`),
  ADD CONSTRAINT `borrow_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`id`);

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`borrowID`) REFERENCES `borrow` (`borrowID`),
  ADD CONSTRAINT `status` FOREIGN KEY (`status_ID`) REFERENCES `history_status` (`status_ID`),
  ADD CONSTRAINT `user id` FOREIGN KEY (`id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
