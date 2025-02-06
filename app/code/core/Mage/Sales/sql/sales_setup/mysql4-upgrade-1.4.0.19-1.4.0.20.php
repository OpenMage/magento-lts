<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$installer->getConnection()->addKey($installer->getTable('sales/quote'), 'IDX_IS_ACTIVE', 'is_active');
$installer->getConnection()->addKey($installer->getTable('sales/order_item'), 'IDX_PRODUCT_ID', 'product_id');
