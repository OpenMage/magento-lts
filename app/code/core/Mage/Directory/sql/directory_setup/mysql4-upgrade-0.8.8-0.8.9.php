<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection  = $this->getConnection();

$regionTable = $installer->getTable('directory/country_region');
$regionsToIns = [
    ['RO', 'AB', 'Alba'], ['RO', 'AR', 'Arad'], ['RO', 'AG', 'Argeş'],
    ['RO', 'BC', 'Bacău'], ['RO', 'BH', 'Bihor'], ['RO', 'BN', 'Bistriţa-Năsăud'],
    ['RO', 'BT', 'Botoşani'], ['RO', 'BV', 'Braşov'], ['RO', 'BR', 'Brăila'],
    ['RO', 'B', 'Bucureşti'], ['RO', 'BZ', 'Buzău'], ['RO', 'CS', 'Caraş-Severin'],
    ['RO', 'CL', 'Călăraşi'], ['RO', 'CJ', 'Cluj'], ['RO', 'CT', 'Constanţa'],
    ['RO', 'CV', 'Covasna'], ['RO', 'DB', 'Dâmboviţa'], ['RO', 'DJ', 'Dolj'],
    ['RO', 'GL', 'Galaţi'], ['RO', 'GR', 'Giurgiu'], ['RO', 'GJ', 'Gorj'],
    ['RO', 'HR', 'Harghita'], ['RO', 'HD', 'Hunedoara'], ['RO', 'IL', 'Ialomiţa'],
    ['RO', 'IS', 'Iaşi'], ['RO', 'IF', 'Ilfov'], ['RO', 'MM', 'Maramureş'],
    ['RO', 'MH', 'Mehedinţi'], ['RO', 'MS', 'Mureş'], ['RO', 'NT', 'Neamţ'],
    ['RO', 'OT', 'Olt'], ['RO', 'PH', 'Prahova'], ['RO', 'SM', 'Satu-Mare'],
    ['RO', 'SJ', 'Sălaj'], ['RO', 'SB', 'Sibiu'], ['RO', 'SV', 'Suceava'],
    ['RO', 'TR', 'Teleorman'], ['RO', 'TM', 'Timiş'], ['RO', 'TL', 'Tulcea'],
    ['RO', 'VS', 'Vaslui'], ['RO', 'VL', 'Vâlcea'], ['RO', 'VN', 'Vrancea'],
];

foreach ($regionsToIns as $row) {
    if (!($connection->fetchOne("SELECT 1 FROM `{$regionTable}` WHERE `country_id` = :country_id && `code` = :code", ['country_id' => $row[0], 'code' => $row[1]]))) {
        $connection->insert($regionTable, [
            'country_id'   => $row[0],
            'code'         => $row[1],
            'default_name' => $row[2],
        ]);
    }
}
