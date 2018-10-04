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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

$installer->addAttribute('quote_item', 'base_cost', array(
    'type'              => 'decimal',
    'label'             => 'Cost',
    'visible'           => false,
    'required'          => false,
));

$installer->addAttribute('quote_address_item', 'base_cost', array(
    'type'              => 'decimal',
    'label'             => 'Cost',
    'visible'           => false,
    'required'          => false,
));

$installer->getConnection()->changeColumn($installer->getTable('sales_flat_order_item'), 'cost', 'base_cost', 'DECIMAL( 12, 4 ) NULL DEFAULT \'0.0000\'');

$installer->getConnection()->addColumn($installer->getTable('sales_order'), 'base_total_invoiced_cost', 'DECIMAL( 12, 4 ) NULL DEFAULT NULL');

$installer->addAttribute('order', 'base_total_invoiced_cost', array(
    'type'              => 'static'
));

$installer->updateAttribute('order_item', 'cost', array('attribute_code' => 'base_cost'));
$installer->updateAttribute('invoice_item', 'cost', array('attribute_code' => 'base_cost'));
$installer->updateAttribute('creditmemo_item', 'cost', array('attribute_code' => 'base_cost'));

$installer->endSetup();
