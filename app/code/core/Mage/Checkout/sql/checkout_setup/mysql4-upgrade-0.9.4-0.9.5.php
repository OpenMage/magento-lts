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
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Checkout_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('core/config_data');

$select = $connection->select()
    ->from($table, array('config_id', 'value'))
    ->where('path = ?', 'checkout/options/onepage_checkout_disabled');

$data = $connection->fetchAll($select);

if ($data) {
    try {
        $connection->beginTransaction();

        foreach ($data as $value) {
            $bind = array(
                'path'  => 'checkout/options/onepage_checkout_enabled',
                'value' => !((bool)$value['value'])
            );
            $where = 'config_id = ' . $value['config_id'];
            $connection->update($table, $bind, $where);
        }

        $connection->commit();
    } catch (Exception $e) {
        $installer->getConnection()->rollback();
        throw $e;
    }
}
$installer->endSetup();
