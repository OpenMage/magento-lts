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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

/* @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection  = $installer->getConnection();

$regionTable = $installer->getTable('directory/country_region');

$regionsToIns = array(
    //After reform of 2010 January
    array('FI', 'Lappi', 'Lappi'),
    array('FI', 'Pohjois-Pohjanmaa', 'Pohjois-Pohjanmaa'),
    array('FI', 'Kainuu', 'Kainuu'),
    array('FI', 'Pohjois-Karjala', 'Pohjois-Karjala'),
    array('FI', 'Pohjois-Savo', 'Pohjois-Savo'),
    array('FI', 'Etelä-Savo', 'Etelä-Savo'),
    array('FI', 'Etelä-Pohjanmaa', 'Etelä-Pohjanmaa'),
    array('FI', 'Pohjanmaa', 'Pohjanmaa'),
    array('FI', 'Pirkanmaa', 'Pirkanmaa'),
    array('FI', 'Satakunta', 'Satakunta'),
    array('FI', 'Keski-Pohjanmaa', 'Keski-Pohjanmaa'),
    array('FI', 'Keski-Suomi', 'Keski-Suomi'),
    array('FI', 'Varsinais-Suomi', 'Varsinais-Suomi'),
    array('FI', 'Etelä-Karjala', 'Etelä-Karjala'),
    array('FI', 'Päijät-Häme', 'Päijät-Häme'),
    array('FI', 'Kanta-Häme', 'Kanta-Häme'),
    array('FI', 'Uusimaa', 'Uusimaa'),
    array('FI', 'Itä-Uusimaa', 'Itä-Uusimaa'),
    array('FI', 'Kymenlaakso', 'Kymenlaakso'),
    array('FI', 'Ahvenanmaa', 'Ahvenanmaa'),

    //ISO-3166-2:EE
    array('EE', 'EE-37', 'Harjumaa'),
    array('EE', 'EE-39', 'Hiiumaa'),
    array('EE', 'EE-44', 'Ida-Virumaa'),
    array('EE', 'EE-49', 'Jõgevamaa'),
    array('EE', 'EE-51', 'Järvamaa'),
    array('EE', 'EE-57', 'Läänemaa'),
    array('EE', 'EE-59', 'Lääne-Virumaa'),
    array('EE', 'EE-65', 'Põlvamaa'),
    array('EE', 'EE-67', 'Pärnumaa'),
    array('EE', 'EE-70', 'Raplamaa'),
    array('EE', 'EE-74', 'Saaremaa'),
    array('EE', 'EE-78', 'Tartumaa'),
    array('EE', 'EE-82', 'Valgamaa'),
    array('EE', 'EE-84', 'Viljandimaa'),
    array('EE', 'EE-86', 'Võrumaa'),

    //After reform of 2009 July
    array('LV', 'LV-DGV', 'Daugavpils'),//now become good
    array('LV', 'LV-JEL', 'Jelgava'),
    array('LV', 'Jēkabpils', 'Jēkabpils'),
    array('LV', 'LV-JUR', 'Jūrmala'),
    array('LV', 'LV-LPX', 'Liepāja'),
    array('LV', 'LV-LE', 'Liepājas novads'),
    array('LV', 'LV-REZ', 'Rēzekne'),
    array('LV', 'LV-RIX', 'Rīga'),
    array('LV', 'LV-RI', 'Rīgas novads'),
    array('LV', 'Valmiera', 'Valmiera'),
    array('LV', 'LV-VEN', 'Ventspils'),
    array('LV', 'Aglonas novads', 'Aglonas novads'),
    array('LV', 'LV-AI', 'Aizkraukles novads'),
    array('LV', 'Aizputes novads', 'Aizputes novads'),
    array('LV', 'Aknīstes novads', 'Aknīstes novads'),
    array('LV', 'Alojas novads', 'Alojas novads'),
    array('LV', 'Alsungas novads', 'Alsungas novads'),
    array('LV', 'LV-AL', 'Alūksnes novads'),
    array('LV', 'Amatas novads', 'Amatas novads'),
    array('LV', 'Apes novads', 'Apes novads'),
    array('LV', 'Auces novads', 'Auces novads'),
    array('LV', 'Babītes novads', 'Babītes novads'),
    array('LV', 'Baldones novads', 'Baldones novads'),
    array('LV', 'Baltinavas novads', 'Baltinavas novads'),
    array('LV', 'LV-BL', 'Balvu novads'),
    array('LV', 'LV-BU', 'Bauskas novads'),
    array('LV', 'Beverīnas novads', 'Beverīnas novads'),
    array('LV', 'Brocēnu novads', 'Brocēnu novads'),
    array('LV', 'Burtnieku novads', 'Burtnieku novads'),
    array('LV', 'Carnikavas novads', 'Carnikavas novads'),
    array('LV', 'Cesvaines novads', 'Cesvaines novads'),
    array('LV', 'Ciblas novads', 'Ciblas novads'),
    array('LV', 'LV-CE', 'Cēsu novads'),
    array('LV', 'Dagdas novads', 'Dagdas novads'),
    array('LV', 'LV-DA', 'Daugavpils novads'),
    array('LV', 'LV-DO', 'Dobeles novads'),
    array('LV', 'Dundagas novads', 'Dundagas novads'),
    array('LV', 'Durbes novads', 'Durbes novads'),
    array('LV', 'Engures novads', 'Engures novads'),
    array('LV', 'Garkalnes novads', 'Garkalnes novads'),
    array('LV', 'Grobiņas novads', 'Grobiņas novads'),
    array('LV', 'LV-GU', 'Gulbenes novads'),
    array('LV', 'Iecavas novads', 'Iecavas novads'),
    array('LV', 'Ikšķiles novads', 'Ikšķiles novads'),
    array('LV', 'Ilūkstes novads', 'Ilūkstes novads'),
    array('LV', 'Inčukalna novads', 'Inčukalna novads'),
    array('LV', 'Jaunjelgavas novads', 'Jaunjelgavas novads'),
    array('LV', 'Jaunpiebalgas novads', 'Jaunpiebalgas novads'),
    array('LV', 'Jaunpils novads', 'Jaunpils novads'),
    array('LV', 'LV-JL', 'Jelgavas novads'),
    array('LV', 'LV-JK', 'Jēkabpils novads'),
    array('LV', 'Kandavas novads', 'Kandavas novads'),
    array('LV', 'Kokneses novads', 'Kokneses novads'),
    array('LV', 'Krimuldas novads', 'Krimuldas novads'),
    array('LV', 'Krustpils novads', 'Krustpils novads'),
    array('LV', 'LV-KR', 'Krāslavas novads'),
    array('LV', 'LV-KU', 'Kuldīgas novads'),
    array('LV', 'Kārsavas novads', 'Kārsavas novads'),
    array('LV', 'Lielvārdes novads', 'Lielvārdes novads'),
    array('LV', 'LV-LM', 'Limbažu novads'),
    array('LV', 'Lubānas novads', 'Lubānas novads'),
    array('LV', 'LV-LU', 'Ludzas novads'),
    array('LV', 'Līgatnes novads', 'Līgatnes novads'),
    array('LV', 'Līvānu novads', 'Līvānu novads'),
    array('LV', 'LV-MA', 'Madonas novads'),
    array('LV', 'Mazsalacas novads', 'Mazsalacas novads'),
    array('LV', 'Mālpils novads', 'Mālpils novads'),
    array('LV', 'Mārupes novads', 'Mārupes novads'),
    array('LV', 'Naukšēnu novads', 'Naukšēnu novads'),
    array('LV', 'Neretas novads', 'Neretas novads'),
    array('LV', 'Nīcas novads', 'Nīcas novads'),
    array('LV', 'LV-OG', 'Ogres novads'),
    array('LV', 'Olaines novads', 'Olaines novads'),
    array('LV', 'Ozolnieku novads', 'Ozolnieku novads'),
    array('LV', 'LV-PR', 'Preiļu novads'),
    array('LV', 'Priekules novads', 'Priekules novads'),
    array('LV', 'Priekuļu novads', 'Priekuļu novads'),
    array('LV', 'Pārgaujas novads', 'Pārgaujas novads'),
    array('LV', 'Pāvilostas novads', 'Pāvilostas novads'),
    array('LV', 'Pļaviņu novads', 'Pļaviņu novads'),
    array('LV', 'Raunas novads', 'Raunas novads'),
    array('LV', 'Riebiņu novads', 'Riebiņu novads'),
    array('LV', 'Rojas novads', 'Rojas novads'),
    array('LV', 'Ropažu novads', 'Ropažu novads'),
    array('LV', 'Rucavas novads', 'Rucavas novads'),
    array('LV', 'Rugāju novads', 'Rugāju novads'),
    array('LV', 'Rundāles novads', 'Rundāles novads'),
    array('LV', 'LV-RE', 'Rēzeknes novads'),
    array('LV', 'Rūjienas novads', 'Rūjienas novads'),
    array('LV', 'Salacgrīvas novads', 'Salacgrīvas novads'),
    array('LV', 'Salas novads', 'Salas novads'),
    array('LV', 'Salaspils novads', 'Salaspils novads'),
    array('LV', 'LV-SA', 'Saldus novads'),
    array('LV', 'Saulkrastu novads', 'Saulkrastu novads'),
    array('LV', 'Siguldas novads', 'Siguldas novads'),
    array('LV', 'Skrundas novads', 'Skrundas novads'),
    array('LV', 'Skrīveru novads', 'Skrīveru novads'),
    array('LV', 'Smiltenes novads', 'Smiltenes novads'),
    array('LV', 'Stopiņu novads', 'Stopiņu novads'),
    array('LV', 'Strenču novads', 'Strenču novads'),
    array('LV', 'Sējas novads', 'Sējas novads'),
    array('LV', 'LV-TA', 'Talsu novads'),
    array('LV', 'LV-TU', 'Tukuma novads'),
    array('LV', 'Tērvetes novads', 'Tērvetes novads'),
    array('LV', 'Vaiņodes novads', 'Vaiņodes novads'),
    array('LV', 'LV-VK', 'Valkas novads'),
    array('LV', 'LV-VM', 'Valmieras novads'),
    array('LV', 'Varakļānu novads', 'Varakļānu novads'),
    array('LV', 'Vecpiebalgas novads', 'Vecpiebalgas novads'),
    array('LV', 'Vecumnieku novads', 'Vecumnieku novads'),
    array('LV', 'LV-VE', 'Ventspils novads'),
    array('LV', 'Viesītes novads', 'Viesītes novads'),
    array('LV', 'Viļakas novads', 'Viļakas novads'),
    array('LV', 'Viļānu novads', 'Viļānu novads'),
    array('LV', 'Vārkavas novads', 'Vārkavas novads'),
    array('LV', 'Zilupes novads', 'Zilupes novads'),
    array('LV', 'Ādažu novads', 'Ādažu novads'),
    array('LV', 'Ērgļu novads', 'Ērgļu novads'),
    array('LV', 'Ķeguma novads', 'Ķeguma novads'),
    array('LV', 'Ķekavas novads', 'Ķekavas novads'),
    
    //ISO-3166-2:LT
    array('LT', 'LT-AL', 'Alytaus Apskritis'),
    array('LT', 'LT-KU', 'Kauno Apskritis'),
    array('LT', 'LT-KL', 'Klaipėdos Apskritis'),
    array('LT', 'LT-MR', 'Marijampolės Apskritis'),
    array('LT', 'LT-PN', 'Panevėžio Apskritis'),
    array('LT', 'LT-SA', 'Šiaulių Apskritis'),
    array('LT', 'LT-TA', 'Tauragės Apskritis'),
    array('LT', 'LT-TE', 'Telšių Apskritis'),
    array('LT', 'LT-UT', 'Utenos Apskritis'),
    array('LT', 'LT-VL', 'Vilniaus Apskritis'),
);

foreach ($regionsToIns as $row) {
    if (! ($connection->fetchOne("SELECT 1 FROM `{$regionTable}` WHERE `country_id` = :country_id && `code` = :code", array('country_id' => $row[0], 'code' => $row[1])))) {
        $connection->insert($regionTable, array(
            'country_id'   => $row[0],
            'code'         => $row[1],
            'default_name' => $row[2]
        ));
    } 
}

