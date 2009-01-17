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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Entity_Setup */

$installer->addAttribute('quote', 'customer_prefix', array('type'=>'static'));
$installer->addAttribute('quote', 'customer_middlename', array('type'=>'static'));
$installer->addAttribute('quote', 'customer_suffix', array('type'=>'static'));

$installer->addAttribute('quote_address', 'prefix', array('type'=>'static'));
$installer->addAttribute('quote_address', 'middlename', array('type'=>'static'));
$installer->addAttribute('quote_address', 'suffix', array('type'=>'static'));
