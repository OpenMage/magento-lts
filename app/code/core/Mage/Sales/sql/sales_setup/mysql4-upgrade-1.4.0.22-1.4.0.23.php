<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$statusTable        = $installer->getTable('sales/order_status');
$statusStateTable   = $installer->getTable('sales/order_status_state');
$statusLabelTable   = $installer->getTable('sales/order_status_label');

$installer->run("
CREATE TABLE `{$statusTable}` (
  `status` varchar(32) NOT NULL,
  `label` varchar(128) NOT NULL,
  PRIMARY KEY (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$statuses = Mage::getConfig()->getNode('global/sales/order/statuses')->asArray();
$data = [];
foreach ($statuses as $code => $info) {
    $data[] = [
        'status'    => $code,
        'label'     => $info['label'],
    ];
}
$installer->getConnection()->insertArray($statusTable, ['status', 'label'], $data);

$installer->run("
CREATE TABLE `{$statusStateTable}` (
  `status` varchar(32) NOT NULL,
  `state` varchar(32) NOT NULL,
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`status`,`state`),
  CONSTRAINT `FK_SALES_ORDER_STATUS_STATE_STATUS` FOREIGN KEY (`status`)
    REFERENCES `{$statusTable}` (`status`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");
$states     = Mage::getConfig()->getNode('global/sales/order/states')->asArray();
$data = [];
foreach ($states as $code => $info) {
    if (isset($info['statuses'])) {
        foreach ($info['statuses'] as $status => $statusInfo) {
            $data[] = [
                'status'    => $status,
                'state'     => $code,
                'is_default' => is_array($statusInfo) && isset($statusInfo['@']['default']) ? 1 : 0,
            ];
        }
    }
}
$installer->getConnection()->insertArray(
    $statusStateTable,
    ['status', 'state', 'is_default'],
    $data,
);

$installer->run("
CREATE TABLE `{$statusLabelTable}` (
  `status` varchar(32) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `label` varchar(128) NOT NULL,
  PRIMARY KEY (`status`,`store_id`),
  KEY `FK_SALES_ORDER_STATUS_LABEL_STORE` (`store_id`),
  CONSTRAINT `FK_SALES_ORDER_STATUS_LABEL_STATUS` FOREIGN KEY (`status`)
    REFERENCES `{$statusTable}` (`status`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_ORDER_STATUS_LABEL_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core/store')}` (`store_id`)ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");
