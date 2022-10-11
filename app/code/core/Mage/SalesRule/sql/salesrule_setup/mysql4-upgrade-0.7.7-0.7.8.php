<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

/**
 * add attributes discount_description, shipping_discount_amount, base_shipping_discount_amount
 */
$installer->addAttribute('quote_address', 'discount_description', ['type'=>'varchar']);
$installer->addAttribute('quote_address', 'shipping_discount_amount', ['type'=>'decimal']);
$installer->addAttribute('quote_address', 'base_shipping_discount_amount', ['type'=>'decimal']);

$installer->addAttribute('order', 'discount_description', ['type'=>'varchar']);
$installer->addAttribute('order', 'shipping_discount_amount', ['type'=>'decimal']);
$installer->addAttribute('order', 'base_shipping_discount_amount', ['type'=>'decimal']);
