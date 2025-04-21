<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addKey($installer->getTable('customer_address_entity_datetime'), 'IDX_VALUE', ['entity_id', 'attribute_id', 'value']);
$installer->getConnection()->addKey($installer->getTable('customer_address_entity_decimal'), 'IDX_VALUE', ['entity_id', 'attribute_id', 'value']);
$installer->getConnection()->addKey($installer->getTable('customer_address_entity_int'), 'IDX_VALUE', ['entity_id', 'attribute_id', 'value']);
$installer->getConnection()->addKey($installer->getTable('customer_address_entity_text'), 'IDX_VALUE', ['entity_id', 'attribute_id']);
$installer->getConnection()->addKey($installer->getTable('customer_address_entity_varchar'), 'IDX_VALUE', ['entity_id', 'attribute_id', 'value']);

$installer->getConnection()->addKey($installer->getTable('customer_entity_datetime'), 'IDX_VALUE', ['entity_id', 'attribute_id', 'value']);
$installer->getConnection()->addKey($installer->getTable('customer_entity_decimal'), 'IDX_VALUE', ['entity_id', 'attribute_id', 'value']);
$installer->getConnection()->addKey($installer->getTable('customer_entity_int'), 'IDX_VALUE', ['entity_id', 'attribute_id', 'value']);
$installer->getConnection()->addKey($installer->getTable('customer_entity_text'), 'IDX_VALUE', ['entity_id', 'attribute_id']);
$installer->getConnection()->addKey($installer->getTable('customer_entity_varchar'), 'IDX_VALUE', ['entity_id', 'attribute_id', 'value']);

$installer->endSetup();
