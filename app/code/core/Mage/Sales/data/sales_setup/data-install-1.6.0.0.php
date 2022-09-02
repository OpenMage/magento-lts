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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

/**
 * Install order statuses from config
 */
$data     = [];
$statuses = Mage::getConfig()->getNode('global/sales/order/statuses')->asArray();
foreach ($statuses as $code => $info) {
    $data[] = [
        'status' => $code,
        'label'  => $info['label']
    ];
}
$installer->getConnection()->insertArray(
    $installer->getTable('sales/order_status'),
    ['status', 'label'],
    $data
);

/**
 * Install order states from config
 */
$data   = [];
$states = Mage::getConfig()->getNode('global/sales/order/states')->asArray();

foreach ($states as $code => $info) {
    if (isset($info['statuses'])) {
        foreach ($info['statuses'] as $status => $statusInfo) {
            $data[] = [
                'status'     => $status,
                'state'      => $code,
                'is_default' => is_array($statusInfo) && isset($statusInfo['@']['default']) ? 1 : 0
            ];
        }
    }
}
$installer->getConnection()->insertArray(
    $installer->getTable('sales/order_status_state'),
    ['status', 'state', 'is_default'],
    $data
);
