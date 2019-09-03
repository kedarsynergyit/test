-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2017 at 10:08 PM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
`id` int(20) NOT NULL,
  `name` varchar(40) NOT NULL,
  `status` varchar(40) NOT NULL,
  `creator` varchar(30) NOT NULL,
  `created` date NOT NULL,
  `started` date NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `name`, `status`, `creator`, `created`, `started`) VALUES
(1, 'project1', 'active', 'urvashi', '2017-04-11', '2017-04-26'),
(2, 'project2', 'Finish', 'urvashi', '2017-04-04', '2017-04-26'),
(3, 'project3', 'active', 'urvashi', '2017-04-02', '2017-04-26'),
(4, 'project4', 'active', 'urvashi', '2017-05-11', '2017-04-26'),
(5, 'swathi', 'active', 'urvashi', '2017-04-03', '2017-04-24');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `status` varchar(20) NOT NULL,
  `priority` varchar(40) NOT NULL,
  `description` varchar(50) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `title` varchar(30) NOT NULL,
  `user` varchar(20) NOT NULL,
  `project` varchar(30) NOT NULL,
`id` int(20) NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`status`, `priority`, `description`, `start`, `end`, `title`, `user`, `project`, `id`, `name`) VALUES
('Active', 'High', 'Task task Task task Task task Task task Task task ', '2017-04-03', '2017-04-27', 'Task title', 'abc', 'Project1', 1, 'Task1'),
('Active', 'High', 'Task task Task task Task task Task task Task task ', '2017-04-03', '2017-04-27', 'Task title', 'abc', 'Project1', 2, 'Task2'),
('', 'High', 'Task task Task task Task task Task task Task task', '2017-04-02', '2017-04-26', 'Task title', 'abc', 'Project1', 3, 'Task name'),
('', 'High', 'Task task Task task Task task Task task Task task', '2017-04-02', '2017-04-26', 'Task title', 'abc', 'Project1', 4, 'Task name'),
('', 'High', 'Task task Task task Task task Task task Task task', '2017-04-02', '2017-04-26', 'Task title', 'abc', 'Project1', 5, 'Task name');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(50) NOT NULL,
  `password` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` int(10) NOT NULL,
  `role` int(10) NOT NULL,
  `e_password` varchar(20) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `password`, `name`, `email`, `phone`, `status`, `role`, `e_password`) VALUES
(1, 'abcd', 'urvashi', 'urvashidave123@gmail.com', '6478236059', 1, 1, 'abcd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project`
--
ALTER TABLE `project`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
MODIFY `id` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
MODIFY `id` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` int(50) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
