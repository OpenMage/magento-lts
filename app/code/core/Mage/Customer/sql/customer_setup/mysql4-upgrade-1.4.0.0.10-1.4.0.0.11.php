<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
