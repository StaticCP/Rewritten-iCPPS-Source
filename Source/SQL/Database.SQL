-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2014 at 10:35 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `iCPPS`
--

-- --------------------------------------------------------

--
-- Table structure for table `accs`
--

CREATE TABLE IF NOT EXISTS `accs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT 'Nickname',
  `crumbs` text NOT NULL COMMENT 'User Crumbs',
  `password` text NOT NULL COMMENT 'MD5 Password',
  `isActive` int(11) NOT NULL COMMENT 'Email activation',
  `badges` int(11) NOT NULL,
  `email` text NOT NULL COMMENT 'User email',
  `ip` text NOT NULL COMMENT 'User IP address',
  `lastIP` text NOT NULL COMMENT 'User last IP address',
  `time` text NOT NULL COMMENT 'Time for username changing',
  `key` text NOT NULL COMMENT 'Key for activation',
  `slkey` text NOT NULL COMMENT 'Private key for login',
  `puffles` longtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `forgot_users`
--

CREATE TABLE IF NOT EXISTS `forgot_users` (
  `username` text NOT NULL,
  `key` text NOT NULL,
  `time` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `igloos`
--

CREATE TABLE IF NOT EXISTS `igloos` (
  `id` int(11) NOT NULL,
  `current` int(11) NOT NULL,
  `location` int(11) NOT NULL,
  `locations` text NOT NULL,
  `igloos` text NOT NULL,
  `igloo1` int(11) NOT NULL,
  `igloo2` int(11) NOT NULL,
  `igloo3` int(11) NOT NULL,
  `igloo4` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ipbans`
--

CREATE TABLE IF NOT EXISTS `ipbans` (
  `ip` text NOT NULL COMMENT 'IP of the ban receiver',
  `person` text NOT NULL COMMENT 'IP owners name',
  `expiration` int(21) NOT NULL COMMENT 'Unix timestamp of ban expiration'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `email` varchar(999) NOT NULL,
  `message` longtext NOT NULL,
  `rID` varchar(50) NOT NULL,
  `ips` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `monitor`
--

CREATE TABLE IF NOT EXISTS `monitor` (
  `ip` text NOT NULL,
  `requests` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `redemption`
--

CREATE TABLE IF NOT EXISTS `redemption` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index',
  `Type` text NOT NULL COMMENT 'Type',
  `Code` text NOT NULL,
  `Items` text NOT NULL,
  `Coins` int(11) NOT NULL DEFAULT '0',
  `Expire` int(11) NOT NULL DEFAULT '0' COMMENT 'Unix expire date',
  `Uses` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Redemption Server Data' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `redemption`
--

INSERT INTO `redemption` (`ID`, `Type`, `Code`, `Items`, `Coins`, `Expire`, `Uses`) VALUES
(1, 'BLANKET', 'TimmyRocks', '1', 500, 1393113600, 5);

-- --------------------------------------------------------

--
-- Table structure for table `stamps`
--

CREATE TABLE IF NOT EXISTS `stamps` (
  `id` int(11) NOT NULL,
  `stamps` longtext NOT NULL,
  `cover` longtext NOT NULL,
  `restamps` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE IF NOT EXISTS `stats` (
  `ID` int(11) NOT NULL COMMENT 'Server ID',
  `population` int(11) NOT NULL COMMENT 'Population',
  `ts` text NOT NULL COMMENT 'Timestamp',
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`ID`, `population`, `ts`) VALUES
(1, 0, '1399062738'),
(101, 1, '1399062319'),
(102, 1, '1396140634'),

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
