<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/**
 * Fill table directory/country
 */
$data = [
    ['AD', 'AD', 'AND'], ['AE', 'AE', 'ARE'], ['AF', 'AF', 'AFG'], ['AG', 'AG', 'ATG'],
    ['AI', 'AI', 'AIA'], ['AL', 'AL', 'ALB'], ['AM', 'AM', 'ARM'], ['AN', 'AN', 'ANT'],
    ['AO', 'AO', 'AGO'], ['AQ', 'AQ', 'ATA'], ['AR', 'AR', 'ARG'], ['AS', 'AS', 'ASM'],
    ['AT', 'AT', 'AUT'], ['AU', 'AU', 'AUS'], ['AW', 'AW', 'ABW'], ['AX', 'AX', 'ALA'],
    ['AZ', 'AZ', 'AZE'], ['BA', 'BA', 'BIH'], ['BB', 'BB', 'BRB'], ['BD', 'BD', 'BGD'],
    ['BE', 'BE', 'BEL'], ['BF', 'BF', 'BFA'], ['BG', 'BG', 'BGR'], ['BH', 'BH', 'BHR'],
    ['BI', 'BI', 'BDI'], ['BJ', 'BJ', 'BEN'], ['BL', 'BL', 'BLM'], ['BM', 'BM', 'BMU'],
    ['BN', 'BN', 'BRN'], ['BO', 'BO', 'BOL'], ['BR', 'BR', 'BRA'], ['BS', 'BS', 'BHS'],
    ['BT', 'BT', 'BTN'], ['BV', 'BV', 'BVT'], ['BW', 'BW', 'BWA'], ['BY', 'BY', 'BLR'],
    ['BZ', 'BZ', 'BLZ'], ['CA', 'CA', 'CAN'], ['CC', 'CC', 'CCK'], ['CD', 'CD', 'COD'],
    ['CF', 'CF', 'CAF'], ['CG', 'CG', 'COG'], ['CH', 'CH', 'CHE'], ['CI', 'CI', 'CIV'],
    ['CK', 'CK', 'COK'], ['CL', 'CL', 'CHL'], ['CM', 'CM', 'CMR'], ['CN', 'CN', 'CHN'],
    ['CO', 'CO', 'COL'], ['CR', 'CR', 'CRI'], ['CU', 'CU', 'CUB'], ['CV', 'CV', 'CPV'],
    ['CX', 'CX', 'CXR'], ['CY', 'CY', 'CYP'], ['CZ', 'CZ', 'CZE'], ['DE', 'DE', 'DEU'],
    ['DJ', 'DJ', 'DJI'], ['DK', 'DK', 'DNK'], ['DM', 'DM', 'DMA'], ['DO', 'DO', 'DOM'],
    ['DZ', 'DZ', 'DZA'], ['EC', 'EC', 'ECU'], ['EE', 'EE', 'EST'], ['EG', 'EG', 'EGY'],
    ['EH', 'EH', 'ESH'], ['ER', 'ER', 'ERI'], ['ES', 'ES', 'ESP'], ['ET', 'ET', 'ETH'],
    ['FI', 'FI', 'FIN'], ['FJ', 'FJ', 'FJI'], ['FK', 'FK', 'FLK'], ['FM', 'FM', 'FSM'],
    ['FO', 'FO', 'FRO'], ['FR', 'FR', 'FRA'], ['GA', 'GA', 'GAB'], ['GB', 'GB', 'GBR'],
    ['GD', 'GD', 'GRD'], ['GE', 'GE', 'GEO'], ['GF', 'GF', 'GUF'], ['GG', 'GG', 'GGY'],
    ['GH', 'GH', 'GHA'], ['GI', 'GI', 'GIB'], ['GL', 'GL', 'GRL'], ['GM', 'GM', 'GMB'],
    ['GN', 'GN', 'GIN'], ['GP', 'GP', 'GLP'], ['GQ', 'GQ', 'GNQ'], ['GR', 'GR', 'GRC'],
    ['GS', 'GS', 'SGS'], ['GT', 'GT', 'GTM'], ['GU', 'GU', 'GUM'], ['GW', 'GW', 'GNB'],
    ['GY', 'GY', 'GUY'], ['HK', 'HK', 'HKG'], ['HM', 'HM', 'HMD'], ['HN', 'HN', 'HND'],
    ['HR', 'HR', 'HRV'], ['HT', 'HT', 'HTI'], ['HU', 'HU', 'HUN'], ['ID', 'ID', 'IDN'],
    ['IE', 'IE', 'IRL'], ['IL', 'IL', 'ISR'], ['IM', 'IM', 'IMN'], ['IN', 'IN', 'IND'],
    ['IO', 'IO', 'IOT'], ['IQ', 'IQ', 'IRQ'], ['IR', 'IR', 'IRN'], ['IS', 'IS', 'ISL'],
    ['IT', 'IT', 'ITA'], ['JE', 'JE', 'JEY'], ['JM', 'JM', 'JAM'], ['JO', 'JO', 'JOR'],
    ['JP', 'JP', 'JPN'], ['KE', 'KE', 'KEN'], ['KG', 'KG', 'KGZ'], ['KH', 'KH', 'KHM'],
    ['KI', 'KI', 'KIR'], ['KM', 'KM', 'COM'], ['KN', 'KN', 'KNA'], ['KP', 'KP', 'PRK'],
    ['KR', 'KR', 'KOR'], ['KW', 'KW', 'KWT'], ['KY', 'KY', 'CYM'], ['KZ', 'KZ', 'KAZ'],
    ['LA', 'LA', 'LAO'], ['LB', 'LB', 'LBN'], ['LC', 'LC', 'LCA'], ['LI', 'LI', 'LIE'],
    ['LK', 'LK', 'LKA'], ['LR', 'LR', 'LBR'], ['LS', 'LS', 'LSO'], ['LT', 'LT', 'LTU'],
    ['LU', 'LU', 'LUX'], ['LV', 'LV', 'LVA'], ['LY', 'LY', 'LBY'], ['MA', 'MA', 'MAR'],
    ['MC', 'MC', 'MCO'], ['MD', 'MD', 'MDA'], ['ME', 'ME', 'MNE'], ['MF', 'MF', 'MAF'],
    ['MG', 'MG', 'MDG'], ['MH', 'MH', 'MHL'], ['MK', 'MK', 'MKD'], ['ML', 'ML', 'MLI'],
    ['MM', 'MM', 'MMR'], ['MN', 'MN', 'MNG'], ['MO', 'MO', 'MAC'], ['MP', 'MP', 'MNP'],
    ['MQ', 'MQ', 'MTQ'], ['MR', 'MR', 'MRT'], ['MS', 'MS', 'MSR'], ['MT', 'MT', 'MLT'],
    ['MU', 'MU', 'MUS'], ['MV', 'MV', 'MDV'], ['MW', 'MW', 'MWI'], ['MX', 'MX', 'MEX'],
    ['MY', 'MY', 'MYS'], ['MZ', 'MZ', 'MOZ'], ['NA', 'NA', 'NAM'], ['NC', 'NC', 'NCL'],
    ['NE', 'NE', 'NER'], ['NF', 'NF', 'NFK'], ['NG', 'NG', 'NGA'], ['NI', 'NI', 'NIC'],
    ['NL', 'NL', 'NLD'], ['NO', 'NO', 'NOR'], ['NP', 'NP', 'NPL'], ['NR', 'NR', 'NRU'],
    ['NU', 'NU', 'NIU'], ['NZ', 'NZ', 'NZL'], ['OM', 'OM', 'OMN'], ['PA', 'PA', 'PAN'],
    ['PE', 'PE', 'PER'], ['PF', 'PF', 'PYF'], ['PG', 'PG', 'PNG'], ['PH', 'PH', 'PHL'],
    ['PK', 'PK', 'PAK'], ['PL', 'PL', 'POL'], ['PM', 'PM', 'SPM'], ['PN', 'PN', 'PCN'],
    ['PR', 'PR', 'PRI'], ['PS', 'PS', 'PSE'], ['PT', 'PT', 'PRT'], ['PW', 'PW', 'PLW'],
    ['PY', 'PY', 'PRY'], ['QA', 'QA', 'QAT'], ['RE', 'RE', 'REU'], ['RO', 'RO', 'ROU'],
    ['RS', 'RS', 'SRB'], ['RU', 'RU', 'RUS'], ['RW', 'RW', 'RWA'], ['SA', 'SA', 'SAU'],
    ['SB', 'SB', 'SLB'], ['SC', 'SC', 'SYC'], ['SD', 'SD', 'SDN'], ['SE', 'SE', 'SWE'],
    ['SG', 'SG', 'SGP'], ['SH', 'SH', 'SHN'], ['SI', 'SI', 'SVN'], ['SJ', 'SJ', 'SJM'],
    ['SK', 'SK', 'SVK'], ['SL', 'SL', 'SLE'], ['SM', 'SM', 'SMR'], ['SN', 'SN', 'SEN'],
    ['SO', 'SO', 'SOM'], ['SR', 'SR', 'SUR'], ['ST', 'ST', 'STP'], ['SV', 'SV', 'SLV'],
    ['SY', 'SY', 'SYR'], ['SZ', 'SZ', 'SWZ'], ['TC', 'TC', 'TCA'], ['TD', 'TD', 'TCD'],
    ['TF', 'TF', 'ATF'], ['TG', 'TG', 'TGO'], ['TH', 'TH', 'THA'], ['TJ', 'TJ', 'TJK'],
    ['TK', 'TK', 'TKL'], ['TL', 'TL', 'TLS'], ['TM', 'TM', 'TKM'], ['TN', 'TN', 'TUN'],
    ['TO', 'TO', 'TON'], ['TR', 'TR', 'TUR'], ['TT', 'TT', 'TTO'], ['TV', 'TV', 'TUV'],
    ['TW', 'TW', 'TWN'], ['TZ', 'TZ', 'TZA'], ['UA', 'UA', 'UKR'], ['UG', 'UG', 'UGA'],
    ['UM', 'UM', 'UMI'], ['US', 'US', 'USA'], ['UY', 'UY', 'URY'], ['UZ', 'UZ', 'UZB'],
    ['VA', 'VA', 'VAT'], ['VC', 'VC', 'VCT'], ['VE', 'VE', 'VEN'], ['VG', 'VG', 'VGB'],
    ['VI', 'VI', 'VIR'], ['VN', 'VN', 'VNM'], ['VU', 'VU', 'VUT'], ['WF', 'WF', 'WLF'],
    ['WS', 'WS', 'WSM'], ['YE', 'YE', 'YEM'], ['YT', 'YT', 'MYT'], ['ZA', 'ZA', 'ZAF'],
    ['ZM', 'ZM', 'ZMB'], ['ZW', 'ZW', 'ZWE']
];

