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

$installer->getConnection()->dropForeignKey(
    $installer->getTable('customer_eav_attribute_website'),
    'FK_CUSTOMER_EAV_ATTRIBUTE_WEBSITE_ATTRIBUTE_EAV_ATTRIBUTE',
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('customer_eav_attribute_website'),
    'FK_CUSTOMER_EAV_ATTRIBUTE_WEBSITE_WEBSITE_CORE_WEBSITE',
);

$installer->getConnection()->addConstraint(
    'FK_CUST_EAV_ATTR_WEBST_ATTR_EAV_ATTR',
    $installer->getTable('customer_eav_attribute_website'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
);
$installer->getConnection()->addConstraint(
    'FK_CUST_EAV_ATTR_WEBST_WEBST_CORE_WEBST',
    $installer->getTable('customer_eav_attribute_website'),
    'website_id',
    $installer->getTable('core_website'),
    'website_id',
);

$installer->endSetup();
