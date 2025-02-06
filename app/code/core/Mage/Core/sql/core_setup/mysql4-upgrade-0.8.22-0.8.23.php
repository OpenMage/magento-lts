<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