$columns = ['country_id', 'iso2_code', 'iso3_code'];
$installer->getConnection()->insertArray($installer->getTable('directory/country'), $columns, $data);

/**
 * Fill table directory/country_region
 * Fill table directory/country_region_name for en_US locale
 */
$data = [
    ['US', 'AL', 'Alabama'], ['US', 'AK', 'Alaska'], ['US', 'AS', 'American Samoa'],
    ['US', 'AZ', 'Arizona'], ['US', 'AR', 'Arkansas'], ['US', 'AF', 'Armed Forces Africa'],
    ['US', 'AA', 'Armed Forces Americas'], ['US', 'AC', 'Armed Forces Canada'],
    ['US', 'AE', 'Armed Forces Europe'], ['US', 'AM', 'Armed Forces Middle East'],
    ['US', 'AP', 'Armed Forces Pacific'], ['US', 'CA', 'California'], ['US', 'CO', 'Colorado'],
    ['US', 'CT', 'Connecticut'], ['US', 'DE', 'Delaware'], ['US', 'DC', 'District of Columbia'],
    ['US', 'FM', 'Federated States Of Micronesia'], ['US', 'FL', 'Florida'], ['US', 'GA', 'Georgia'],
    ['US', 'GU', 'Guam'], ['US', 'HI', 'Hawaii'], ['US', 'ID', 'Idaho'], ['US', 'IL', 'Illinois'],
    ['US', 'IN', 'Indiana'], ['US', 'IA', 'Iowa'], ['US', 'KS', 'Kansas'], ['US', 'KY', 'Kentucky'],
    ['US', 'LA', 'Louisiana'], ['US', 'ME', 'Maine'], ['US', 'MH', 'Marshall Islands'],
    ['US', 'MD', 'Maryland'], ['US', 'MA', 'Massachusetts'], ['US', 'MI', 'Michigan'],
    ['US', 'MN', 'Minnesota'], ['US', 'MS', 'Mississippi'], ['US', 'MO', 'Missouri'],
    ['US', 'MT', 'Montana'], ['US', 'NE', 'Nebraska'], ['US', 'NV', 'Nevada'],
    ['US', 'NH', 'New Hampshire'], ['US', 'NJ', 'New Jersey'], ['US', 'NM', 'New Mexico'],
    ['US', 'NY', 'New York'], ['US', 'NC', 'North Carolina'], ['US', 'ND', 'North Dakota'],
    ['US', 'MP', 'Northern Mariana Islands'], ['US', 'OH', 'Ohio'], ['US', 'OK', 'Oklahoma'],
    ['US', 'OR', 'Oregon'], ['US', 'PW', 'Palau'], ['US', 'PA', 'Pennsylvania'],
    ['US', 'PR', 'Puerto Rico'], ['US', 'RI', 'Rhode Island'], ['US', 'SC', 'South Carolina'],
    ['US', 'SD', 'South Dakota'], ['US', 'TN', 'Tennessee'], ['US', 'TX', 'Texas'],
    ['US', 'UT', 'Utah'], ['US', 'VT', 'Vermont'], ['US', 'VI', 'Virgin Islands'],
    ['US', 'VA', 'Virginia'], ['US', 'WA', 'Washington'], ['US', 'WV', 'West Virginia'],
    ['US', 'WI', 'Wisconsin'], ['US', 'WY', 'Wyoming'], ['CA', 'AB', 'Alberta'],
    ['CA', 'BC', 'British Columbia'], ['CA', 'MB', 'Manitoba'],
    ['CA', 'NL', 'Newfoundland and Labrador'], ['CA', 'NB', 'New Brunswick'],
    ['CA', 'NS', 'Nova Scotia'], ['CA', 'NT', 'Northwest Territories'], ['CA', 'NU', 'Nunavut'],
    ['CA', 'ON', 'Ontario'], ['CA', 'PE', 'Prince Edward Island'], ['CA', 'QC', 'Quebec'],
    ['CA', 'SK', 'Saskatchewan'], ['CA', 'YT', 'Yukon Territory'], ['DE', 'NDS', 'Niedersachsen'],
    ['DE', 'BAW', 'Baden-Württemberg'], ['DE', 'BAY', 'Bayern'], ['DE', 'BER', 'Berlin'],
    ['DE', 'BRG', 'Brandenburg'], ['DE', 'BRE', 'Bremen'], ['DE', 'HAM', 'Hamburg'],
    ['DE', 'HES', 'Hessen'], ['DE', 'MEC', 'Mecklenburg-Vorpommern'],
    ['DE', 'NRW', 'Nordrhein-Westfalen'], ['DE', 'RHE', 'Rheinland-Pfalz'], ['DE', 'SAR', 'Saarland'],
    ['DE', 'SAS', 'Sachsen'], ['DE', 'SAC', 'Sachsen-Anhalt'], ['DE', 'SCN', 'Schleswig-Holstein'],
    ['DE', 'THE', 'Thüringen'], ['AT', 'WI', 'Wien'], ['AT', 'NO', 'Niederösterreich'],
    ['AT', 'OO', 'Oberösterreich'], ['AT', 'SB', 'Salzburg'], ['AT', 'KN', 'Kärnten'],
    ['AT', 'ST', 'Steiermark'], ['AT', 'TI', 'Tirol'], ['AT', 'BL', 'Burgenland'],
    ['AT', 'VB', 'Voralberg'], ['CH', 'AG', 'Aargau'], ['CH', 'AI', 'Appenzell Innerrhoden'],
    ['CH', 'AR', 'Appenzell Ausserrhoden'], ['CH', 'BE', 'Bern'], ['CH', 'BL', 'Basel-Landschaft'],
    ['CH', 'BS', 'Basel-Stadt'], ['CH', 'FR', 'Freiburg'], ['CH', 'GE', 'Genf'],
    ['CH', 'GL', 'Glarus'], ['CH', 'GR', 'Graubünden'], ['CH', 'JU', 'Jura'],
    ['CH', 'LU', 'Luzern'], ['CH', 'NE', 'Neuenburg'], ['CH', 'NW', 'Nidwalden'],
    ['CH', 'OW', 'Obwalden'], ['CH', 'SG', 'St. Gallen'], ['CH', 'SH', 'Schaffhausen'],
    ['CH', 'SO', 'Solothurn'], ['CH', 'SZ', 'Schwyz'], ['CH', 'TG', 'Thurgau'],
    ['CH', 'TI', 'Tessin'], ['CH', 'UR', 'Uri'], ['CH', 'VD', 'Waadt'], ['CH', 'VS', 'Wallis'],
    ['CH', 'ZG', 'Zug'], ['CH', 'ZH', 'Zürich'], ['ES', 'A Coruсa', 'A Coruña'],
    ['ES', 'Alava', 'Alava'], ['ES', 'Albacete', 'Albacete'], ['ES', 'Alicante', 'Alicante'],
    ['ES', 'Almeria', 'Almeria'], ['ES', 'Asturias', 'Asturias'], ['ES', 'Avila', 'Avila'],
    ['ES', 'Badajoz', 'Badajoz'], ['ES', 'Baleares', 'Baleares'], ['ES', 'Barcelona', 'Barcelona'],
    ['ES', 'Burgos', 'Burgos'], ['ES', 'Caceres', 'Caceres'], ['ES', 'Cadiz', 'Cadiz'],
    ['ES', 'Cantabria', 'Cantabria'], ['ES', 'Castellon', 'Castellon'], ['ES', 'Ceuta', 'Ceuta'],
    ['ES', 'Ciudad Real', 'Ciudad Real'], ['ES', 'Cordoba', 'Cordoba'], ['ES', 'Cuenca', 'Cuenca'],
    ['ES', 'Girona', 'Girona'], ['ES', 'Granada', 'Granada'], ['ES', 'Guadalajara', 'Guadalajara'],
    ['ES', 'Guipuzcoa', 'Guipuzcoa'], ['ES', 'Huelva', 'Huelva'], ['ES', 'Huesca', 'Huesca'],
    ['ES', 'Jaen', 'Jaen'], ['ES', 'La Rioja', 'La Rioja'], ['ES', 'Las Palmas', 'Las Palmas'],
    ['ES', 'Leon', 'Leon'], ['ES', 'Lleida', 'Lleida'], ['ES', 'Lugo', 'Lugo'],
    ['ES', 'Madrid', 'Madrid'], ['ES', 'Malaga', 'Malaga'], ['ES', 'Melilla', 'Melilla'],
    ['ES', 'Murcia', 'Murcia'], ['ES', 'Navarra', 'Navarra'], ['ES', 'Ourense', 'Ourense'],
    ['ES', 'Palencia', 'Palencia'], ['ES', 'Pontevedra', 'Pontevedra'], ['ES', 'Salamanca', 'Salamanca'],
    ['ES', 'Santa Cruz de Tenerife', 'Santa Cruz de Tenerife'], ['ES', 'Segovia', 'Segovia'],
    ['ES', 'Sevilla', 'Sevilla'], ['ES', 'Soria', 'Soria'], ['ES', 'Tarragona', 'Tarragona'],
    ['ES', 'Teruel', 'Teruel'], ['ES', 'Toledo', 'Toledo'], ['ES', 'Valencia', 'Valencia'],
    ['ES', 'Valladolid', 'Valladolid'], ['ES', 'Vizcaya', 'Vizcaya'], ['ES', 'Zamora', 'Zamora'],
    ['ES', 'Zaragoza', 'Zaragoza'], ['FR', 1, 'Ain'], ['FR', 2, 'Aisne'], ['FR', 3, 'Allier'],
    ['FR', 4, 'Alpes-de-Haute-Provence'], ['FR', 5, 'Hautes-Alpes'], ['FR', 6, 'Alpes-Maritimes'],
    ['FR', 7, 'Ardèche'], ['FR', 8, 'Ardennes'], ['FR', 9, 'Ariège'], ['FR', 10, 'Aube'],
    ['FR', 11, 'Aude'], ['FR', 12, 'Aveyron'], ['FR', 13, 'Bouches-du-Rhône'],
    ['FR', 14, 'Calvados'], ['FR', 15, 'Cantal'], ['FR', 16, 'Charente'],
    ['FR', 17, 'Charente-Maritime'], ['FR', 18, 'Cher'], ['FR', 19, 'Corrèze'],
    ['FR', '2A', 'Corse-du-Sud'], ['FR', '2B', 'Haute-Corse'], ['FR', 21, 'Côte-d\'Or'],
    ['FR', 22, 'Côtes-d\'Armor'], ['FR', 23, 'Creuse'], ['FR', 24, 'Dordogne'], ['FR', 25, 'Doubs'],
    ['FR', 26, 'Drôme'], ['FR', 27, 'Eure'], ['FR', 28, 'Eure-et-Loir'], ['FR', 29, 'Finistère'],
    ['FR', 30, 'Gard'], ['FR', 31, 'Haute-Garonne'], ['FR', 32, 'Gers'], ['FR', 33, 'Gironde'],
    ['FR', 34, 'Hérault'], ['FR', 35, 'Ille-et-Vilaine'], ['FR', 36, 'Indre'],
    ['FR', 37, 'Indre-et-Loire'], ['FR', 38, 'Isère'], ['FR', 39, 'Jura'], ['FR', 40, 'Landes'],
    ['FR', 41, 'Loir-et-Cher'], ['FR', 42, 'Loire'], ['FR', 43, 'Haute-Loire'],
    ['FR', 44, 'Loire-Atlantique'], ['FR', 45, 'Loiret'], ['FR', 46, 'Lot'],
    ['FR', 47, 'Lot-et-Garonne'], ['FR', 48, 'Lozère'], ['FR', 49, 'Maine-et-Loire'],
    ['FR', 50, 'Manche'], ['FR', 51, 'Marne'], ['FR', 52, 'Haute-Marne'], ['FR', 53, 'Mayenne'],
    ['FR', 54, 'Meurthe-et-Moselle'], ['FR', 55, 'Meuse'], ['FR', 56, 'Morbihan'],
    ['FR', 57, 'Moselle'], ['FR', 58, 'Nièvre'], ['FR', 59, 'Nord'], ['FR', 60, 'Oise'],
    ['FR', 61, 'Orne'], ['FR', 62, 'Pas-de-Calais'], ['FR', 63, 'Puy-de-Dôme'],
    ['FR', 64, 'Pyrénées-Atlantiques'], ['FR', 65, 'Hautes-Pyrénées'], ['FR', 66, 'Pyrénées-Orientales'],
    ['FR', 67, 'Bas-Rhin'], ['FR', 68, 'Haut-Rhin'], ['FR', 69, 'Rhône'], ['FR', 70, 'Haute-Saône'],
    ['FR', 71, 'Saône-et-Loire'], ['FR', 72, 'Sarthe'], ['FR', 73, 'Savoie'],
    ['FR', 74, 'Haute-Savoie'], ['FR', 75, 'Paris'], ['FR', 76, 'Seine-Maritime'],
    ['FR', 77, 'Seine-et-Marne'], ['FR', 78, 'Yvelines'], ['FR', 79, 'Deux-Sèvres'],
    ['FR', 80, 'Somme'], ['FR', 81, 'Tarn'], ['FR', 82, 'Tarn-et-Garonne'], ['FR', 83, 'Var'],
    ['FR', 84, 'Vaucluse'], ['FR', 85, 'Vendée'], ['FR', 86, 'Vienne'], ['FR', 87, 'Haute-Vienne'],
    ['FR', 88, 'Vosges'], ['FR', 89, 'Yonne'], ['FR', 90, 'Territoire-de-Belfort'],
    ['FR', 91, 'Essonne'], ['FR', 92, 'Hauts-de-Seine'], ['FR', 93, 'Seine-Saint-Denis'],
    ['FR', 94, 'Val-de-Marne'], ['FR', 95, 'Val-d\'Oise'], ['RO', 'AB', 'Alba'],
    ['RO', 'AR', 'Arad'], ['RO', 'AG', 'Argeş'], ['RO', 'BC', 'Bacău'], ['RO', 'BH', 'Bihor'],
    ['RO', 'BN', 'Bistriţa-Năsăud'], ['RO', 'BT', 'Botoşani'], ['RO', 'BV', 'Braşov'],
    ['RO', 'BR', 'Brăila'], ['RO', 'B', 'Bucureşti'], ['RO', 'BZ', 'Buzău'],
    ['RO', 'CS', 'Caraş-Severin'], ['RO', 'CL', 'Călăraşi'], ['RO', 'CJ', 'Cluj'],
    ['RO', 'CT', 'Constanţa'], ['RO', 'CV', 'Covasna'], ['RO', 'DB', 'Dâmboviţa'],
    ['RO', 'DJ', 'Dolj'], ['RO', 'GL', 'Galaţi'], ['RO', 'GR', 'Giurgiu'], ['RO', 'GJ', 'Gorj'],
    ['RO', 'HR', 'Harghita'], ['RO', 'HD', 'Hunedoara'], ['RO', 'IL', 'Ialomiţa'],
    ['RO', 'IS', 'Iaşi'], ['RO', 'IF', 'Ilfov'], ['RO', 'MM', 'Maramureş'],
    ['RO', 'MH', 'Mehedinţi'], ['RO', 'MS', 'Mureş'], ['RO', 'NT', 'Neamţ'], ['RO', 'OT', 'Olt'],
    ['RO', 'PH', 'Prahova'], ['RO', 'SM', 'Satu-Mare'], ['RO', 'SJ', 'Sălaj'],
    ['RO', 'SB', 'Sibiu'], ['RO', 'SV', 'Suceava'], ['RO', 'TR', 'Teleorman'],
    ['RO', 'TM', 'Timiş'], ['RO', 'TL', 'Tulcea'], ['RO', 'VS', 'Vaslui'],
    ['RO', 'VL', 'Vâlcea'], ['RO', 'VN', 'Vrancea'], ['FI', 'Lappi', 'Lappi'],
    ['FI', 'Pohjois-Pohjanmaa', 'Pohjois-Pohjanmaa'], ['FI', 'Kainuu', 'Kainuu'],
    ['FI', 'Pohjois-Karjala', 'Pohjois-Karjala'], ['FI', 'Pohjois-Savo', 'Pohjois-Savo'],
    ['FI', 'Etelä-Savo', 'Etelä-Savo'], ['FI', 'Etelä-Pohjanmaa', 'Etelä-Pohjanmaa'],
    ['FI', 'Pohjanmaa', 'Pohjanmaa'], ['FI', 'Pirkanmaa', 'Pirkanmaa'], ['FI', 'Satakunta', 'Satakunta'],
    ['FI', 'Keski-Pohjanmaa', 'Keski-Pohjanmaa'], ['FI', 'Keski-Suomi', 'Keski-Suomi'],
    ['FI', 'Varsinais-Suomi', 'Varsinais-Suomi'], ['FI', 'Etelä-Karjala', 'Etelä-Karjala'],
    ['FI', 'Päijät-Häme', 'Päijät-Häme'], ['FI', 'Kanta-Häme', 'Kanta-Häme'],
    ['FI', 'Uusimaa', 'Uusimaa'], ['FI', 'Itä-Uusimaa', 'Itä-Uusimaa'],
    ['FI', 'Kymenlaakso', 'Kymenlaakso'], ['FI', 'Ahvenanmaa', 'Ahvenanmaa'],
    ['EE', 'EE-37', 'Harjumaa'], ['EE', 'EE-39', 'Hiiumaa'], ['EE', 'EE-44', 'Ida-Virumaa'],
    ['EE', 'EE-49', 'Jõgevamaa'], ['EE', 'EE-51', 'Järvamaa'], ['EE', 'EE-57', 'Läänemaa'],
    ['EE', 'EE-59', 'Lääne-Virumaa'], ['EE', 'EE-65', 'Põlvamaa'], ['EE', 'EE-67', 'Pärnumaa'],
    ['EE', 'EE-70', 'Raplamaa'], ['EE', 'EE-74', 'Saaremaa'], ['EE', 'EE-78', 'Tartumaa'],
    ['EE', 'EE-82', 'Valgamaa'], ['EE', 'EE-84', 'Viljandimaa'], ['EE', 'EE-86', 'Võrumaa'],
    ['LV', 'LV-DGV', 'Daugavpils'], ['LV', 'LV-JEL', 'Jelgava'], ['LV', 'Jēkabpils', 'Jēkabpils'],
    ['LV', 'LV-JUR', 'Jūrmala'], ['LV', 'LV-LPX', 'Liepāja'], ['LV', 'LV-LE', 'Liepājas novads'],
    ['LV', 'LV-REZ', 'Rēzekne'], ['LV', 'LV-RIX', 'Rīga'], ['LV', 'LV-RI', 'Rīgas novads'],
    ['LV', 'Valmiera', 'Valmiera'], ['LV', 'LV-VEN', 'Ventspils'],
    ['LV', 'Aglonas novads', 'Aglonas novads'], ['LV', 'LV-AI', 'Aizkraukles novads'],
    ['LV', 'Aizputes novads', 'Aizputes novads'], ['LV', 'Aknīstes novads', 'Aknīstes novads'],
    ['LV', 'Alojas novads', 'Alojas novads'], ['LV', 'Alsungas novads', 'Alsungas novads'],
    ['LV', 'LV-AL', 'Alūksnes novads'], ['LV', 'Amatas novads', 'Amatas novads'],
    ['LV', 'Apes novads', 'Apes novads'], ['LV', 'Auces novads', 'Auces novads'],
    ['LV', 'Babītes novads', 'Babītes novads'], ['LV', 'Baldones novads', 'Baldones novads'],
    ['LV', 'Baltinavas novads', 'Baltinavas novads'], ['LV', 'LV-BL', 'Balvu novads'],
    ['LV', 'LV-BU', 'Bauskas novads'], ['LV', 'Beverīnas novads', 'Beverīnas novads'],
    ['LV', 'Brocēnu novads', 'Brocēnu novads'], ['LV', 'Burtnieku novads', 'Burtnieku novads'],
    ['LV', 'Carnikavas novads', 'Carnikavas novads'], ['LV', 'Cesvaines novads', 'Cesvaines novads'],
    ['LV', 'Ciblas novads', 'Ciblas novads'], ['LV', 'LV-CE', 'Cēsu novads'],
    ['LV', 'Dagdas novads', 'Dagdas novads'], ['LV', 'LV-DA', 'Daugavpils novads'],
    ['LV', 'LV-DO', 'Dobeles novads'], ['LV', 'Dundagas novads', 'Dundagas novads'],
    ['LV', 'Durbes novads', 'Durbes novads'], ['LV', 'Engures novads', 'Engures novads'],
    ['LV', 'Garkalnes novads', 'Garkalnes novads'], ['LV', 'Grobiņas novads', 'Grobiņas novads'],
    ['LV', 'LV-GU', 'Gulbenes novads'], ['LV', 'Iecavas novads', 'Iecavas novads'],
    ['LV', 'Ikšķiles novads', 'Ikšķiles novads'], ['LV', 'Ilūkstes novads', 'Ilūkstes novads'],
    ['LV', 'Inčukalna novads', 'Inčukalna novads'], ['LV', 'Jaunjelgavas novads', 'Jaunjelgavas novads'],
    ['LV', 'Jaunpiebalgas novads', 'Jaunpiebalgas novads'], ['LV', 'Jaunpils novads', 'Jaunpils novads'],
    ['LV', 'LV-JL', 'Jelgavas novads'], ['LV', 'LV-JK', 'Jēkabpils novads'],
    ['LV', 'Kandavas novads', 'Kandavas novads'], ['LV', 'Kokneses novads', 'Kokneses novads'],
    ['LV', 'Krimuldas novads', 'Krimuldas novads'], ['LV', 'Krustpils novads', 'Krustpils novads'],
    ['LV', 'LV-KR', 'Krāslavas novads'], ['LV', 'LV-KU', 'Kuldīgas novads'],
    ['LV', 'Kārsavas novads', 'Kārsavas novads'], ['LV', 'Lielvārdes novads', 'Lielvārdes novads'],
    ['LV', 'LV-LM', 'Limbažu novads'], ['LV', 'Lubānas novads', 'Lubānas novads'],
    ['LV', 'LV-LU', 'Ludzas novads'], ['LV', 'Līgatnes novads', 'Līgatnes novads'],
    ['LV', 'Līvānu novads', 'Līvānu novads'], ['LV', 'LV-MA', 'Madonas novads'],
    ['LV', 'Mazsalacas novads', 'Mazsalacas novads'], ['LV', 'Mālpils novads', 'Mālpils novads'],
    ['LV', 'Mārupes novads', 'Mārupes novads'], ['LV', 'Naukšēnu novads', 'Naukšēnu novads'],
    ['LV', 'Neretas novads', 'Neretas novads'], ['LV', 'Nīcas novads', 'Nīcas novads'],
    ['LV', 'LV-OG', 'Ogres novads'], ['LV', 'Olaines novads', 'Olaines novads'],
    ['LV', 'Ozolnieku novads', 'Ozolnieku novads'], ['LV', 'LV-PR', 'Preiļu novads'],
    ['LV', 'Priekules novads', 'Priekules novads'], ['LV', 'Priekuļu novads', 'Priekuļu novads'],
    ['LV', 'Pārgaujas novads', 'Pārgaujas novads'], ['LV', 'Pāvilostas novads', 'Pāvilostas novads'],
    ['LV', 'Pļaviņu novads', 'Pļaviņu novads'], ['LV', 'Raunas novads', 'Raunas novads'],
    ['LV', 'Riebiņu novads', 'Riebiņu novads'], ['LV', 'Rojas novads', 'Rojas novads'],
    ['LV', 'Ropažu novads', 'Ropažu novads'], ['LV', 'Rucavas novads', 'Rucavas novads'],
    ['LV', 'Rugāju novads', 'Rugāju novads'], ['LV', 'Rundāles novads', 'Rundāles novads'],
    ['LV', 'LV-RE', 'Rēzeknes novads'], ['LV', 'Rūjienas novads', 'Rūjienas novads'],
    ['LV', 'Salacgrīvas novads', 'Salacgrīvas novads'], ['LV', 'Salas novads', 'Salas novads'],
    ['LV', 'Salaspils novads', 'Salaspils novads'], ['LV', 'LV-SA', 'Saldus novads'],
    ['LV', 'Saulkrastu novads', 'Saulkrastu novads'], ['LV', 'Siguldas novads', 'Siguldas novads'],
    ['LV', 'Skrundas novads', 'Skrundas novads'], ['LV', 'Skrīveru novads', 'Skrīveru novads'],
    ['LV', 'Smiltenes novads', 'Smiltenes novads'], ['LV', 'Stopiņu novads', 'Stopiņu novads'],
    ['LV', 'Strenču novads', 'Strenču novads'], ['LV', 'Sējas novads', 'Sējas novads'],
    ['LV', 'LV-TA', 'Talsu novads'], ['LV', 'LV-TU', 'Tukuma novads'],
    ['LV', 'Tērvetes novads', 'Tērvetes novads'], ['LV', 'Vaiņodes novads', 'Vaiņodes novads'],
    ['LV', 'LV-VK', 'Valkas novads'], ['LV', 'LV-VM', 'Valmieras novads'],
    ['LV', 'Varakļānu novads', 'Varakļānu novads'], ['LV', 'Vecpiebalgas novads', 'Vecpiebalgas novads'],
    ['LV', 'Vecumnieku novads', 'Vecumnieku novads'], ['LV', 'LV-VE', 'Ventspils novads'],
    ['LV', 'Viesītes novads', 'Viesītes novads'], ['LV', 'Viļakas novads', 'Viļakas novads'],
    ['LV', 'Viļānu novads', 'Viļānu novads'], ['LV', 'Vārkavas novads', 'Vārkavas novads'],
    ['LV', 'Zilupes novads', 'Zilupes novads'], ['LV', 'Ādažu novads', 'Ādažu novads'],
    ['LV', 'Ērgļu novads', 'Ērgļu novads'], ['LV', 'Ķeguma novads', 'Ķeguma novads'],
    ['LV', 'Ķekavas novads', 'Ķekavas novads'], ['LT', 'LT-AL', 'Alytaus Apskritis'],
    ['LT', 'LT-KU', 'Kauno Apskritis'], ['LT', 'LT-KL', 'Klaipėdos Apskritis'],
    ['LT', 'LT-MR', 'Marijampolės Apskritis'], ['LT', 'LT-PN', 'Panevėžio Apskritis'],
    ['LT', 'LT-SA', 'Šiaulių Apskritis'], ['LT', 'LT-TA', 'Tauragės Apskritis'],
    ['LT', 'LT-TE', 'Telšių Apskritis'], ['LT', 'LT-UT', 'Utenos Apskritis'],
    ['LT', 'LT-VL', 'Vilniaus Apskritis']
];

foreach ($data as $row) {
    $bind = [
        'country_id'    => $row[0],
        'code'          => $row[1],
        'default_name'  => $row[2],
    ];
    $installer->getConnection()->insert($installer->getTable('directory/country_region'), $bind);
    $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory/country_region'));

    $bind = [
        'locale'    => 'en_US',
        'region_id' => $regionId,
        'name'      => $row[2]
    ];
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);
}

/**
 * Fill table directory/currency_rate
 */
$data = [
    ['EUR', 'EUR', 1],
    ['EUR', 'USD', 1.415000000000],
    ['USD', 'EUR', 0.706700000000],
    ['USD', 'USD', 1],
];

$columns = ['currency_from', 'currency_to', 'rate'];
$installer->getConnection()->insertArray($installer->getTable('directory/currency_rate'), $columns, $data);
