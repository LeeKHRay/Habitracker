-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.7.33 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for habitracker
CREATE DATABASE IF NOT EXISTS `habitracker` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `habitracker`;

-- Dumping structure for table habitracker.activity
CREATE TABLE IF NOT EXISTS `activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_name` varchar(256) NOT NULL,
  `activity_repetition` int(1) NOT NULL DEFAULT '0',
  `activity_one_off_datetime` datetime DEFAULT NULL,
  `activity_recurring_date_0` enum('MON','TUE','WED','THU','FRI','SAT','SUN') DEFAULT NULL,
  `activity_recurring_time_0` time DEFAULT NULL,
  `activity_recurring_date_1` enum('MON','TUE','WED','THU','FRI','SAT','SUN') DEFAULT NULL,
  `activity_recurring_time_1` time DEFAULT NULL,
  `activity_recurring_date_2` enum('MON','TUE','WED','THU','FRI','SAT','SUN') DEFAULT NULL,
  `activity_recurring_time_2` time DEFAULT NULL,
  `activity_time_remark` varchar(256) DEFAULT NULL,
  `activity_location` enum('Islands','Kwai Tsing','North','Sai Kung','Sha Tin','Tai Po','Tsuen Wan','Tuen Mun','Yuen Long','Kowloon City','Kwun Tong','Sham Shui Po','Wong Tai Sin','Yau Tsim Mong','Central & Western','Eastern','Southern','Wan Chai','Online','Others') NOT NULL,
  `activity_remark` varchar(256) DEFAULT NULL,
  `activity_status_open` tinyint(1) NOT NULL DEFAULT '1',
  `host` varchar(150) NOT NULL,
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table habitracker.activity_chat_message
CREATE TABLE IF NOT EXISTS `activity_chat_message` (
  `chat_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `chat_message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table habitracker.chat_message
CREATE TABLE IF NOT EXISTS `chat_message` (
  `chat_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `to_user_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `chat_message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`chat_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table habitracker.goal
CREATE TABLE IF NOT EXISTS `goal` (
  `goal_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `goal_name` varchar(255) NOT NULL,
  `goal_description` varchar(255) DEFAULT NULL,
  `goal_subtask` varchar(255) DEFAULT NULL,
  `goal_end_date` date NOT NULL,
  `goal_start_time` time DEFAULT NULL,
  `goal_end_time` time DEFAULT NULL,
  `goal_public` tinyint(1) NOT NULL,
  `goal_completed` tinyint(1) NOT NULL DEFAULT '0',
  `streak` int(11) NOT NULL DEFAULT '0',
  `streak_last_week` int(11) DEFAULT '0',
  PRIMARY KEY (`goal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table habitracker.pwd_reset
CREATE TABLE IF NOT EXISTS `pwd_reset` (
  `pwd_reset_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` text NOT NULL,
  `selector` text NOT NULL,
  `token` longtext NOT NULL,
  `expires` text NOT NULL,
  PRIMARY KEY (`pwd_reset_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table habitracker.report
CREATE TABLE IF NOT EXISTS `report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_type` enum('goal','activity') NOT NULL,
  `goal_id` int(11) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `reporter` varchar(150) NOT NULL,
  `owner` varchar(150) NOT NULL,
  `reason` text NOT NULL,
  `report_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved` tinyint(1) NOT NULL DEFAULT '0',
  `goal_name` varchar(255) DEFAULT NULL,
  `activity_name` varchar(256) DEFAULT NULL,
  `dismissed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table habitracker.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` text,
  `last_name` text,
  `welcome_message` longtext,
  `avatar` varchar(255) NOT NULL DEFAULT 'avatar_default.jpg',
  `receive_daily_reminder` tinyint(1) NOT NULL DEFAULT '0',
  `receive_weekly_report` tinyint(1) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_typing` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table habitracker.user_activity
CREATE TABLE IF NOT EXISTS `user_activity` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  PRIMARY KEY (`entry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
