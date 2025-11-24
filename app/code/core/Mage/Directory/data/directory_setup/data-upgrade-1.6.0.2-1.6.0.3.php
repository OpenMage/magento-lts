<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

$data = [
    ['directory/country_region', 'default_name'],
    ['directory/country_region_name', 'name'],
];

foreach ($data as $row) {
    $installer->getConnection()->update(
        $installer->getTable($row[0]),
        [$row[1]          => 'Vorarlberg'],
        [$row[1] . ' = ?' => 'Voralberg'],
    );
}
