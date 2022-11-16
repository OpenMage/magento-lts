<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
