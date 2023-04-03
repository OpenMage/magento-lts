<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice_comment'),
    'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`'
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/shipment_comment'),
    'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`'
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo_comment'),
    'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`'
);

$installer->endSetup();
