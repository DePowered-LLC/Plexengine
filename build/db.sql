DROP TABLE IF EXISTS `chat`;
DROP TABLE IF EXISTS `guests`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `notifications`;

CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nick` varchar(32) NOT NULL,
  `message` text NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `color` varchar(14) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13091 ;

CREATE TABLE IF NOT EXISTS `guests` (
  `nick` varchar(32) NOT NULL,
  `last_online` bigint(20) NOT NULL,
  `limitation` text NOT NULL,
  UNIQUE KEY `nick` (`nick`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `info` text NOT NULL,
  `type` text NOT NULL,
  `is_readed` boolean NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(32) NOT NULL,
  `nick` varchar(32) NOT NULL,
  `pass` text NOT NULL,
  `date_of_birth` varchar(10) NOT NULL,
  `access` varchar(32) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'chat',
  `ignored` text NOT NULL,
  `credits` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `country` varchar(2) NOT NULL,
  `last_online` bigint(20) NOT NULL,
  `verificated` tinyint(1) NOT NULL DEFAULT '0',
  `about` text NOT NULL,
  `limitation` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nick` (`nick`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;