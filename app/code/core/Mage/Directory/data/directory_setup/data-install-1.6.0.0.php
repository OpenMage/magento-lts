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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

/**
 * Fill table directory/country
 */
$data = array(
    array('AD', 'AD', 'AND'), array('AE', 'AE', 'ARE'), array('AF', 'AF', 'AFG'), array('AG', 'AG', 'ATG'),
    array('AI', 'AI', 'AIA'), array('AL', 'AL', 'ALB'), array('AM', 'AM', 'ARM'), array('AN', 'AN', 'ANT'),
    array('AO', 'AO', 'AGO'), array('AQ', 'AQ', 'ATA'), array('AR', 'AR', 'ARG'), array('AS', 'AS', 'ASM'),
    array('AT', 'AT', 'AUT'), array('AU', 'AU', 'AUS'), array('AW', 'AW', 'ABW'), array('AX', 'AX', 'ALA'),
    array('AZ', 'AZ', 'AZE'), array('BA', 'BA', 'BIH'), array('BB', 'BB', 'BRB'), array('BD', 'BD', 'BGD'),
    array('BE', 'BE', 'BEL'), array('BF', 'BF', 'BFA'), array('BG', 'BG', 'BGR'), array('BH', 'BH', 'BHR'),
    array('BI', 'BI', 'BDI'), array('BJ', 'BJ', 'BEN'), array('BL', 'BL', 'BLM'), array('BM', 'BM', 'BMU'),
    array('BN', 'BN', 'BRN'), array('BO', 'BO', 'BOL'), array('BR', 'BR', 'BRA'), array('BS', 'BS', 'BHS'),
    array('BT', 'BT', 'BTN'), array('BV', 'BV', 'BVT'), array('BW', 'BW', 'BWA'), array('BY', 'BY', 'BLR'),
    array('BZ', 'BZ', 'BLZ'), array('CA', 'CA', 'CAN'), array('CC', 'CC', 'CCK'), array('CD', 'CD', 'COD'),
    array('CF', 'CF', 'CAF'), array('CG', 'CG', 'COG'), array('CH', 'CH', 'CHE'), array('CI', 'CI', 'CIV'),
    array('CK', 'CK', 'COK'), array('CL', 'CL', 'CHL'), array('CM', 'CM', 'CMR'), array('CN', 'CN', 'CHN'),
    array('CO', 'CO', 'COL'), array('CR', 'CR', 'CRI'), array('CU', 'CU', 'CUB'), array('CV', 'CV', 'CPV'),
    array('CX', 'CX', 'CXR'), array('CY', 'CY', 'CYP'), array('CZ', 'CZ', 'CZE'), array('DE', 'DE', 'DEU'),
    array('DJ', 'DJ', 'DJI'), array('DK', 'DK', 'DNK'), array('DM', 'DM', 'DMA'), array('DO', 'DO', 'DOM'),
    array('DZ', 'DZ', 'DZA'), array('EC', 'EC', 'ECU'), array('EE', 'EE', 'EST'), array('EG', 'EG', 'EGY'),
    array('EH', 'EH', 'ESH'), array('ER', 'ER', 'ERI'), array('ES', 'ES', 'ESP'), array('ET', 'ET', 'ETH'),
    array('FI', 'FI', 'FIN'), array('FJ', 'FJ', 'FJI'), array('FK', 'FK', 'FLK'), array('FM', 'FM', 'FSM'),
    array('FO', 'FO', 'FRO'), array('FR', 'FR', 'FRA'), array('GA', 'GA', 'GAB'), array('GB', 'GB', 'GBR'),
    array('GD', 'GD', 'GRD'), array('GE', 'GE', 'GEO'), array('GF', 'GF', 'GUF'), array('GG', 'GG', 'GGY'),
    array('GH', 'GH', 'GHA'), array('GI', 'GI', 'GIB'), array('GL', 'GL', 'GRL'), array('GM', 'GM', 'GMB'),
    array('GN', 'GN', 'GIN'), array('GP', 'GP', 'GLP'), array('GQ', 'GQ', 'GNQ'), array('GR', 'GR', 'GRC'),
    array('GS', 'GS', 'SGS'), array('GT', 'GT', 'GTM'), array('GU', 'GU', 'GUM'), array('GW', 'GW', 'GNB'),
    array('GY', 'GY', 'GUY'), array('HK', 'HK', 'HKG'), array('HM', 'HM', 'HMD'), array('HN', 'HN', 'HND'),
    array('HR', 'HR', 'HRV'), array('HT', 'HT', 'HTI'), array('HU', 'HU', 'HUN'), array('ID', 'ID', 'IDN'),
    array('IE', 'IE', 'IRL'), array('IL', 'IL', 'ISR'), array('IM', 'IM', 'IMN'), array('IN', 'IN', 'IND'),
    array('IO', 'IO', 'IOT'), array('IQ', 'IQ', 'IRQ'), array('IR', 'IR', 'IRN'), array('IS', 'IS', 'ISL'),
    array('IT', 'IT', 'ITA'), array('JE', 'JE', 'JEY'), array('JM', 'JM', 'JAM'), array('JO', 'JO', 'JOR'),
    array('JP', 'JP', 'JPN'), array('KE', 'KE', 'KEN'), array('KG', 'KG', 'KGZ'), array('KH', 'KH', 'KHM'),
    array('KI', 'KI', 'KIR'), array('KM', 'KM', 'COM'), array('KN', 'KN', 'KNA'), array('KP', 'KP', 'PRK'),
    array('KR', 'KR', 'KOR'), array('KW', 'KW', 'KWT'), array('KY', 'KY', 'CYM'), array('KZ', 'KZ', 'KAZ'),
    array('LA', 'LA', 'LAO'), array('LB', 'LB', 'LBN'), array('LC', 'LC', 'LCA'), array('LI', 'LI', 'LIE'),
    array('LK', 'LK', 'LKA'), array('LR', 'LR', 'LBR'), array('LS', 'LS', 'LSO'), array('LT', 'LT', 'LTU'),
    array('LU', 'LU', 'LUX'), array('LV', 'LV', 'LVA'), array('LY', 'LY', 'LBY'), array('MA', 'MA', 'MAR'),
    array('MC', 'MC', 'MCO'), array('MD', 'MD', 'MDA'), array('ME', 'ME', 'MNE'), array('MF', 'MF', 'MAF'),
    array('MG', 'MG', 'MDG'), array('MH', 'MH', 'MHL'), array('MK', 'MK', 'MKD'), array('ML', 'ML', 'MLI'),
    array('MM', 'MM', 'MMR'), array('MN', 'MN', 'MNG'), array('MO', 'MO', 'MAC'), array('MP', 'MP', 'MNP'),
    array('MQ', 'MQ', 'MTQ'), array('MR', 'MR', 'MRT'), array('MS', 'MS', 'MSR'), array('MT', 'MT', 'MLT'),
    array('MU', 'MU', 'MUS'), array('MV', 'MV', 'MDV'), array('MW', 'MW', 'MWI'), array('MX', 'MX', 'MEX'),
    array('MY', 'MY', 'MYS'), array('MZ', 'MZ', 'MOZ'), array('NA', 'NA', 'NAM'), array('NC', 'NC', 'NCL'),
    array('NE', 'NE', 'NER'), array('NF', 'NF', 'NFK'), array('NG', 'NG', 'NGA'), array('NI', 'NI', 'NIC'),
    array('NL', 'NL', 'NLD'), array('NO', 'NO', 'NOR'), array('NP', 'NP', 'NPL'), array('NR', 'NR', 'NRU'),
    array('NU', 'NU', 'NIU'), array('NZ', 'NZ', 'NZL'), array('OM', 'OM', 'OMN'), array('PA', 'PA', 'PAN'),
    array('PE', 'PE', 'PER'), array('PF', 'PF', 'PYF'), array('PG', 'PG', 'PNG'), array('PH', 'PH', 'PHL'),
    array('PK', 'PK', 'PAK'), array('PL', 'PL', 'POL'), array('PM', 'PM', 'SPM'), array('PN', 'PN', 'PCN'),
    array('PR', 'PR', 'PRI'), array('PS', 'PS', 'PSE'), array('PT', 'PT', 'PRT'), array('PW', 'PW', 'PLW'),
    array('PY', 'PY', 'PRY'), array('QA', 'QA', 'QAT'), array('RE', 'RE', 'REU'), array('RO', 'RO', 'ROU'),
    array('RS', 'RS', 'SRB'), array('RU', 'RU', 'RUS'), array('RW', 'RW', 'RWA'), array('SA', 'SA', 'SAU'),
    array('SB', 'SB', 'SLB'), array('SC', 'SC', 'SYC'), array('SD', 'SD', 'SDN'), array('SE', 'SE', 'SWE'),
    array('SG', 'SG', 'SGP'), array('SH', 'SH', 'SHN'), array('SI', 'SI', 'SVN'), array('SJ', 'SJ', 'SJM'),
    array('SK', 'SK', 'SVK'), array('SL', 'SL', 'SLE'), array('SM', 'SM', 'SMR'), array('SN', 'SN', 'SEN'),
    array('SO', 'SO', 'SOM'), array('SR', 'SR', 'SUR'), array('ST', 'ST', 'STP'), array('SV', 'SV', 'SLV'),
    array('SY', 'SY', 'SYR'), array('SZ', 'SZ', 'SWZ'), array('TC', 'TC', 'TCA'), array('TD', 'TD', 'TCD'),
    array('TF', 'TF', 'ATF'), array('TG', 'TG', 'TGO'), array('TH', 'TH', 'THA'), array('TJ', 'TJ', 'TJK'),
    array('TK', 'TK', 'TKL'), array('TL', 'TL', 'TLS'), array('TM', 'TM', 'TKM'), array('TN', 'TN', 'TUN'),
    array('TO', 'TO', 'TON'), array('TR', 'TR', 'TUR'), array('TT', 'TT', 'TTO'), array('TV', 'TV', 'TUV'),
    array('TW', 'TW', 'TWN'), array('TZ', 'TZ', 'TZA'), array('UA', 'UA', 'UKR'), array('UG', 'UG', 'UGA'),
    array('UM', 'UM', 'UMI'), array('US', 'US', 'USA'), array('UY', 'UY', 'URY'), array('UZ', 'UZ', 'UZB'),
    array('VA', 'VA', 'VAT'), array('VC', 'VC', 'VCT'), array('VE', 'VE', 'VEN'), array('VG', 'VG', 'VGB'),
    array('VI', 'VI', 'VIR'), array('VN', 'VN', 'VNM'), array('VU', 'VU', 'VUT'), array('WF', 'WF', 'WLF'),
    array('WS', 'WS', 'WSM'), array('YE', 'YE', 'YEM'), array('YT', 'YT', 'MYT'), array('ZA', 'ZA', 'ZAF'),
    array('ZM', 'ZM', 'ZMB'), array('ZW', 'ZW', 'ZWE')
);

