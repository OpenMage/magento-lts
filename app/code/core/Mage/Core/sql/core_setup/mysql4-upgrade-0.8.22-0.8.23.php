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

$installer->getConnection()->addColumn(
    $installer->getTable('core/layout_link'),
    'area',
    "VARCHAR(64) NOT NULL DEFAULT '' AFTER `store_id`",
);

$installer->getConnection()->update(
    $installer->getTable('core/layout_link'),
    ['area' => Mage::getSingleton('core/design_package')->getArea()],
);

$installer->endSetup();
