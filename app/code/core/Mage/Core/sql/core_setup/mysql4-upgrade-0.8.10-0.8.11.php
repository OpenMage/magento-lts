<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey($installer->getTable('design_change'), 'FK_DESIGN_CHANGE_STORE');

$storeIds = $installer->getConnection()->fetchCol(
    "SELECT store_id FROM {$installer->getTable('core_store')}",
);

if (!empty($storeIds)) {
    $storeIds = implode(',', $storeIds);
    $installer->run("DELETE FROM {$installer->getTable('design_change')} WHERE store_id NOT IN ($storeIds)");
}

$installer->getConnection()->addConstraint(
    'FK_DESIGN_CHANGE_STORE',
    $installer->getTable('design_change'),
    'store_id',
    $installer->getTable('core_store'),
    'store_id',
);

$installer->endSetup();
