<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

$middlenameAttributeCode = 'middlename';

$installer->addAttribute('customer', $middlenameAttributeCode, array(
    'type'       => 'varchar',
    'label'      => 'Middle Name/Initial',
    'input'      => 'text',
    'required'   => 0,
    'sort_order' => 50,
    'is_visible' => 1,
    'is_system'  => 0,
    'position'   => 50
));

$middlenameAttribute = Mage::getSingleton('eav/config')
    ->getAttribute('customer', $middlenameAttributeCode);
$middlenameAttribute->setData('used_in_forms', array(
    'customer_account_create',
    'customer_account_edit',
    'checkout_register',
    'adminhtml_customer',
    'adminhtml_checkout'
));
$middlenameAttribute->save();

$installer->addAttribute('customer_address', $middlenameAttributeCode, array(
    'type'       => 'varchar',
    'label'      => 'Middle Name/Initial',
    'input'      => 'text',
    'required'   => 0,
    'sort_order' => 30,
    'is_visible' => 1,
    'is_system'  => 0,
    'position'   => 30
));

$middlenameAttribute = Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', $middlenameAttributeCode);
$middlenameAttribute->setData('used_in_forms', array(
    'adminhtml_customer_address',
    'customer_address_edit',
    'customer_register_address'
));
$middlenameAttribute->save();
