<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Directory install
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS `{$installer->getTable('directory_country')}`;
CREATE TABLE `{$installer->getTable('directory_country')}` (
  `country_id` varchar(2) NOT NULL default '',
  `iso2_code` varchar(2) NOT NULL default '',
  `iso3_code` varchar(3) NOT NULL default '',
  PRIMARY KEY  (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries';

INSERT INTO `{$installer->getTable('directory_country')}` VALUES
('AD', 'AD', 'AND'),('AE', 'AE', 'ARE'),('AF', 'AF', 'AFG'),('AG', 'AG', 'ATG'),
('AI', 'AI', 'AIA'),('AL', 'AL', 'ALB'),('AM', 'AM', 'ARM'),('AN', 'AN', 'ANT'),
('AO', 'AO', 'AGO'),('AQ', 'AQ', 'ATA'),('AR', 'AR', 'ARG'),('AS', 'AS', 'ASM'),
('AT', 'AT', 'AUT'),('AU', 'AU', 'AUS'),('AW', 'AW', 'ABW'),('AZ', 'AZ', 'AZE'),
('BA', 'BA', 'BIH'),('BB', 'BB', 'BRB'),('BD', 'BD', 'BGD'),('BE', 'BE', 'BEL'),
('BF', 'BF', 'BFA'),('BG', 'BG', 'BGR'),('BH', 'BH', 'BHR'),('BI', 'BI', 'BDI'),
('BJ', 'BJ', 'BEN'),('BM', 'BM', 'BMU'),('BN', 'BN', 'BRN'),('BO', 'BO', 'BOL'),
('BR', 'BR', 'BRA'),('BS', 'BS', 'BHS'),('BT', 'BT', 'BTN'),('BV', 'BV', 'BVT'),
('BW', 'BW', 'BWA'),('BY', 'BY', 'BLR'),('BZ', 'BZ', 'BLZ'),('CA', 'CA', 'CAN'),
('CC', 'CC', 'CCK'),('CF', 'CF', 'CAF'),('CG', 'CG', 'COG'),('CH', 'CH', 'CHE'),
('CI', 'CI', 'CIV'),('CK', 'CK', 'COK'),('CL', 'CL', 'CHL'),('CM', 'CM', 'CMR'),
('CN', 'CN', 'CHN'),('CO', 'CO', 'COL'),('CR', 'CR', 'CRI'),('CU', 'CU', 'CUB'),
('CV', 'CV', 'CPV'),('CX', 'CX', 'CXR'),('CY', 'CY', 'CYP'),('CZ', 'CZ', 'CZE'),
('DE', 'DE', 'DEU'),('DJ', 'DJ', 'DJI'),('DK', 'DK', 'DNK'),('DM', 'DM', 'DMA'),
('DO', 'DO', 'DOM'),('DZ', 'DZ', 'DZA'),('EC', 'EC', 'ECU'),('EE', 'EE', 'EST'),
('EG', 'EG', 'EGY'),('EH', 'EH', 'ESH'),('ER', 'ER', 'ERI'),('ES', 'ES', 'ESP'),
('ET', 'ET', 'ETH'),('FI', 'FI', 'FIN'),('FJ', 'FJ', 'FJI'),('FK', 'FK', 'FLK'),
('FM', 'FM', 'FSM'),('FO', 'FO', 'FRO'),('FR', 'FR', 'FRA'),('FX', 'FX', 'FXX'),
('GA', 'GA', 'GAB'),('GB', 'GB', 'GBR'),('GD', 'GD', 'GRD'),('GE', 'GE', 'GEO'),
('GF', 'GF', 'GUF'),('GH', 'GH', 'GHA'),('GI', 'GI', 'GIB'),('GL', 'GL', 'GRL'),
('GM', 'GM', 'GMB'),('GN', 'GN', 'GIN'),('GP', 'GP', 'GLP'),('GQ', 'GQ', 'GNQ'),
('GR', 'GR', 'GRC'),('GS', 'GS', 'SGS'),('GT', 'GT', 'GTM'),('GU', 'GU', 'GUM'),
('GW', 'GW', 'GNB'),('GY', 'GY', 'GUY'),('HK', 'HK', 'HKG'),('HM', 'HM', 'HMD'),
('HN', 'HN', 'HND'),('HR', 'HR', 'HRV'),('HT', 'HT', 'HTI'),('HU', 'HU', 'HUN'),
('ID', 'ID', 'IDN'),('IE', 'IE', 'IRL'),('IL', 'IL', 'ISR'),('IN', 'IN', 'IND'),
('IO', 'IO', 'IOT'),('IQ', 'IQ', 'IRQ'),('IR', 'IR', 'IRN'),('IS', 'IS', 'ISL'),
('IT', 'IT', 'ITA'),('JM', 'JM', 'JAM'),('JO', 'JO', 'JOR'),('JP', 'JP', 'JPN'),
('KE', 'KE', 'KEN'),('KG', 'KG', 'KGZ'),('KH', 'KH', 'KHM'),('KI', 'KI', 'KIR'),
('KM', 'KM', 'COM'),('KN', 'KN', 'KNA'),('KP', 'KP', 'PRK'),('KR', 'KR', 'KOR'),
('KW', 'KW', 'KWT'),('KY', 'KY', 'CYM'),('KZ', 'KZ', 'KAZ'),('LA', 'LA', 'LAO'),
('LB', 'LB', 'LBN'),('LC', 'LC', 'LCA'),('LI', 'LI', 'LIE'),('LK', 'LK', 'LKA'),
('LR', 'LR', 'LBR'),('LS', 'LS', 'LSO'),('LT', 'LT', 'LTU'),('LU', 'LU', 'LUX'),
('LV', 'LV', 'LVA'),('LY', 'LY', 'LBY'),('MA', 'MA', 'MAR'),('MC', 'MC', 'MCO'),
('MD', 'MD', 'MDA'),('MG', 'MG', 'MDG'),('MH', 'MH', 'MHL'),('MK', 'MK', 'MKD'),
('ML', 'ML', 'MLI'),('MM', 'MM', 'MMR'),('MN', 'MN', 'MNG'),('MO', 'MO', 'MAC'),
('MP', 'MP', 'MNP'),('MQ', 'MQ', 'MTQ'),('MR', 'MR', 'MRT'),('MS', 'MS', 'MSR'),
('MT', 'MT', 'MLT'),('MU', 'MU', 'MUS'),('MV', 'MV', 'MDV'),('MW', 'MW', 'MWI'),
('MX', 'MX', 'MEX'),('MY', 'MY', 'MYS'),('MZ', 'MZ', 'MOZ'),('NA', 'NA', 'NAM'),
('NC', 'NC', 'NCL'),('NE', 'NE', 'NER'),('NF', 'NF', 'NFK'),('NG', 'NG', 'NGA'),
('NI', 'NI', 'NIC'),('NL', 'NL', 'NLD'),('NO', 'NO', 'NOR'),('NP', 'NP', 'NPL'),
('NR', 'NR', 'NRU'),('NU', 'NU', 'NIU'),('NZ', 'NZ', 'NZL'),('OM', 'OM', 'OMN'),
('PA', 'PA', 'PAN'),('PE', 'PE', 'PER'),('PF', 'PF', 'PYF'),('PG', 'PG', 'PNG'),
('PH', 'PH', 'PHL'),('PK', 'PK', 'PAK'),('PL', 'PL', 'POL'),('PM', 'PM', 'SPM'),
('PN', 'PN', 'PCN'),('PR', 'PR', 'PRI'),('PT', 'PT', 'PRT'),('PW', 'PW', 'PLW'),
('PY', 'PY', 'PRY'),('QA', 'QA', 'QAT'),('RE', 'RE', 'REU'),('RO', 'RO', 'ROM'),
('RU', 'RU', 'RUS'),('RW', 'RW', 'RWA'),('SA', 'SA', 'SAU'),('SB', 'SB', 'SLB'),
('SC', 'SC', 'SYC'),('SD', 'SD', 'SDN'),('SE', 'SE', 'SWE'),('SG', 'SG', 'SGP'),
('SH', 'SH', 'SHN'),('SI', 'SI', 'SVN'),('SJ', 'SJ', 'SJM'),('SK', 'SK', 'SVK'),
('SL', 'SL', 'SLE'),('SM', 'SM', 'SMR'),('SN', 'SN', 'SEN'),('SO', 'SO', 'SOM'),
('SR', 'SR', 'SUR'),('ST', 'ST', 'STP'),('SV', 'SV', 'SLV'),('SY', 'SY', 'SYR'),
('SZ', 'SZ', 'SWZ'),('TC', 'TC', 'TCA'),('TD', 'TD', 'TCD'),('TF', 'TF', 'ATF'),
('TG', 'TG', 'TGO'),('TH', 'TH', 'THA'),('TJ', 'TJ', 'TJK'),('TK', 'TK', 'TKL'),
('TM', 'TM', 'TKM'),('TN', 'TN', 'TUN'),('TO', 'TO', 'TON'),('TR', 'TR', 'TUR'),
('TT', 'TT', 'TTO'),('TV', 'TV', 'TUV'),('TW', 'TW', 'TWN'),('TZ', 'TZ', 'TZA'),
('UA', 'UA', 'UKR'),('UG', 'UG', 'UGA'),('UM', 'UM', 'UMI'),('US', 'US', 'USA'),
('UY', 'UY', 'URY'),('UZ', 'UZ', 'UZB'),('VA', 'VA', 'VAT'),('VC', 'VC', 'VCT'),
('VE', 'VE', 'VEN'),('VG', 'VG', 'VGB'),('VI', 'VI', 'VIR'),('VN', 'VN', 'VNM'),
('VU', 'VU', 'VUT'),('WF', 'WF', 'WLF'),('WS', 'WS', 'WSM'),('YE', 'YE', 'YEM'),
('YT', 'YT', 'MYT'),('ZA', 'ZA', 'ZAF'),('ZM', 'ZM', 'ZMB'),('ZW', 'ZW', 'ZWE');

-- DROP TABLE IF EXISTS `{$installer->getTable('directory_country_format')}`;
CREATE TABLE `{$installer->getTable('directory_country_format')}` (
  `country_format_id` int(10) unsigned NOT NULL auto_increment,
  `country_id` varchar(2) NOT NULL default '',
  `type` varchar(30) NOT NULL default '',
  `format` text NOT NULL,
  PRIMARY KEY  (`country_format_id`),
  UNIQUE KEY `country_type` (`country_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries format';

-- DROP TABLE IF EXISTS `{$installer->getTable('directory_country_region')}`;
CREATE TABLE `{$installer->getTable('directory_country_region')}` (
  `region_id` mediumint(8) unsigned NOT NULL auto_increment,
  `country_id` varchar(4) NOT NULL default '0',
  `code` varchar(32) NOT NULL default '',
  `default_name` varchar(255) default NULL,
  PRIMARY KEY  (`region_id`),
  KEY `FK_REGION_COUNTRY` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Country regions';

INSERT INTO `{$installer->getTable('directory_country_region')}` VALUES
(1, 'US', 'AL', 'Alabama'),(2, 'US', 'AK', 'Alaska'),(3, 'US', 'AS', 'American Samoa'),
(4, 'US', 'AZ', 'Arizona'),(5, 'US', 'AR', 'Arkansas'),(6, 'US', 'AF', 'Armed Forces Africa'),
(7, 'US', 'AA', 'Armed Forces Americas'),(8, 'US', 'AC', 'Armed Forces Canada'),(9, 'US', 'AE', 'Armed Forces Europe'),
(10, 'US', 'AM', 'Armed Forces Middle East'),(11, 'US', 'AP', 'Armed Forces Pacific'),
(12, 'US', 'CA', 'California'),(13, 'US', 'CO', 'Colorado'),(14, 'US', 'CT', 'Connecticut'),
(15, 'US', 'DE', 'Delaware'),(16, 'US', 'DC', 'District of Columbia'),(17, 'US', 'FM', 'Federated States Of Micronesia'),
(18, 'US', 'FL', 'Florida'),(19, 'US', 'GA', 'Georgia'),(20, 'US', 'GU', 'Guam'),
(21, 'US', 'HI', 'Hawaii'),(22, 'US', 'ID', 'Idaho'),(23, 'US', 'IL', 'Illinois'),
(24, 'US', 'IN', 'Indiana'),(25, 'US', 'IA', 'Iowa'),(26, 'US', 'KS', 'Kansas'),
(27, 'US', 'KY', 'Kentucky'),(28, 'US', 'LA', 'Louisiana'),(29, 'US', 'ME', 'Maine'),
(30, 'US', 'MH', 'Marshall Islands'),(31, 'US', 'MD', 'Maryland'),(32, 'US', 'MA', 'Massachusetts'),
(33, 'US', 'MI', 'Michigan'),(34, 'US', 'MN', 'Minnesota'),(35, 'US', 'MS', 'Mississippi'),
(36, 'US', 'MO', 'Missouri'),(37, 'US', 'MT', 'Montana'),(38, 'US', 'NE', 'Nebraska'),
(39, 'US', 'NV', 'Nevada'),(40, 'US', 'NH', 'New Hampshire'),(41, 'US', 'NJ', 'New Jersey'),
(42, 'US', 'NM', 'New Mexico'),(43, 'US', 'NY', 'New York'),(44, 'US', 'NC', 'North Carolina'),
(45, 'US', 'ND', 'North Dakota'),(46, 'US', 'MP', 'Northern Mariana Islands'),(47, 'US', 'OH', 'Ohio'),
(48, 'US', 'OK', 'Oklahoma'),(49, 'US', 'OR', 'Oregon'),(50, 'US', 'PW', 'Palau'),
(51, 'US', 'PA', 'Pennsylvania'),(52, 'US', 'PR', 'Puerto Rico'),(53, 'US', 'RI', 'Rhode Island'),
(54, 'US', 'SC', 'South Carolina'),(55, 'US', 'SD', 'South Dakota'),(56, 'US', 'TN', 'Tennessee'),
(57, 'US', 'TX', 'Texas'),(58, 'US', 'UT', 'Utah'),(59, 'US', 'VT', 'Vermont'),
(60, 'US', 'VI', 'Virgin Islands'),(61, 'US', 'VA', 'Virginia'),(62, 'US', 'WA', 'Washington'),
(63, 'US', 'WV', 'West Virginia'),(64, 'US', 'WI', 'Wisconsin'),(65, 'US', 'WY', 'Wyoming'),
(66, 'CA', 'AB', 'Alberta'),(67, 'CA', 'BC', 'British Columbia'),(68, 'CA', 'MB', 'Manitoba'),
(69, 'CA', 'NF', 'Newfoundland'),(70, 'CA', 'NB', 'New Brunswick'),(71, 'CA', 'NS', 'Nova Scotia'),
(72, 'CA', 'NT', 'Northwest Territories'),(73, 'CA', 'NU', 'Nunavut'),(74, 'CA', 'ON', 'Ontario'),
(75, 'CA', 'PE', 'Prince Edward Island'),(76, 'CA', 'QC', 'Quebec'),(77, 'CA', 'SK', 'Saskatchewan'),
(78, 'CA', 'YT', 'Yukon Territory'),(79, 'DE', 'NDS', 'Niedersachsen'),(80, 'DE', 'BAW', 'Baden-Württemberg'),
(81, 'DE', 'BAY', 'Bayern'),(82, 'DE', 'BER', 'Berlin'),(83, 'DE', 'BRG', 'Brandenburg'),
(84, 'DE', 'BRE', 'Bremen'),(85, 'DE', 'HAM', 'Hamburg'),(86, 'DE', 'HES', 'Hessen'),
(87, 'DE', 'MEC', 'Mecklenburg-Vorpommern'),(88, 'DE', 'NRW', 'Nordrhein-Westfalen'),(89, 'DE', 'RHE', 'Rheinland-Pfalz'),
(90, 'DE', 'SAR', 'Saarland'),(91, 'DE', 'SAS', 'Sachsen'),(92, 'DE', 'SAC', 'Sachsen-Anhalt'),
(93, 'DE', 'SCN', 'Schleswig-Holstein'),(94, 'DE', 'THE', 'Thüringen'),(95, 'AT', 'WI', 'Wien'),
(96, 'AT', 'NO', 'Niederösterreich'),(97, 'AT', 'OO', 'Oberösterreich'),(98, 'AT', 'SB', 'Salzburg'),
(99, 'AT', 'KN', 'Kärnten'),(100, 'AT', 'ST', 'Steiermark'),(101, 'AT', 'TI', 'Tirol'),
(102, 'AT', 'BL', 'Burgenland'),(103, 'AT', 'VB', 'Voralberg'),(104, 'CH', 'AG', 'Aargau'),
(105, 'CH', 'AI', 'Appenzell Innerrhoden'),(106, 'CH', 'AR', 'Appenzell Ausserrhoden'),(107, 'CH', 'BE', 'Bern'),
(108, 'CH', 'BL', 'Basel-Landschaft'),(109, 'CH', 'BS', 'Basel-Stadt'),(110, 'CH', 'FR', 'Freiburg'),
(111, 'CH', 'GE', 'Genf'),(112, 'CH', 'GL', 'Glarus'),(113, 'CH', 'JU', 'Graubünden'),
(114, 'CH', 'JU', 'Jura'),(115, 'CH', 'LU', 'Luzern'),(116, 'CH', 'NE', 'Neuenburg'),
(117, 'CH', 'NW', 'Nidwalden'),(118, 'CH', 'OW', 'Obwalden'),(119, 'CH', 'SG', 'St. Gallen'),
(120, 'CH', 'SH', 'Schaffhausen'),(121, 'CH', 'SO', 'Solothurn'),(122, 'CH', 'SZ', 'Schwyz'),
(123, 'CH', 'TG', 'Thurgau'),(124, 'CH', 'TI', 'Tessin'),(125, 'CH', 'UR', 'Uri'),
(126, 'CH', 'VD', 'Waadt'),(127, 'CH', 'VS', 'Wallis'),(128, 'CH', 'ZG', 'Zug'),
(129, 'CH', 'ZH', 'Zürich'),(130, 'ES', 'A Coruсa', 'A Coruña'),(131, 'ES', 'Alava', 'Alava'),
(132, 'ES', 'Albacete', 'Albacete'),(133, 'ES', 'Alicante', 'Alicante'),(134, 'ES', 'Almeria', 'Almeria'),
(135, 'ES', 'Asturias', 'Asturias'),(136, 'ES', 'Avila', 'Avila'),(137, 'ES', 'Badajoz', 'Badajoz'),
(138, 'ES', 'Baleares', 'Baleares'),(139, 'ES', 'Barcelona', 'Barcelona'),
(140, 'ES', 'Burgos', 'Burgos'),(141, 'ES', 'Caceres', 'Caceres'),(142, 'ES', 'Cadiz', 'Cadiz'),
(143, 'ES', 'Cantabria', 'Cantabria'),(144, 'ES', 'Castellon', 'Castellon'),(145, 'ES', 'Ceuta', 'Ceuta'),
(146, 'ES', 'Ciudad Real', 'Ciudad Real'),(147, 'ES', 'Cordoba', 'Cordoba'),(148, 'ES', 'Cuenca', 'Cuenca'),
(149, 'ES', 'Girona', 'Girona'),(150, 'ES', 'Granada', 'Granada'),(151, 'ES', 'Guadalajara', 'Guadalajara'),
(152, 'ES', 'Guipuzcoa', 'Guipuzcoa'),(153, 'ES', 'Huelva', 'Huelva'),(154, 'ES', 'Huesca', 'Huesca'),
(155, 'ES', 'Jaen', 'Jaen'),(156, 'ES', 'La Rioja', 'La Rioja'),(157, 'ES', 'Las Palmas', 'Las Palmas'),
(158, 'ES', 'Leon', 'Leon'),(159, 'ES', 'Lleida', 'Lleida'),(160, 'ES', 'Lugo', 'Lugo'),
(161, 'ES', 'Madrid', 'Madrid'),(162, 'ES', 'Malaga', 'Malaga'),(163, 'ES', 'Melilla', 'Melilla'),
(164, 'ES', 'Murcia', 'Murcia'),(165, 'ES', 'Navarra', 'Navarra'),(166, 'ES', 'Ourense', 'Ourense'),
(167, 'ES', 'Palencia', 'Palencia'),(168, 'ES', 'Pontevedra', 'Pontevedra'),(169, 'ES', 'Salamanca', 'Salamanca'),
(170, 'ES', 'Santa Cruz de Tenerife', 'Santa Cruz de Tenerife'),(171, 'ES', 'Segovia', 'Segovia'),(172, 'ES', 'Sevilla', 'Sevilla'),
(173, 'ES', 'Soria', 'Soria'),(174, 'ES', 'Tarragona', 'Tarragona'),(175, 'ES', 'Teruel', 'Teruel'),
(176, 'ES', 'Toledo', 'Toledo'),(177, 'ES', 'Valencia', 'Valencia'),(178, 'ES', 'Valladolid', 'Valladolid'),
(179, 'ES', 'Vizcaya', 'Vizcaya'),(180, 'ES', 'Zamora', 'Zamora'),(181, 'ES', 'Zaragoza', 'Zaragoza');

-- DROP TABLE IF EXISTS `{$installer->getTable('directory_country_region_name')}`;
CREATE TABLE `{$installer->getTable('directory_country_region_name')}` (
  `locale` varchar(8) NOT NULL default '',
  `region_id` mediumint(8) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`locale`,`region_id`),
  KEY `FK_DIRECTORY_REGION_NAME_REGION` (`region_id`),
  CONSTRAINT `FK_DIRECTORY_REGION_NAME_REGION` FOREIGN KEY (`region_id`) REFERENCES {$installer->getTable('directory_country_region')} (`region_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Regions names';

INSERT INTO `{$installer->getTable('directory_country_region_name')}` VALUES
('en_US', 1, 'Alabama'),('en_US', 2, 'Alaska'),('en_US', 3, 'American Samoa'),
('en_US', 4, 'Arizona'),('en_US', 5, 'Arkansas'),('en_US', 6, 'Armed Forces Africa'),
('en_US', 7, 'Armed Forces Americas'),
('en_US', 8, 'Armed Forces Canada'),('en_US', 9, 'Armed Forces Europe'),
('en_US', 10, 'Armed Forces Middle East'),
('en_US', 11, 'Armed Forces Pacific'),('en_US', 12, 'California'),
('en_US', 13, 'Colorado'),('en_US', 14, 'Connecticut'),('en_US', 15, 'Delaware'),
('en_US', 16, 'District of Columbia'),('en_US', 17, 'Federated States Of Micronesia'),('en_US', 18, 'Florida'),
('en_US', 19, 'Georgia'),('en_US', 20, 'Guam'),('en_US', 21, 'Hawaii'),
('en_US', 22, 'Idaho'),('en_US', 23, 'Illinois'),('en_US', 24, 'Indiana'),
('en_US', 25, 'Iowa'),('en_US', 26, 'Kansas'),('en_US', 27, 'Kentucky'),
('en_US', 28, 'Louisiana'),('en_US', 29, 'Maine'),('en_US', 30, 'Marshall Islands'),
('en_US', 31, 'Maryland'),('en_US', 32, 'Massachusetts'),('en_US', 33, 'Michigan'),
('en_US', 34, 'Minnesota'),('en_US', 35, 'Mississippi'),('en_US', 36, 'Missouri'),
('en_US', 37, 'Montana'),('en_US', 38, 'Nebraska'),('en_US', 39, 'Nevada'),
('en_US', 40, 'New Hampshire'),('en_US', 41, 'New Jersey'),('en_US', 42, 'New Mexico'),
('en_US', 43, 'New York'),('en_US', 44, 'North Carolina'),('en_US', 45, 'North Dakota'),
('en_US', 46, 'Northern Mariana Islands'),('en_US', 47, 'Ohio'),('en_US', 48, 'Oklahoma'),
('en_US', 49, 'Oregon'),('en_US', 50, 'Palau'),('en_US', 51, 'Pennsylvania'),
('en_US', 52, 'Puerto Rico'),('en_US', 53, 'Rhode Island'),('en_US', 54, 'South Carolina'),
('en_US', 55, 'South Dakota'),('en_US', 56, 'Tennessee'),('en_US', 57, 'Texas'),
('en_US', 58, 'Utah'),('en_US', 59, 'Vermont'),('en_US', 60, 'Virgin Islands'),
('en_US', 61, 'Virginia'),('en_US', 62, 'Washington'),('en_US', 63, 'West Virginia'),
('en_US', 64, 'Wisconsin'),('en_US', 65, 'Wyoming'),('en_US', 66, 'Alberta'),
('en_US', 67, 'British Columbia'),('en_US', 68, 'Manitoba'),('en_US', 69, 'Newfoundland'),
('en_US', 70, 'New Brunswick'),('en_US', 71, 'Nova Scotia'),('en_US', 72, 'Northwest Territories'),
('en_US', 73, 'Nunavut'),('en_US', 74, 'Ontario'),('en_US', 75, 'Prince Edward Island'),
('en_US', 76, 'Quebec'),('en_US', 77, 'Saskatchewan'),('en_US', 78, 'Yukon Territory'),
('en_US', 79, 'Niedersachsen'),('en_US', 80, 'Baden-Württemberg'),('en_US', 81, 'Bayern'),
('en_US', 82, 'Berlin'),('en_US', 83, 'Brandenburg'),('en_US', 84, 'Bremen'),
('en_US', 85, 'Hamburg'),('en_US', 86, 'Hessen'),('en_US', 87, 'Mecklenburg-Vorpommern'),
('en_US', 88, 'Nordrhein-Westfalen'),('en_US', 89, 'Rheinland-Pfalz'),('en_US', 90, 'Saarland'),
('en_US', 91, 'Sachsen'),('en_US', 92, 'Sachsen-Anhalt'),('en_US', 93, 'Schleswig-Holstein'),
('en_US', 94, 'Thüringen'),('en_US', 95, 'Wien'),('en_US', 96, 'Niederösterreich'),
('en_US', 97, 'Oberösterreich'),('en_US', 98, 'Salzburg'),('en_US', 99, 'Kärnten'),
('en_US', 100, 'Steiermark'),('en_US', 101, 'Tirol'),('en_US', 102, 'Burgenland'),
('en_US', 103, 'Voralberg'),('en_US', 104, 'Aargau'),('en_US', 105, 'Appenzell Innerrhoden'),
('en_US', 106, 'Appenzell Ausserrhoden'),('en_US', 107, 'Bern'),('en_US', 108, 'Basel-Landschaft'),
('en_US', 109, 'Basel-Stadt'),('en_US', 110, 'Freiburg'),('en_US', 111, 'Genf'),
('en_US', 112, 'Glarus'),('en_US', 113, 'Graubünden'),('en_US', 114, 'Jura'),
('en_US', 115, 'Luzern'),('en_US', 116, 'Neuenburg'),('en_US', 117, 'Nidwalden'),
('en_US', 118, 'Obwalden'),('en_US', 119, 'St. Gallen'),('en_US', 120, 'Schaffhausen'),
('en_US', 121, 'Solothurn'),('en_US', 122, 'Schwyz'),('en_US', 123, 'Thurgau'),
('en_US', 124, 'Tessin'),('en_US', 125, 'Uri'),('en_US', 126, 'Waadt'),
('en_US', 127, 'Wallis'),('en_US', 128, 'Zug'),('en_US', 129, 'Zürich'),
('en_US', 130, 'A Coruña'),('en_US', 131, 'Alava'),('en_US', 132, 'Albacete'),
('en_US', 133, 'Alicante'),('en_US', 134, 'Almeria'),('en_US', 135, 'Asturias'),
('en_US', 136, 'Avila'),('en_US', 137, 'Badajoz'),('en_US', 138, 'Baleares'),
('en_US', 139, 'Barcelona'),('en_US', 140, 'Burgos'),('en_US', 141, 'Caceres'),
('en_US', 142, 'Cadiz'),('en_US', 143, 'Cantabria'),('en_US', 144, 'Castellon'),
('en_US', 145, 'Ceuta'),('en_US', 146, 'Ciudad Real'),('en_US', 147, 'Cordoba'),
('en_US', 148, 'Cuenca'),('en_US', 149, 'Girona'),('en_US', 150, 'Granada'),
('en_US', 151, 'Guadalajara'),('en_US', 152, 'Guipuzcoa'),('en_US', 153, 'Huelva'),
('en_US', 154, 'Huesca'),('en_US', 155, 'Jaen'),('en_US', 156, 'La Rioja'),
('en_US', 157, 'Las Palmas'),('en_US', 158, 'Leon'),('en_US', 159, 'Lleida'),
('en_US', 160, 'Lugo'),('en_US', 161, 'Madrid'),('en_US', 162, 'Malaga'),
('en_US', 163, 'Melilla'),('en_US', 164, 'Murcia'),('en_US', 165, 'Navarra'),
('en_US', 166, 'Ourense'),('en_US', 167, 'Palencia'),('en_US', 168, 'Pontevedra'),
('en_US', 169, 'Salamanca'),('en_US', 170, 'Santa Cruz de Tenerife'),('en_US', 171, 'Segovia'),
('en_US', 172, 'Sevilla'),('en_US', 173, 'Soria'),('en_US', 174, 'Tarragona'),
('en_US', 175, 'Teruel'),('en_US', 176, 'Toledo'),('en_US', 177, 'Valencia'),
('en_US', 178, 'Valladolid'),('en_US', 179, 'Vizcaya'),('en_US', 180, 'Zamora'),
('en_US', 181, 'Zaragoza');

-- DROP TABLE IF EXISTS `{$installer->getTable('directory_currency_rate')}`;
CREATE TABLE `{$installer->getTable('directory_currency_rate')}` (
  `currency_from` char(3) NOT NULL default '',
  `currency_to` char(3) NOT NULL default '',
  `rate` decimal(24,12) NOT NULL default '0.000000000000',
  PRIMARY KEY  (`currency_from`,`currency_to`),
  KEY `FK_CURRENCY_RATE_TO` (`currency_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$installer->getTable('directory_currency_rate')}` VALUES
('EUR', 'EUR', 1.000000000000),('EUR', 'USD', 1.415000000000),
('USD', 'EUR', 0.706700000000),('USD', 'USD', 1.000000000000);
");
$installer->endSetup();
