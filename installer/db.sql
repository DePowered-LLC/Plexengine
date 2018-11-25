CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nick` varchar(32) NOT NULL,
  `message` text NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `color` varchar(14) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1769 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(32) NOT NULL,
  `nick` varchar(16) NOT NULL,
  `pass` text NOT NULL,
  `date_of_birth` varchar(10) NOT NULL,
  `access` varchar(32) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'chat',
  `ignored` text NOT NULL,
  `credits` int(11) NOT NULL,
  `country` varchar(2) NOT NULL,
  `last_online` bigint(20) NOT NULL,
  `verificated` tinyint(1) NOT NULL DEFAULT '0',
  `about` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

CREATE TABLE IF NOT EXISTS `data` (
  `key` varchar(32) NOT NULL,
  `val` text NOT NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `data` (`key`, `val`) VALUES
('guests', '');