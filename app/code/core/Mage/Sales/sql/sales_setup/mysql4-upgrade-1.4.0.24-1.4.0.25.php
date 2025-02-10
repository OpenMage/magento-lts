<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->getConnection()->addKey($this->getTable('sales/order'), 'IDX_QUOTE_ID', 'quote_id');
