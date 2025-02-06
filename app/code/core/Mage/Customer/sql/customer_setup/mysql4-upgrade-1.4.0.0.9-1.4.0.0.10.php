<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;

$installer->updateEntityType('customer', 'entity_attribute_collection', 'customer/attribute_collection');
$installer->updateEntityType('customer_address', 'entity_attribute_collection', 'customer/address_attribute_collection');
