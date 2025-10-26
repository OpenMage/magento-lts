<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
