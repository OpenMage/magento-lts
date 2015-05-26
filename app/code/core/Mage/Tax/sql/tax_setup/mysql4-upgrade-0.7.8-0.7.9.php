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
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Tax_Model_Mysql4_Setup */

$installer->addAttribute('invoice', 'shipping_tax_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'base_shipping_tax_amount', array('type'=>'decimal'));

$installer->addAttribute('creditmemo', 'shipping_tax_amount', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'base_shipping_tax_amount', array('type'=>'decimal'));

$installer->addAttribute('quote_item', 'price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'base_price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'base_row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_address_item', 'price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_address_item', 'base_price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_address_item', 'row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_address_item', 'base_row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'subtotal_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'base_subtotal_total_incl_tax', array('type'=>'decimal'));

$installer->addAttribute('order_item', 'price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'base_price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'base_row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('order', 'subtotal_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_subtotal_incl_tax', array('type'=>'decimal'));

$installer->addAttribute('invoice_item', 'price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'base_price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'base_row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'subtotal_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'base_subtotal_incl_tax', array('type'=>'decimal'));

$installer->addAttribute('creditmemo_item', 'price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'base_price_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'base_row_total_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'subtotal_incl_tax', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'base_subtotal_incl_tax', array('type'=>'decimal'));

