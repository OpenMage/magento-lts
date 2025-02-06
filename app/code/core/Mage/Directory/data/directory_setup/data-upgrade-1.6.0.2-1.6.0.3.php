<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
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
