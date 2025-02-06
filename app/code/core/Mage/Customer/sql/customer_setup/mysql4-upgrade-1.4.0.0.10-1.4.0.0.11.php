<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('customer/eav_attribute'),
    'data_model',
    'varchar(255) default NULL',
);

$installer->updateAttribute('customer_address', 'postcode', 'data_model', 'customer/attribute_data_postcode');
