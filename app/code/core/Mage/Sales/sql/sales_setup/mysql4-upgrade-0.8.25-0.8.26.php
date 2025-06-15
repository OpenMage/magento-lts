<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$installer->run("
    ALTER TABLE `{$this->getTable('sales_order')}` ADD INDEX `IDX_CUSTOMER` (`customer_id`);
");
