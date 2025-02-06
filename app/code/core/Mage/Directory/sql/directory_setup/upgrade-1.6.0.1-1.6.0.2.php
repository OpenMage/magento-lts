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

foreach (['AM', 'AC', 'AF'] as $code) {
    $installer->getConnection()->update(
        $installer->getTable('directory/country_region'),
        ['code' => 'AE'],
        ['code = ?' => $code],
    );
}
