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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

// Add reset password link customer Id attribute
$installer->addAttribute('customer', 'rp_customer_id', array(
    'type'     => 'varchar',
    'input'    => 'hidden',
    'visible'  => false,
    'required' => false
));

$installer->endSetup();
