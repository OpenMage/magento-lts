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
$installer->startSetup();

$installer->getConnection()->dropForeignKey(
    $installer->getTable('customer_eav_attribute_website'),
    'FK_CUSTOMER_EAV_ATTRIBUTE_WEBSITE_ATTRIBUTE_EAV_ATTRIBUTE'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('customer_eav_attribute_website'),
    'FK_CUSTOMER_EAV_ATTRIBUTE_WEBSITE_WEBSITE_CORE_WEBSITE'
);

$installer->getConnection()->addConstraint(
    'FK_CUST_EAV_ATTR_WEBST_ATTR_EAV_ATTR',
    $installer->getTable('customer_eav_attribute_website'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id'
);
$installer->getConnection()->addConstraint(
    'FK_CUST_EAV_ATTR_WEBST_WEBST_CORE_WEBST',
    $installer->getTable('customer_eav_attribute_website'),
    'website_id',
    $installer->getTable('core_website'),
    'website_id'
);

$installer->endSetup();
