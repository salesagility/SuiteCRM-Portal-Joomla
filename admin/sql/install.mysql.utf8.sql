DROP TABLE IF EXISTS `#__advancedopenportal`;
 
CREATE TABLE `#__advancedopenportal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sugar_url` varchar(200) DEFAULT NULL,
  `sugar_user` varchar(60) DEFAULT NULL,
  `sugar_pass` varchar(32) DEFAULT NULL,
  `client_secret` varchar(4098) DEFAULT NULL,
  `client_id` varchar(36) DEFAULT NULL,
  `allow_case_reopen` BOOLEAN,
  `allow_case_closing` BOOLEAN,
  `allow_priority` BOOLEAN,
  `allow_type` BOOLEAN,
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