$columns = array('country_id', 'iso2_code', 'iso3_code');
$installer->getConnection()->insertArray($installer->getTable('directory/country'), $columns, $data);

/**
 * Fill table directory/country_region
 * Fill table directory/country_region_name for en_US locale
 */
$data = array(
    array('US', 'AL', 'Alabama'), array('US', 'AK', 'Alaska'), array('US', 'AS', 'American Samoa'),
    array('US', 'AZ', 'Arizona'), array('US', 'AR', 'Arkansas'), array('US', 'AF', 'Armed Forces Africa'),
    array('US', 'AA', 'Armed Forces Americas'), array('US', 'AC', 'Armed Forces Canada'),
    array('US', 'AE', 'Armed Forces Europe'), array('US', 'AM', 'Armed Forces Middle East'),
    array('US', 'AP', 'Armed Forces Pacific'), array('US', 'CA', 'California'), array('US', 'CO', 'Colorado'),
    array('US', 'CT', 'Connecticut'), array('US', 'DE', 'Delaware'), array('US', 'DC', 'District of Columbia'),
    array('US', 'FM', 'Federated States Of Micronesia'), array('US', 'FL', 'Florida'), array('US', 'GA', 'Georgia'),
    array('US', 'GU', 'Guam'), array('US', 'HI', 'Hawaii'), array('US', 'ID', 'Idaho'), array('US', 'IL', 'Illinois'),
    array('US', 'IN', 'Indiana'), array('US', 'IA', 'Iowa'), array('US', 'KS', 'Kansas'), array('US', 'KY', 'Kentucky'),
    array('US', 'LA', 'Louisiana'), array('US', 'ME', 'Maine'), array('US', 'MH', 'Marshall Islands'),
    array('US', 'MD', 'Maryland'), array('US', 'MA', 'Massachusetts'), array('US', 'MI', 'Michigan'),
    array('US', 'MN', 'Minnesota'), array('US', 'MS', 'Mississippi'), array('US', 'MO', 'Missouri'),
    array('US', 'MT', 'Montana'), array('US', 'NE', 'Nebraska'), array('US', 'NV', 'Nevada'),
    array('US', 'NH', 'New Hampshire'), array('US', 'NJ', 'New Jersey'), array('US', 'NM', 'New Mexico'),
    array('US', 'NY', 'New York'), array('US', 'NC', 'North Carolina'), array('US', 'ND', 'North Dakota'),
    array('US', 'MP', 'Northern Mariana Islands'), array('US', 'OH', 'Ohio'), array('US', 'OK', 'Oklahoma'),
    array('US', 'OR', 'Oregon'), array('US', 'PW', 'Palau'), array('US', 'PA', 'Pennsylvania'),
    array('US', 'PR', 'Puerto Rico'), array('US', 'RI', 'Rhode Island'), array('US', 'SC', 'South Carolina'),
    array('US', 'SD', 'South Dakota'), array('US', 'TN', 'Tennessee'), array('US', 'TX', 'Texas'),
    array('US', 'UT', 'Utah'), array('US', 'VT', 'Vermont'), array('US', 'VI', 'Virgin Islands'),
    array('US', 'VA', 'Virginia'), array('US', 'WA', 'Washington'), array('US', 'WV', 'West Virginia'),
    array('US', 'WI', 'Wisconsin'), array('US', 'WY', 'Wyoming'), array('CA', 'AB', 'Alberta'),
    array('CA', 'BC', 'British Columbia'), array('CA', 'MB', 'Manitoba'),
    array('CA', 'NL', 'Newfoundland and Labrador'), array('CA', 'NB', 'New Brunswick'),
    array('CA', 'NS', 'Nova Scotia'), array('CA', 'NT', 'Northwest Territories'), array('CA', 'NU', 'Nunavut'),
    array('CA', 'ON', 'Ontario'), array('CA', 'PE', 'Prince Edward Island'), array('CA', 'QC', 'Quebec'),
    array('CA', 'SK', 'Saskatchewan'), array('CA', 'YT', 'Yukon Territory'), array('DE', 'NDS', 'Niedersachsen'),
    array('DE', 'BAW', 'Baden-Württemberg'), array('DE', 'BAY', 'Bayern'), array('DE', 'BER', 'Berlin'),
    array('DE', 'BRG', 'Brandenburg'), array('DE', 'BRE', 'Bremen'), array('DE', 'HAM', 'Hamburg'),
    array('DE', 'HES', 'Hessen'), array('DE', 'MEC', 'Mecklenburg-Vorpommern'),
    array('DE', 'NRW', 'Nordrhein-Westfalen'), array('DE', 'RHE', 'Rheinland-Pfalz'), array('DE', 'SAR', 'Saarland'),
    array('DE', 'SAS', 'Sachsen'), array('DE', 'SAC', 'Sachsen-Anhalt'), array('DE', 'SCN', 'Schleswig-Holstein'),
    array('DE', 'THE', 'Thüringen'), array('AT', 'WI', 'Wien'), array('AT', 'NO', 'Niederösterreich'),
    array('AT', 'OO', 'Oberösterreich'), array('AT', 'SB', 'Salzburg'), array('AT', 'KN', 'Kärnten'),
    array('AT', 'ST', 'Steiermark'), array('AT', 'TI', 'Tirol'), array('AT', 'BL', 'Burgenland'),
    array('AT', 'VB', 'Voralberg'), array('CH', 'AG', 'Aargau'), array('CH', 'AI', 'Appenzell Innerrhoden'),
    array('CH', 'AR', 'Appenzell Ausserrhoden'), array('CH', 'BE', 'Bern'), array('CH', 'BL', 'Basel-Landschaft'),
    array('CH', 'BS', 'Basel-Stadt'), array('CH', 'FR', 'Freiburg'), array('CH', 'GE', 'Genf'),
    array('CH', 'GL', 'Glarus'), array('CH', 'GR', 'Graubünden'), array('CH', 'JU', 'Jura'),
    array('CH', 'LU', 'Luzern'), array('CH', 'NE', 'Neuenburg'), array('CH', 'NW', 'Nidwalden'),
    array('CH', 'OW', 'Obwalden'), array('CH', 'SG', 'St. Gallen'), array('CH', 'SH', 'Schaffhausen'),
    array('CH', 'SO', 'Solothurn'), array('CH', 'SZ', 'Schwyz'), array('CH', 'TG', 'Thurgau'),
    array('CH', 'TI', 'Tessin'), array('CH', 'UR', 'Uri'), array('CH', 'VD', 'Waadt'), array('CH', 'VS', 'Wallis'),
    array('CH', 'ZG', 'Zug'), array('CH', 'ZH', 'Zürich'), array('ES', 'A Coruсa', 'A Coruña'),
    array('ES', 'Alava', 'Alava'), array('ES', 'Albacete', 'Albacete'), array('ES', 'Alicante', 'Alicante'),
    array('ES', 'Almeria', 'Almeria'), array('ES', 'Asturias', 'Asturias'), array('ES', 'Avila', 'Avila'),
    array('ES', 'Badajoz', 'Badajoz'), array('ES', 'Baleares', 'Baleares'), array('ES', 'Barcelona', 'Barcelona'),
    array('ES', 'Burgos', 'Burgos'), array('ES', 'Caceres', 'Caceres'), array('ES', 'Cadiz', 'Cadiz'),
    array('ES', 'Cantabria', 'Cantabria'), array('ES', 'Castellon', 'Castellon'), array('ES', 'Ceuta', 'Ceuta'),
    array('ES', 'Ciudad Real', 'Ciudad Real'), array('ES', 'Cordoba', 'Cordoba'), array('ES', 'Cuenca', 'Cuenca'),
    array('ES', 'Girona', 'Girona'), array('ES', 'Granada', 'Granada'), array('ES', 'Guadalajara', 'Guadalajara'),
    array('ES', 'Guipuzcoa', 'Guipuzcoa'), array('ES', 'Huelva', 'Huelva'), array('ES', 'Huesca', 'Huesca'),
    array('ES', 'Jaen', 'Jaen'), array('ES', 'La Rioja', 'La Rioja'), array('ES', 'Las Palmas', 'Las Palmas'),
    array('ES', 'Leon', 'Leon'), array('ES', 'Lleida', 'Lleida'), array('ES', 'Lugo', 'Lugo'),
    array('ES', 'Madrid', 'Madrid'), array('ES', 'Malaga', 'Malaga'), array('ES', 'Melilla', 'Melilla'),
    array('ES', 'Murcia', 'Murcia'), array('ES', 'Navarra', 'Navarra'), array('ES', 'Ourense', 'Ourense'),
    array('ES', 'Palencia', 'Palencia'), array('ES', 'Pontevedra', 'Pontevedra'), array('ES', 'Salamanca', 'Salamanca'),
    array('ES', 'Santa Cruz de Tenerife', 'Santa Cruz de Tenerife'), array('ES', 'Segovia', 'Segovia'),
    array('ES', 'Sevilla', 'Sevilla'), array('ES', 'Soria', 'Soria'), array('ES', 'Tarragona', 'Tarragona'),
    array('ES', 'Teruel', 'Teruel'), array('ES', 'Toledo', 'Toledo'), array('ES', 'Valencia', 'Valencia'),
    array('ES', 'Valladolid', 'Valladolid'), array('ES', 'Vizcaya', 'Vizcaya'), array('ES', 'Zamora', 'Zamora'),
    array('ES', 'Zaragoza', 'Zaragoza'), array('FR', 1, 'Ain'), array('FR', 2, 'Aisne'), array('FR', 3, 'Allier'),
    array('FR', 4, 'Alpes-de-Haute-Provence'), array('FR', 5, 'Hautes-Alpes'), array('FR', 6, 'Alpes-Maritimes'),
    array('FR', 7, 'Ardèche'), array('FR', 8, 'Ardennes'), array('FR', 9, 'Ariège'), array('FR', 10, 'Aube'),
    array('FR', 11, 'Aude'), array('FR', 12, 'Aveyron'), array('FR', 13, 'Bouches-du-Rhône'),
    array('FR', 14, 'Calvados'), array('FR', 15, 'Cantal'), array('FR', 16, 'Charente'),
    array('FR', 17, 'Charente-Maritime'), array('FR', 18, 'Cher'), array('FR', 19, 'Corrèze'),
    array('FR', '2A', 'Corse-du-Sud'), array('FR', '2B', 'Haute-Corse'), array('FR', 21, 'Côte-d\'Or'),
    array('FR', 22, 'Côtes-d\'Armor'), array('FR', 23, 'Creuse'), array('FR', 24, 'Dordogne'), array('FR', 25, 'Doubs'),
    array('FR', 26, 'Drôme'), array('FR', 27, 'Eure'), array('FR', 28, 'Eure-et-Loir'), array('FR', 29, 'Finistère'),
    array('FR', 30, 'Gard'), array('FR', 31, 'Haute-Garonne'), array('FR', 32, 'Gers'), array('FR', 33, 'Gironde'),
    array('FR', 34, 'Hérault'), array('FR', 35, 'Ille-et-Vilaine'), array('FR', 36, 'Indre'),
    array('FR', 37, 'Indre-et-Loire'), array('FR', 38, 'Isère'), array('FR', 39, 'Jura'), array('FR', 40, 'Landes'),
    array('FR', 41, 'Loir-et-Cher'), array('FR', 42, 'Loire'), array('FR', 43, 'Haute-Loire'),
    array('FR', 44, 'Loire-Atlantique'), array('FR', 45, 'Loiret'), array('FR', 46, 'Lot'),
    array('FR', 47, 'Lot-et-Garonne'), array('FR', 48, 'Lozère'), array('FR', 49, 'Maine-et-Loire'),
    array('FR', 50, 'Manche'), array('FR', 51, 'Marne'), array('FR', 52, 'Haute-Marne'), array('FR', 53, 'Mayenne'),
    array('FR', 54, 'Meurthe-et-Moselle'), array('FR', 55, 'Meuse'), array('FR', 56, 'Morbihan'),
    array('FR', 57, 'Moselle'), array('FR', 58, 'Nièvre'), array('FR', 59, 'Nord'), array('FR', 60, 'Oise'),
    array('FR', 61, 'Orne'), array('FR', 62, 'Pas-de-Calais'), array('FR', 63, 'Puy-de-Dôme'),
    array('FR', 64, 'Pyrénées-Atlantiques'), array('FR', 65, 'Hautes-Pyrénées'), array('FR', 66, 'Pyrénées-Orientales'),
    array('FR', 67, 'Bas-Rhin'), array('FR', 68, 'Haut-Rhin'), array('FR', 69, 'Rhône'), array('FR', 70, 'Haute-Saône'),
    array('FR', 71, 'Saône-et-Loire'), array('FR', 72, 'Sarthe'), array('FR', 73, 'Savoie'),
    array('FR', 74, 'Haute-Savoie'), array('FR', 75, 'Paris'), array('FR', 76, 'Seine-Maritime'),
    array('FR', 77, 'Seine-et-Marne'), array('FR', 78, 'Yvelines'), array('FR', 79, 'Deux-Sèvres'),
    array('FR', 80, 'Somme'), array('FR', 81, 'Tarn'), array('FR', 82, 'Tarn-et-Garonne'), array('FR', 83, 'Var'),
    array('FR', 84, 'Vaucluse'), array('FR', 85, 'Vendée'), array('FR', 86, 'Vienne'), array('FR', 87, 'Haute-Vienne'),
    array('FR', 88, 'Vosges'), array('FR', 89, 'Yonne'), array('FR', 90, 'Territoire-de-Belfort'),
    array('FR', 91, 'Essonne'), array('FR', 92, 'Hauts-de-Seine'), array('FR', 93, 'Seine-Saint-Denis'),
    array('FR', 94, 'Val-de-Marne'), array('FR', 95, 'Val-d\'Oise'), array('RO', 'AB', 'Alba'),
    array('RO', 'AR', 'Arad'), array('RO', 'AG', 'Argeş'), array('RO', 'BC', 'Bacău'), array('RO', 'BH', 'Bihor'),
    array('RO', 'BN', 'Bistriţa-Năsăud'), array('RO', 'BT', 'Botoşani'), array('RO', 'BV', 'Braşov'),
    array('RO', 'BR', 'Brăila'), array('RO', 'B', 'Bucureşti'), array('RO', 'BZ', 'Buzău'),
    array('RO', 'CS', 'Caraş-Severin'), array('RO', 'CL', 'Călăraşi'), array('RO', 'CJ', 'Cluj'),
    array('RO', 'CT', 'Constanţa'), array('RO', 'CV', 'Covasna'), array('RO', 'DB', 'Dâmboviţa'),
    array('RO', 'DJ', 'Dolj'), array('RO', 'GL', 'Galaţi'), array('RO', 'GR', 'Giurgiu'), array('RO', 'GJ', 'Gorj'),
    array('RO', 'HR', 'Harghita'), array('RO', 'HD', 'Hunedoara'), array('RO', 'IL', 'Ialomiţa'),
    array('RO', 'IS', 'Iaşi'), array('RO', 'IF', 'Ilfov'), array('RO', 'MM', 'Maramureş'),
    array('RO', 'MH', 'Mehedinţi'), array('RO', 'MS', 'Mureş'), array('RO', 'NT', 'Neamţ'), array('RO', 'OT', 'Olt'),
    array('RO', 'PH', 'Prahova'), array('RO', 'SM', 'Satu-Mare'), array('RO', 'SJ', 'Sălaj'),
    array('RO', 'SB', 'Sibiu'), array('RO', 'SV', 'Suceava'), array('RO', 'TR', 'Teleorman'),
    array('RO', 'TM', 'Timiş'), array('RO', 'TL', 'Tulcea'), array('RO', 'VS', 'Vaslui'),
    array('RO', 'VL', 'Vâlcea'), array('RO', 'VN', 'Vrancea'), array('FI', 'Lappi', 'Lappi'),
    array('FI', 'Pohjois-Pohjanmaa', 'Pohjois-Pohjanmaa'), array('FI', 'Kainuu', 'Kainuu'),
    array('FI', 'Pohjois-Karjala', 'Pohjois-Karjala'), array('FI', 'Pohjois-Savo', 'Pohjois-Savo'),
    array('FI', 'Etelä-Savo', 'Etelä-Savo'), array('FI', 'Etelä-Pohjanmaa', 'Etelä-Pohjanmaa'),
    array('FI', 'Pohjanmaa', 'Pohjanmaa'), array('FI', 'Pirkanmaa', 'Pirkanmaa'), array('FI', 'Satakunta', 'Satakunta'),
    array('FI', 'Keski-Pohjanmaa', 'Keski-Pohjanmaa'), array('FI', 'Keski-Suomi', 'Keski-Suomi'),
    array('FI', 'Varsinais-Suomi', 'Varsinais-Suomi'), array('FI', 'Etelä-Karjala', 'Etelä-Karjala'),
    array('FI', 'Päijät-Häme', 'Päijät-Häme'), array('FI', 'Kanta-Häme', 'Kanta-Häme'),
    array('FI', 'Uusimaa', 'Uusimaa'), array('FI', 'Itä-Uusimaa', 'Itä-Uusimaa'),
    array('FI', 'Kymenlaakso', 'Kymenlaakso'), array('FI', 'Ahvenanmaa', 'Ahvenanmaa'),
    array('EE', 'EE-37', 'Harjumaa'), array('EE', 'EE-39', 'Hiiumaa'), array('EE', 'EE-44', 'Ida-Virumaa'),
    array('EE', 'EE-49', 'Jõgevamaa'), array('EE', 'EE-51', 'Järvamaa'), array('EE', 'EE-57', 'Läänemaa'),
    array('EE', 'EE-59', 'Lääne-Virumaa'), array('EE', 'EE-65', 'Põlvamaa'), array('EE', 'EE-67', 'Pärnumaa'),
    array('EE', 'EE-70', 'Raplamaa'), array('EE', 'EE-74', 'Saaremaa'), array('EE', 'EE-78', 'Tartumaa'),
    array('EE', 'EE-82', 'Valgamaa'), array('EE', 'EE-84', 'Viljandimaa'), array('EE', 'EE-86', 'Võrumaa'),
    array('LV', 'LV-DGV', 'Daugavpils'), array('LV', 'LV-JEL', 'Jelgava'), array('LV', 'Jēkabpils', 'Jēkabpils'),
    array('LV', 'LV-JUR', 'Jūrmala'), array('LV', 'LV-LPX', 'Liepāja'), array('LV', 'LV-LE', 'Liepājas novads'),
    array('LV', 'LV-REZ', 'Rēzekne'), array('LV', 'LV-RIX', 'Rīga'), array('LV', 'LV-RI', 'Rīgas novads'),
    array('LV', 'Valmiera', 'Valmiera'), array('LV', 'LV-VEN', 'Ventspils'),
    array('LV', 'Aglonas novads', 'Aglonas novads'), array('LV', 'LV-AI', 'Aizkraukles novads'),
    array('LV', 'Aizputes novads', 'Aizputes novads'), array('LV', 'Aknīstes novads', 'Aknīstes novads'),
    array('LV', 'Alojas novads', 'Alojas novads'), array('LV', 'Alsungas novads', 'Alsungas novads'),
    array('LV', 'LV-AL', 'Alūksnes novads'), array('LV', 'Amatas novads', 'Amatas novads'),
    array('LV', 'Apes novads', 'Apes novads'), array('LV', 'Auces novads', 'Auces novads'),
    array('LV', 'Babītes novads', 'Babītes novads'), array('LV', 'Baldones novads', 'Baldones novads'),
    array('LV', 'Baltinavas novads', 'Baltinavas novads'), array('LV', 'LV-BL', 'Balvu novads'),
    array('LV', 'LV-BU', 'Bauskas novads'), array('LV', 'Beverīnas novads', 'Beverīnas novads'),
    array('LV', 'Brocēnu novads', 'Brocēnu novads'), array('LV', 'Burtnieku novads', 'Burtnieku novads'),
    array('LV', 'Carnikavas novads', 'Carnikavas novads'), array('LV', 'Cesvaines novads', 'Cesvaines novads'),
    array('LV', 'Ciblas novads', 'Ciblas novads'), array('LV', 'LV-CE', 'Cēsu novads'),
    array('LV', 'Dagdas novads', 'Dagdas novads'), array('LV', 'LV-DA', 'Daugavpils novads'),
    array('LV', 'LV-DO', 'Dobeles novads'), array('LV', 'Dundagas novads', 'Dundagas novads'),
    array('LV', 'Durbes novads', 'Durbes novads'), array('LV', 'Engures novads', 'Engures novads'),
    array('LV', 'Garkalnes novads', 'Garkalnes novads'), array('LV', 'Grobiņas novads', 'Grobiņas novads'),
    array('LV', 'LV-GU', 'Gulbenes novads'), array('LV', 'Iecavas novads', 'Iecavas novads'),
    array('LV', 'Ikšķiles novads', 'Ikšķiles novads'), array('LV', 'Ilūkstes novads', 'Ilūkstes novads'),
    array('LV', 'Inčukalna novads', 'Inčukalna novads'), array('LV', 'Jaunjelgavas novads', 'Jaunjelgavas novads'),
    array('LV', 'Jaunpiebalgas novads', 'Jaunpiebalgas novads'), array('LV', 'Jaunpils novads', 'Jaunpils novads'),
    array('LV', 'LV-JL', 'Jelgavas novads'), array('LV', 'LV-JK', 'Jēkabpils novads'),
    array('LV', 'Kandavas novads', 'Kandavas novads'), array('LV', 'Kokneses novads', 'Kokneses novads'),
    array('LV', 'Krimuldas novads', 'Krimuldas novads'), array('LV', 'Krustpils novads', 'Krustpils novads'),
    array('LV', 'LV-KR', 'Krāslavas novads'), array('LV', 'LV-KU', 'Kuldīgas novads'),
    array('LV', 'Kārsavas novads', 'Kārsavas novads'), array('LV', 'Lielvārdes novads', 'Lielvārdes novads'),
    array('LV', 'LV-LM', 'Limbažu novads'), array('LV', 'Lubānas novads', 'Lubānas novads'),
    array('LV', 'LV-LU', 'Ludzas novads'), array('LV', 'Līgatnes novads', 'Līgatnes novads'),
    array('LV', 'Līvānu novads', 'Līvānu novads'), array('LV', 'LV-MA', 'Madonas novads'),
    array('LV', 'Mazsalacas novads', 'Mazsalacas novads'), array('LV', 'Mālpils novads', 'Mālpils novads'),
    array('LV', 'Mārupes novads', 'Mārupes novads'), array('LV', 'Naukšēnu novads', 'Naukšēnu novads'),
    array('LV', 'Neretas novads', 'Neretas novads'), array('LV', 'Nīcas novads', 'Nīcas novads'),
    array('LV', 'LV-OG', 'Ogres novads'), array('LV', 'Olaines novads', 'Olaines novads'),
    array('LV', 'Ozolnieku novads', 'Ozolnieku novads'), array('LV', 'LV-PR', 'Preiļu novads'),
    array('LV', 'Priekules novads', 'Priekules novads'), array('LV', 'Priekuļu novads', 'Priekuļu novads'),
    array('LV', 'Pārgaujas novads', 'Pārgaujas novads'), array('LV', 'Pāvilostas novads', 'Pāvilostas novads'),
    array('LV', 'Pļaviņu novads', 'Pļaviņu novads'), array('LV', 'Raunas novads', 'Raunas novads'),
    array('LV', 'Riebiņu novads', 'Riebiņu novads'), array('LV', 'Rojas novads', 'Rojas novads'),
    array('LV', 'Ropažu novads', 'Ropažu novads'), array('LV', 'Rucavas novads', 'Rucavas novads'),
    array('LV', 'Rugāju novads', 'Rugāju novads'), array('LV', 'Rundāles novads', 'Rundāles novads'),
    array('LV', 'LV-RE', 'Rēzeknes novads'), array('LV', 'Rūjienas novads', 'Rūjienas novads'),
    array('LV', 'Salacgrīvas novads', 'Salacgrīvas novads'), array('LV', 'Salas novads', 'Salas novads'),
    array('LV', 'Salaspils novads', 'Salaspils novads'), array('LV', 'LV-SA', 'Saldus novads'),
    array('LV', 'Saulkrastu novads', 'Saulkrastu novads'), array('LV', 'Siguldas novads', 'Siguldas novads'),
    array('LV', 'Skrundas novads', 'Skrundas novads'), array('LV', 'Skrīveru novads', 'Skrīveru novads'),
    array('LV', 'Smiltenes novads', 'Smiltenes novads'), array('LV', 'Stopiņu novads', 'Stopiņu novads'),
    array('LV', 'Strenču novads', 'Strenču novads'), array('LV', 'Sējas novads', 'Sējas novads'),
    array('LV', 'LV-TA', 'Talsu novads'), array('LV', 'LV-TU', 'Tukuma novads'),
    array('LV', 'Tērvetes novads', 'Tērvetes novads'), array('LV', 'Vaiņodes novads', 'Vaiņodes novads'),
    array('LV', 'LV-VK', 'Valkas novads'), array('LV', 'LV-VM', 'Valmieras novads'),
    array('LV', 'Varakļānu novads', 'Varakļānu novads'), array('LV', 'Vecpiebalgas novads', 'Vecpiebalgas novads'),
    array('LV', 'Vecumnieku novads', 'Vecumnieku novads'), array('LV', 'LV-VE', 'Ventspils novads'),
    array('LV', 'Viesītes novads', 'Viesītes novads'), array('LV', 'Viļakas novads', 'Viļakas novads'),
    array('LV', 'Viļānu novads', 'Viļānu novads'), array('LV', 'Vārkavas novads', 'Vārkavas novads'),
    array('LV', 'Zilupes novads', 'Zilupes novads'), array('LV', 'Ādažu novads', 'Ādažu novads'),
    array('LV', 'Ērgļu novads', 'Ērgļu novads'), array('LV', 'Ķeguma novads', 'Ķeguma novads'),
    array('LV', 'Ķekavas novads', 'Ķekavas novads'), array('LT', 'LT-AL', 'Alytaus Apskritis'),
    array('LT', 'LT-KU', 'Kauno Apskritis'), array('LT', 'LT-KL', 'Klaipėdos Apskritis'),
    array('LT', 'LT-MR', 'Marijampolės Apskritis'), array('LT', 'LT-PN', 'Panevėžio Apskritis'),
    array('LT', 'LT-SA', 'Šiaulių Apskritis'), array('LT', 'LT-TA', 'Tauragės Apskritis'),
    array('LT', 'LT-TE', 'Telšių Apskritis'), array('LT', 'LT-UT', 'Utenos Apskritis'),
    array('LT', 'LT-VL', 'Vilniaus Apskritis')
);

foreach ($data as $row) {
    $bind = array(
        'country_id'    => $row[0],
        'code'          => $row[1],
        'default_name'  => $row[2],
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region'), $bind);
    $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory/country_region'));

    $bind = array(
        'locale'    => 'en_US',
        'region_id' => $regionId,
        'name'      => $row[2]
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);
}

/**
 * Fill table directory/currency_rate
 */
$data = array(
    array('EUR', 'EUR', 1),
    array('EUR', 'USD', 1.415000000000),
    array('USD', 'EUR', 0.706700000000),
    array('USD', 'USD', 1),
);

$columns = array('currency_from', 'currency_to', 'rate');
$installer->getConnection()->insertArray($installer->getTable('directory/currency_rate'), $columns, $data);
