<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->getConnection()->addKey($installer->getTable('sales/quote'), 'IDX_IS_ACTIVE', 'is_active');
$installer->getConnection()->addKey($installer->getTable('sales/order_item'), 'IDX_PRODUCT_ID', 'product_id');
