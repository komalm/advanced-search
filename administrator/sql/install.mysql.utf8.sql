CREATE TABLE IF NOT EXISTS `#__advanced_search_term` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(500) NOT NULL,
  `sound` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term` (`term`),
  KEY `sound` (`sound`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__advanced_search_indexer_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `indexer_id` int(11) NOT NULL,
  `field_code` varchar(50) NOT NULL,
  `field_name` varchar(500) NOT NULL,
  `field_order` int(3) NOT NULL,
  `mapping_field` varchar(100) NOT NULL,
  `mapping_label` varchar(100) NOT NULL,
  `options` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `published` int(1) NOT NULL,
  `search_term` int(1) NOT NULL,
  `useas` int(11) NOT NULL,
  `grid_filter` tinyint(2) NOT NULL,
  `landing_page` int(11) NOT NULL,
  `basic_search` int(1) NOT NULL,
  `category_search` tinyint(1) NOT NULL,
  `display_search` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__advanced_search_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `indexer_id` int(11) NOT NULL,
  `field_code` varchar(50) NOT NULL,
  `field_name` varchar(500) NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(3) NOT NULL,
  `mapping_field` varchar(100) NOT NULL,
  `mapping_label` varchar(100) NOT NULL,
  `options` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `published` int(1) NOT NULL,
  `search_term` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__advanced_search_indexer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `created_date` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(2) NOT NULL,
  `mapped_table` varchar(200) NOT NULL,
  `type_name` varchar(200) NOT NULL,
  `batch_size` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__advanced_search_cronjob` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `limit` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__advanced_search_cronjob_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `limit` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `primary_cat` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__advanced_search_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) NOT NULL,
  `client` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `modified` tinyint(4) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
