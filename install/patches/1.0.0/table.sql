--
-- Database: `athena`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `text` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` int(11) NOT NULL,
  `color` enum('yellow','blue','green') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yellow',
  `xyz` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hide` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
