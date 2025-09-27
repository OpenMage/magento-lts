<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $conn */
$conn = $installer->getConnection();

$conn->addColumn($installer->getTable('sales_quote'), 'customer_dob', 'datetime after customer_suffix');
$installer->addAttribute('quote', 'customer_dob', ['type' => 'static', 'backend' => 'eav/entity_attribute_backend_datetime']);

$installer->addAttribute('order', 'customer_dob', ['type' => 'datetime', 'backend' => 'eav/entity_attribute_backend_datetime']);
