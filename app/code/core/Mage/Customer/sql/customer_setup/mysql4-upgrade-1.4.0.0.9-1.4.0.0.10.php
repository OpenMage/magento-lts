<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;

$installer->updateEntityType('customer', 'entity_attribute_collection', 'customer/attribute_collection');
$installer->updateEntityType('customer_address', 'entity_attribute_collection', 'customer/address_attribute_collection');
