<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
