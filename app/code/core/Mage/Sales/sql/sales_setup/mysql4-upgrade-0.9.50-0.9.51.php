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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();
$installer->addAttribute('order_payment', 'additional_information', array('type' => 'text'));
$installer->addAttribute('quote_payment', 'additional_information', array('type' => 'text'));


$processingItemsCountForOneIteration = 1000;

$connection = $installer->getConnection();

$paymentMethods = array(
    'paypal_standard',
    'paypal_express',
    'paypal_direct',
    'paypaluk_direct',
    'paypaluk_express'
);
$entityTypeCode = 'order_payment';
$attributesIds = array(
    'method' => false,
    'additional_data' => false,
    'additional_information' => false
);

/* get order_payment entity type code*/
$entityTypeId = $connection->fetchOne("
    SELECT entity_type_id
    FROM {$this->getTable('eav_entity_type')}
    WHERE entity_type_code = '{$entityTypeCode}';
");

/* get order_payment attribute codes*/
foreach ($attributesIds as $attributeCode => $attributeId) {
    $attributesIds[$attributeCode] = $connection->fetchOne("
        SELECT attribute_id
        FROM {$this->getTable('eav_attribute')}
        WHERE attribute_code = '{$attributeCode}' and entity_type_id = {$entityTypeId};
    ");
}

/* get count of paypal order payments*/
$methodIds = "'" . implode("','", $paymentMethods) . "'";
$paymentsCount = $connection->fetchOne("
    SELECT count(entity_id) as count
    FROM {$this->getTable('sales_order_entity_varchar')}
    WHERE attribute_id = {$attributesIds['method']} and value in ({$methodIds});
");

$connection->beginTransaction();
try {

    /* process payment attributes*/
    for ($i=0; $i<=$paymentsCount; $i+=$processingItemsCountForOneIteration) {

        /* get payment ids for current iteration*/
        $currentPaymentIds = $installer->getConnection()->fetchCol("
            SELECT entity_id
            FROM {$this->getTable('sales_order_entity_varchar')}
            WHERE attribute_id = {$attributesIds['method']} and value in ({$methodIds})
            LIMIT {$i}, {$processingItemsCountForOneIteration};
        ");

        if (!count($currentPaymentIds)) {
            continue;
        }

        $currentPaymentIdsCondition = implode(',', $currentPaymentIds);

        /* get data for current payment ids*/
        $data = $installer->getConnection()->fetchAll("
            SELECT
                e.entity_id,
                ev_additional_data.value as additional_data
            FROM {$this->getTable('sales_order_entity')} as e
            LEFT JOIN {$this->getTable('sales_order_entity_text')} as ev_additional_data on (ev_additional_data.entity_id = e.entity_id and ev_additional_data.attribute_id = {$attributesIds['additional_data']})
            WHERE e.entity_id in ({$currentPaymentIdsCondition})
        ");

        /* prepare query data items */
        $insertQueryItems = array();
        foreach ($data as $item) {
            if ($item['additional_data'] != '') {
                $additionalInformationFields = array();
                $additionalInformationFields['paypal_payer_email'] = $item['additional_data'];
                $additionalInformation = serialize($additionalInformationFields);

                $insertQueryItems[] = array(
                    $entityTypeId,
                    $attributesIds['additional_information'],
                    $item['entity_id'],
                    $additionalInformation
                );
            }
        }

        if (!count($insertQueryItems)) {
            continue;
        }

        $connection->insertArray(
            $this->getTable('sales_order_entity_text'),
            array('entity_type_id', 'attribute_id', 'entity_id', 'value'),
            $insertQueryItems
        );
    }

} catch (Exception $e) {
    $connection->rollBack();
    throw $e;
}
$connection->commit();

$installer->endSetup();
