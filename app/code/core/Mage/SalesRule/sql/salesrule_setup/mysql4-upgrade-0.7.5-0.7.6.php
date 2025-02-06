<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$conn->addColumn($this->getTable('salesrule'), 'discount_step', 'int unsigned not null after discount_qty');

$installer->endSetup();
