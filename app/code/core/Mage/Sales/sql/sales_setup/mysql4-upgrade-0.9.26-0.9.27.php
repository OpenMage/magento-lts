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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

// very long update :)
set_time_limit(0);

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->getConnection()->addColumn(
    $this->getTable('sales/quote'), 'global_currency_code', 'varchar(255) NULL AFTER `store_to_quote_rate`'
);
$installer->getConnection()->addColumn(
    $this->getTable('sales/quote'), 'base_to_quote_rate', 'decimal(12,4) NULL AFTER `store_to_quote_rate`'
);
$installer->getConnection()->addColumn(
    $this->getTable('sales/quote'), 'base_to_global_rate', 'decimal(12,4) NULL AFTER `store_to_quote_rate`'
);

$installer->addAttribute('quote', 'global_currency_code', array('type'=>'static'));
$installer->addAttribute('quote', 'base_to_global_rate', array('type'=>'static'));
$installer->addAttribute('quote', 'base_to_quote_rate', array('type'=>'static'));

$installer->addAttribute('order', 'global_currency_code', array('type'=>'varchar'));
$installer->addAttribute('order', 'base_to_global_rate', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_to_order_rate', array('type'=>'decimal'));

$installer->addAttribute('invoice', 'global_currency_code', array('type'=>'varchar'));
$installer->addAttribute('invoice', 'base_to_global_rate', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'base_to_order_rate', array('type'=>'decimal'));

$installer->addAttribute('creditmemo', 'global_currency_code', array('type'=>'varchar'));
$installer->addAttribute('creditmemo', 'base_to_global_rate', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'base_to_order_rate', array('type'=>'decimal'));

/*
 * getting all base currency codes and placing them in newly created attribute
 */

$orderEntityType = $installer->getEntityType('order');
$orderEntityType['entity_table'] = 'sales_order';
$invoiceEntityType = $installer->getEntityType('invoice');
$invoiceEntityType['entity_table'] = 'sales_order_entity';
$creditmemoEntityType = $installer->getEntityType('creditmemo');
$creditmemoEntityType['entity_table'] = 'sales_order_entity';

$entityTypes = array($orderEntityType['entity_type_id'] => $orderEntityType,
    $invoiceEntityType['entity_type_id'] => $invoiceEntityType,
    $creditmemoEntityType['entity_type_id'] => $creditmemoEntityType);

try {
    $installer->getConnection()->beginTransaction();

    foreach ($entityTypes as $typeId => $entity) {

        $globalCurrencyCode = $installer->getAttribute($typeId, 'global_currency_code');
        if ($globalCurrencyCode['backend_type'] == 'static') {
            $globalCurrencyCodeTable = $this->getTable($entity['entity_table']);
        } else {
            $globalCurrencyCodeTable = $this->getTable($entity['entity_table']) . '_'
                . $globalCurrencyCode['backend_type'];
        }

        $baseCurrencyCode = $installer->getAttribute($typeId, 'base_currency_code');
        if ($baseCurrencyCode['backend_type'] == 'static') {
            $baseCurrencyCodeTable = $this->getTable($entity['entity_table']);
        } else {
            $baseCurrencyCodeTable = $this->getTable($entity['entity_table']) . '_'
                . $baseCurrencyCode['backend_type'];
        }

        $storeCurrencyCode = $installer->getAttribute($typeId, 'store_currency_code');
        if ($storeCurrencyCode['backend_type'] == 'static') {
            $storeCurrencyCodeTable = $this->getTable($entity['entity_table']);
        } else {
            $storeCurrencyCodeTable = $this->getTable($entity['entity_table']) . '_'
                . $storeCurrencyCode['backend_type'];
        }

        $baseToGlobalRate = $installer->getAttribute($typeId, 'base_to_global_rate');
        if ($baseToGlobalRate['backend_type'] == 'static') {
            $baseToGlobalRateTable = $this->getTable($entity['entity_table']);
        } else {
            $baseToGlobalRateTable = $this->getTable($entity['entity_table']) . '_' . $baseToGlobalRate['backend_type'];
        }

        $storeToBaseRate = $installer->getAttribute($typeId, 'store_to_base_rate');
        if ($storeToBaseRate['backend_type'] == 'static') {
            $storeToBaseRateTable = $this->getTable($entity['entity_table']);
        } else {
            $storeToBaseRateTable = $this->getTable($entity['entity_table']) . '_' . $storeToBaseRate['backend_type'];
        }

        $baseToOrderRate = $installer->getAttribute($typeId, 'base_to_order_rate');
        if ($baseToOrderRate['backend_type'] == 'static') {
            $baseToOrderRateTable = $this->getTable($entity['entity_table']);
        } else {
            $baseToOrderRateTable = $this->getTable($entity['entity_table']) . '_' . $baseToOrderRate['backend_type'];
        }

        $storeToOrderRate = $installer->getAttribute($typeId, 'store_to_order_rate');
        if ($storeToOrderRate['backend_type'] == 'static') {
            $storeToOrderRateTable = $this->getTable($entity['entity_table']);
        } else {
            $storeToOrderRateTable = $this->getTable($entity['entity_table']) . '_' . $storeToOrderRate['backend_type'];
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //copy data from base_currency_code into global_currency_code
        $query = 'INSERT INTO `' . $globalCurrencyCodeTable .
            '` (`entity_type_id`, `attribute_id`, `entity_id`, `value`) SELECT `entity_type_id`, "' .
            $globalCurrencyCode['attribute_id'] . '" as `attribute_id`, `entity_id`, `value` FROM `' .
            $baseCurrencyCodeTable . '` WHERE `attribute_id` = ' . $baseCurrencyCode['attribute_id'] . ';';

        //echo $query . "<br />";
        $installer->getConnection()->query($query);

        //delete old data in base_currency_code
        $query = 'DELETE FROM `' . $baseCurrencyCodeTable . '` WHERE `attribute_id` = '
            . $baseCurrencyCode['attribute_id'] . ';';

        //echo $query . "<br />";
        $installer->getConnection()->query($query);

        //copy data from store_currency_code into base_currency_code
        $query = 'INSERT INTO `' . $baseCurrencyCodeTable .
            '` (`entity_type_id`, `attribute_id`, `entity_id`, `value`) SELECT `entity_type_id`, "' .
            $baseCurrencyCode['attribute_id'] . '" as `attribute_id`, `entity_id`, `value` FROM `' .
            $storeCurrencyCodeTable . '` WHERE `attribute_id` = ' . $storeCurrencyCode['attribute_id'] . ';';

        //echo $query . "<br />";
        $installer->getConnection()->query($query);

        //copy data from store_to_base_rate into base_to_global_rate
        $query = 'INSERT INTO `' . $baseToGlobalRateTable .
            '` (`entity_type_id`, `attribute_id`, `entity_id`, `value`) SELECT `entity_type_id`, "' .
            $baseToGlobalRate['attribute_id'] . '" as `attribute_id`, `entity_id`, `value` FROM `' .
            $storeToBaseRateTable . '` WHERE `attribute_id` = ' . $storeToBaseRate['attribute_id'] . ';';

        //echo $query . "<br />";
        $installer->getConnection()->query($query);

        //copy data from store_to_order_rate into base_to_order_rate
        $query = 'INSERT INTO `' . $baseToOrderRateTable .
            '` (`entity_type_id`, `attribute_id`, `entity_id`, `value`) SELECT `entity_type_id`, "' .
            $baseToOrderRate['attribute_id'] . '" as `attribute_id`, `entity_id`, `value` FROM `' .
            $storeToOrderRateTable . '` WHERE `attribute_id` = ' . $storeToOrderRate['attribute_id'] . ';';

        //echo $query . "<br />";
        $installer->getConnection()->query($query);
    }

    $installer->getConnection()->commit();
} catch(Exception $e) {
    $installer->getConnection()->rollBack();
    throw $e;
}
