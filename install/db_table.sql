-- CREATE DATABASE  IF NOT EXISTS `citro` /*!40100 DEFAULT CHARACTER SET latin1 */;
-- USE `citro`;



--
-- Table structure for table `bt_sys_access`
--
DROP TABLE IF EXISTS `sys_access`;
CREATE TABLE `sys_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(60) NOT NULL,
  `sys_access_groups_id` int(11) NOT NULL,
  `contacts_id` int(11) NOT NULL,
  `user_create` int(11) DEFAULT NULL,
  `user_edit` int(11) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `date_edit` datetime DEFAULT NULL,
  `loginname` varchar(100) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT NULL,
  `visibil` tinyint(1) DEFAULT '1',
  `deleted` tinyint(1) DEFAULT '0',
  `aeskey` varchar(16) DEFAULT NULL COMMENT 'ist nur falls ueber das Protocoll aes verschickt wird das Password',
  `passwordblank` varchar(50) DEFAULT NULL COMMENT 'Ist für das ersemal zum einlogen oder vergabe eines neuen passwortes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid_UNIQUE` (`guid`),
  KEY `fk_bt_sys_user_bt_sys_user_group1` (`sys_access_groups_id`),
  KEY `fk_bt_contact_access_bt_contacts1` (`contacts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




--
-- Table structure for table `sys_access_rights`
--
DROP TABLE IF EXISTS `sys_access_groups`;
CREATE TABLE  `sys_access_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'muss eindeutig sein für eine nicht auf der Id erkennbaren ansatz',
  `lft` int(11) NOT NULL COMMENT 'Ist für die Strucktur verantwortlich.',
  `rgt` int(11) NOT NULL COMMENT 'Ist für die Strucktur verantwortlich',
  `date_create` datetime NOT NULL COMMENT 'erstellungsdatum',
  `date_edit` datetime NOT NULL COMMENT 'veränderungsdatum',
  `access_owner` int(11) NOT NULL COMMENT 'der ersteller der Gruppe',
  `access_edit` int(11) NOT NULL COMMENT 'der User der die Gruppe das letze mal verändert hat',
  `visibil` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'ob gruppe sichtbar ist',
  `text` text CHARACTER SET latin1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Repräsentiert eine Gruppe. Dieser Gruppe sind eine oder mehr' AUTO_INCREMENT=1 ;




--
-- Table structure for table `sys_access_rights`
--
DROP TABLE IF EXISTS `sys_access_rights`;
CREATE TABLE `sys_access_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edata` datetime NOT NULL,
  `resource` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL COMMENT 'Die Resourcen mit namen',
  `role` varchar(65) NOT NULL COMMENT 'Enthällt die Role des Anfragenden User(U_ + GuId) oder Gruppe(G_ + groupId) ',
  `rule_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT 'Enthällt ob der Servis Erlaubt(Allow=1) oder Verboten(Deny=2) ist',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Repräsentert die Rechte einer Gruppe oder eines Access.';




--
-- Table structure for table `contacts`
--
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edata` datetime NOT NULL,
  `vdata` datetime NOT NULL,
  `user_create` int(11) NOT NULL,
  `user_edit` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `first_name` varchar(200) DEFAULT NULL,
  `first_add_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(200) NOT NULL,
  `affix_name` varchar(20) DEFAULT NULL,
  `firma` varchar(255) DEFAULT NULL,
  `position` varchar(200) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `kurz_info` varchar(45) DEFAULT NULL,
  `main_contact_address_id` int(11) DEFAULT NULL,
  `main_contact_phone_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Stellt contactdaten dar die zu einen Access oder Gast oder Vermieter gehoeren koennen';






--
-- Table structure for table `sys_request`
--
DROP TABLE IF EXISTS `sys_request`;
CREATE TABLE `sys_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `contacts`
--
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edata` datetime NOT NULL,
  `vdata` datetime NOT NULL,
  `user_create` int(11) NOT NULL,
  `user_edit` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `first_name` varchar(200) DEFAULT NULL,
  `first_add_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(200) NOT NULL,
  `affix_name` varchar(20) DEFAULT NULL,
  `firma` varchar(255) DEFAULT NULL,
  `position` varchar(200) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `kurz_info` varchar(45) DEFAULT NULL,
  `main_contact_address_id` int(11) DEFAULT NULL,
  `main_contact_phone_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Stellt contactdaten dar die zu einen Access oder Gast oder V';

