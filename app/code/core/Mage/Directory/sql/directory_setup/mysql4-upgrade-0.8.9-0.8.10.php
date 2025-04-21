<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection   = $this->getConnection();

$regionTable  = $installer->getTable('directory/country_region');
$regNameTable = $installer->getTable('directory/country_region_name');

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
    $regionId = $connection->fetchOne("SELECT `region_id` FROM `{$regionTable}` WHERE `country_id` = :country_id && `code` = :code", ['country_id' => $row[0], 'code' => $row[1]]);

    if (!$connection->fetchOne("SELECT 1 FROM `{$regNameTable}` WHERE `region_id` = {$regionId}")) {
        $connection->insert($regNameTable, [
            'locale'    => 'en_US',
            'region_id' => $regionId,
            'name'      => $row[2],
        ]);
    }
}
