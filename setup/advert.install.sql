--
-- Структура таблицы `cot_auto`
--
CREATE TABLE IF NOT EXISTS `cot_advert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL default '',
  `state` tinyint(1) unsigned NOT NULL default '0',
  `category` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `price` decimal(15,2) DEFAULT '0.00',
  `description` varchar(255) DEFAULT '',
  `text` text DEFAULT '',
  `person` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `city` int(11) DEFAULT NULL,
  `city_name` varchar(255) DEFAULT '',
  `phone` varchar(255) DEFAULT '',
  `sticky` tinyint(1) DEFAULT '0',
  `begin` int(11) DEFAULT '0',
  `expire` int(11) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  `user` int(11) NOT NULL default '0',
  `views` mediumint(8) unsigned default '0',
  `admin_notified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT '0',
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `alias_idx` (`alias`),
  KEY `category_idx` (`category`),
  KEY `city_idx` (`city`),
  KEY `begin_idx` (`begin`),
  KEY `expire_idx` (`expire`),
  KEY `sort_idx`  (`sort`),
  KEY `user_idx` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

