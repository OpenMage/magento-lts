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

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection  = $installer->getConnection();

$regionTable = $installer->getTable('directory/country_region');

$regionsToIns = [
    //After reform of 2010 January
    ['FI', 'Lappi', 'Lappi'],
    ['FI', 'Pohjois-Pohjanmaa', 'Pohjois-Pohjanmaa'],
    ['FI', 'Kainuu', 'Kainuu'],
    ['FI', 'Pohjois-Karjala', 'Pohjois-Karjala'],
    ['FI', 'Pohjois-Savo', 'Pohjois-Savo'],
    ['FI', 'Etelä-Savo', 'Etelä-Savo'],
    ['FI', 'Etelä-Pohjanmaa', 'Etelä-Pohjanmaa'],
    ['FI', 'Pohjanmaa', 'Pohjanmaa'],
    ['FI', 'Pirkanmaa', 'Pirkanmaa'],
    ['FI', 'Satakunta', 'Satakunta'],
    ['FI', 'Keski-Pohjanmaa', 'Keski-Pohjanmaa'],
    ['FI', 'Keski-Suomi', 'Keski-Suomi'],
    ['FI', 'Varsinais-Suomi', 'Varsinais-Suomi'],
    ['FI', 'Etelä-Karjala', 'Etelä-Karjala'],
    ['FI', 'Päijät-Häme', 'Päijät-Häme'],
    ['FI', 'Kanta-Häme', 'Kanta-Häme'],
    ['FI', 'Uusimaa', 'Uusimaa'],
    ['FI', 'Itä-Uusimaa', 'Itä-Uusimaa'],
    ['FI', 'Kymenlaakso', 'Kymenlaakso'],
    ['FI', 'Ahvenanmaa', 'Ahvenanmaa'],

    //ISO-3166-2:EE
    ['EE', 'EE-37', 'Harjumaa'],
    ['EE', 'EE-39', 'Hiiumaa'],
    ['EE', 'EE-44', 'Ida-Virumaa'],
    ['EE', 'EE-49', 'Jõgevamaa'],
    ['EE', 'EE-51', 'Järvamaa'],
    ['EE', 'EE-57', 'Läänemaa'],
    ['EE', 'EE-59', 'Lääne-Virumaa'],
    ['EE', 'EE-65', 'Põlvamaa'],
    ['EE', 'EE-67', 'Pärnumaa'],
    ['EE', 'EE-70', 'Raplamaa'],
    ['EE', 'EE-74', 'Saaremaa'],
    ['EE', 'EE-78', 'Tartumaa'],
    ['EE', 'EE-82', 'Valgamaa'],
    ['EE', 'EE-84', 'Viljandimaa'],
    ['EE', 'EE-86', 'Võrumaa'],

    //After reform of 2009 July
    ['LV', 'LV-DGV', 'Daugavpils'],//now become good
    ['LV', 'LV-JEL', 'Jelgava'],
    ['LV', 'Jēkabpils', 'Jēkabpils'],
    ['LV', 'LV-JUR', 'Jūrmala'],
    ['LV', 'LV-LPX', 'Liepāja'],
    ['LV', 'LV-LE', 'Liepājas novads'],
    ['LV', 'LV-REZ', 'Rēzekne'],
    ['LV', 'LV-RIX', 'Rīga'],
    ['LV', 'LV-RI', 'Rīgas novads'],
    ['LV', 'Valmiera', 'Valmiera'],
    ['LV', 'LV-VEN', 'Ventspils'],
    ['LV', 'Aglonas novads', 'Aglonas novads'],
    ['LV', 'LV-AI', 'Aizkraukles novads'],
    ['LV', 'Aizputes novads', 'Aizputes novads'],
    ['LV', 'Aknīstes novads', 'Aknīstes novads'],
    ['LV', 'Alojas novads', 'Alojas novads'],
    ['LV', 'Alsungas novads', 'Alsungas novads'],
    ['LV', 'LV-AL', 'Alūksnes novads'],
    ['LV', 'Amatas novads', 'Amatas novads'],
    ['LV', 'Apes novads', 'Apes novads'],
    ['LV', 'Auces novads', 'Auces novads'],
    ['LV', 'Babītes novads', 'Babītes novads'],
    ['LV', 'Baldones novads', 'Baldones novads'],
    ['LV', 'Baltinavas novads', 'Baltinavas novads'],
    ['LV', 'LV-BL', 'Balvu novads'],
    ['LV', 'LV-BU', 'Bauskas novads'],
    ['LV', 'Beverīnas novads', 'Beverīnas novads'],
    ['LV', 'Brocēnu novads', 'Brocēnu novads'],
    ['LV', 'Burtnieku novads', 'Burtnieku novads'],
    ['LV', 'Carnikavas novads', 'Carnikavas novads'],
    ['LV', 'Cesvaines novads', 'Cesvaines novads'],
    ['LV', 'Ciblas novads', 'Ciblas novads'],
    ['LV', 'LV-CE', 'Cēsu novads'],
    ['LV', 'Dagdas novads', 'Dagdas novads'],
    ['LV', 'LV-DA', 'Daugavpils novads'],
    ['LV', 'LV-DO', 'Dobeles novads'],
    ['LV', 'Dundagas novads', 'Dundagas novads'],
    ['LV', 'Durbes novads', 'Durbes novads'],
    ['LV', 'Engures novads', 'Engures novads'],
    ['LV', 'Garkalnes novads', 'Garkalnes novads'],
    ['LV', 'Grobiņas novads', 'Grobiņas novads'],
    ['LV', 'LV-GU', 'Gulbenes novads'],
    ['LV', 'Iecavas novads', 'Iecavas novads'],
    ['LV', 'Ikšķiles novads', 'Ikšķiles novads'],
    ['LV', 'Ilūkstes novads', 'Ilūkstes novads'],
    ['LV', 'Inčukalna novads', 'Inčukalna novads'],
    ['LV', 'Jaunjelgavas novads', 'Jaunjelgavas novads'],
    ['LV', 'Jaunpiebalgas novads', 'Jaunpiebalgas novads'],
    ['LV', 'Jaunpils novads', 'Jaunpils novads'],
    ['LV', 'LV-JL', 'Jelgavas novads'],
    ['LV', 'LV-JK', 'Jēkabpils novads'],
    ['LV', 'Kandavas novads', 'Kandavas novads'],
    ['LV', 'Kokneses novads', 'Kokneses novads'],
    ['LV', 'Krimuldas novads', 'Krimuldas novads'],
    ['LV', 'Krustpils novads', 'Krustpils novads'],
    ['LV', 'LV-KR', 'Krāslavas novads'],
    ['LV', 'LV-KU', 'Kuldīgas novads'],
    ['LV', 'Kārsavas novads', 'Kārsavas novads'],
    ['LV', 'Lielvārdes novads', 'Lielvārdes novads'],
    ['LV', 'LV-LM', 'Limbažu novads'],
    ['LV', 'Lubānas novads', 'Lubānas novads'],
    ['LV', 'LV-LU', 'Ludzas novads'],
    ['LV', 'Līgatnes novads', 'Līgatnes novads'],
    ['LV', 'Līvānu novads', 'Līvānu novads'],
    ['LV', 'LV-MA', 'Madonas novads'],
    ['LV', 'Mazsalacas novads', 'Mazsalacas novads'],
    ['LV', 'Mālpils novads', 'Mālpils novads'],
    ['LV', 'Mārupes novads', 'Mārupes novads'],
    ['LV', 'Naukšēnu novads', 'Naukšēnu novads'],
    ['LV', 'Neretas novads', 'Neretas novads'],
    ['LV', 'Nīcas novads', 'Nīcas novads'],
    ['LV', 'LV-OG', 'Ogres novads'],
    ['LV', 'Olaines novads', 'Olaines novads'],
    ['LV', 'Ozolnieku novads', 'Ozolnieku novads'],
    ['LV', 'LV-PR', 'Preiļu novads'],
    ['LV', 'Priekules novads', 'Priekules novads'],
    ['LV', 'Priekuļu novads', 'Priekuļu novads'],
    ['LV', 'Pārgaujas novads', 'Pārgaujas novads'],
    ['LV', 'Pāvilostas novads', 'Pāvilostas novads'],
    ['LV', 'Pļaviņu novads', 'Pļaviņu novads'],
    ['LV', 'Raunas novads', 'Raunas novads'],
    ['LV', 'Riebiņu novads', 'Riebiņu novads'],
    ['LV', 'Rojas novads', 'Rojas novads'],
    ['LV', 'Ropažu novads', 'Ropažu novads'],
    ['LV', 'Rucavas novads', 'Rucavas novads'],
    ['LV', 'Rugāju novads', 'Rugāju novads'],
    ['LV', 'Rundāles novads', 'Rundāles novads'],
    ['LV', 'LV-RE', 'Rēzeknes novads'],
    ['LV', 'Rūjienas novads', 'Rūjienas novads'],
    ['LV', 'Salacgrīvas novads', 'Salacgrīvas novads'],
    ['LV', 'Salas novads', 'Salas novads'],
    ['LV', 'Salaspils novads', 'Salaspils novads'],
    ['LV', 'LV-SA', 'Saldus novads'],
    ['LV', 'Saulkrastu novads', 'Saulkrastu novads'],
    ['LV', 'Siguldas novads', 'Siguldas novads'],
    ['LV', 'Skrundas novads', 'Skrundas novads'],
    ['LV', 'Skrīveru novads', 'Skrīveru novads'],
    ['LV', 'Smiltenes novads', 'Smiltenes novads'],
    ['LV', 'Stopiņu novads', 'Stopiņu novads'],
    ['LV', 'Strenču novads', 'Strenču novads'],
    ['LV', 'Sējas novads', 'Sējas novads'],
    ['LV', 'LV-TA', 'Talsu novads'],
    ['LV', 'LV-TU', 'Tukuma novads'],
    ['LV', 'Tērvetes novads', 'Tērvetes novads'],
    ['LV', 'Vaiņodes novads', 'Vaiņodes novads'],
    ['LV', 'LV-VK', 'Valkas novads'],
    ['LV', 'LV-VM', 'Valmieras novads'],
    ['LV', 'Varakļānu novads', 'Varakļānu novads'],
    ['LV', 'Vecpiebalgas novads', 'Vecpiebalgas novads'],
    ['LV', 'Vecumnieku novads', 'Vecumnieku novads'],
    ['LV', 'LV-VE', 'Ventspils novads'],
    ['LV', 'Viesītes novads', 'Viesītes novads'],
    ['LV', 'Viļakas novads', 'Viļakas novads'],
    ['LV', 'Viļānu novads', 'Viļānu novads'],
    ['LV', 'Vārkavas novads', 'Vārkavas novads'],
    ['LV', 'Zilupes novads', 'Zilupes novads'],
    ['LV', 'Ādažu novads', 'Ādažu novads'],
    ['LV', 'Ērgļu novads', 'Ērgļu novads'],
    ['LV', 'Ķeguma novads', 'Ķeguma novads'],
    ['LV', 'Ķekavas novads', 'Ķekavas novads'],

    //ISO-3166-2:LT
    ['LT', 'LT-AL', 'Alytaus Apskritis'],
    ['LT', 'LT-KU', 'Kauno Apskritis'],
    ['LT', 'LT-KL', 'Klaipėdos Apskritis'],
    ['LT', 'LT-MR', 'Marijampolės Apskritis'],
    ['LT', 'LT-PN', 'Panevėžio Apskritis'],
    ['LT', 'LT-SA', 'Šiaulių Apskritis'],
    ['LT', 'LT-TA', 'Tauragės Apskritis'],
    ['LT', 'LT-TE', 'Telšių Apskritis'],
    ['LT', 'LT-UT', 'Utenos Apskritis'],
    ['LT', 'LT-VL', 'Vilniaus Apskritis'],
];

foreach ($regionsToIns as $row) {
    if (! ($connection->fetchOne("SELECT 1 FROM `{$regionTable}` WHERE `country_id` = :country_id && `code` = :code", ['country_id' => $row[0], 'code' => $row[1]]))) {
        $connection->insert($regionTable, [
            'country_id'   => $row[0],
            'code'         => $row[1],
            'default_name' => $row[2]
        ]);
    }
}
