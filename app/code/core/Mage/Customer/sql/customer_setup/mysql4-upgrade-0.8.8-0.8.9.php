<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
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
