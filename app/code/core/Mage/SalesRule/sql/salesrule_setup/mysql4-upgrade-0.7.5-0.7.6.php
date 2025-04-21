<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$conn->addColumn($this->getTable('salesrule'), 'discount_step', 'int unsigned not null after discount_qty');

$installer->endSetup();
