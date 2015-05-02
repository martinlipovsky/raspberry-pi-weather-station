-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Štruktúra tabuľky pre tabuľku `forecast`
--

CREATE TABLE IF NOT EXISTS `forecast` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wind_val` varchar(20) NOT NULL,
  `wind_name` varchar(100) NOT NULL,
  `weather_icon_value` varchar(20) NOT NULL,
  `weather_icon_name` varchar(100) NOT NULL,
  `precipitation` varchar(20) NOT NULL,
  `cloud` varchar(100) NOT NULL,
  `temperature` varchar(20) NOT NULL,
  `humidity` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `openweather`
--

CREATE TABLE IF NOT EXISTS `openweather` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wind_val` varchar(20) NOT NULL,
  `wind_dir` varchar(20) NOT NULL,
  `wind_name` varchar(100) NOT NULL,
  `weather_icon_value` varchar(20) NOT NULL,
  `weather_icon_name` varchar(100) NOT NULL,
  `precipitation` varchar(20) NOT NULL,
  `cloud` varchar(100) NOT NULL,
  `sunrise` varchar(20) NOT NULL,
  `sunset` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `temperature`
--

CREATE TABLE IF NOT EXISTS `temperature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `temperature` double NOT NULL,
  `humidity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
