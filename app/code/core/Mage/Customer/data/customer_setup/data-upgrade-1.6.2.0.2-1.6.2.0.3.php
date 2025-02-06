<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$datetimeType = 'datetime';
// implementation new type for static date attributes
$installer->updateAttribute('customer', 'created_at', 'frontend_input', $datetimeType);

// implement new input filter for datetime type attribute
$attribute = $installer->getAttribute('customer', 'created_at');

$attributeBind = [
    'input_filter' => $datetimeType,
];

$attributeWhere = $installer->getConnection()->quoteInto('attribute_id=?', $attribute['attribute_id']);
$installer->getConnection()->update($installer->getTable('customer/eav_attribute'), $attributeBind, $attributeWhere);
