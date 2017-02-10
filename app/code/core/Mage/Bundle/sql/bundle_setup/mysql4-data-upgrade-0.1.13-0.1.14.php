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
 * @package     Mage_Bundle
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$priceTypeAttribute = $installer->getAttribute('catalog_product', 'price_type');
$taxClassAttribute  = $installer->getAttribute('catalog_product', 'tax_class_id');

$productTable   = $installer->getTable('catalog/product');
$priceTypeTable = $productTable ."_". $priceTypeAttribute['backend_type'];
$taxClassTable  = $productTable ."_". $taxClassAttribute['backend_type'];

$db = $installer->getConnection();
$db->beginTransaction();
try {
    // select bundle product ids with dynamic price
    $select = $db->select()
        ->from(array('attr' => $priceTypeTable), 'attr.entity_id')
        ->joinLeft(array('e' => $productTable), 'attr.entity_id = e.entity_id', '')
        ->where('attr.attribute_id = ?', $priceTypeAttribute['attribute_id'])
        ->where('e.type_id = ?', 'bundle')
        ->where('attr.value = ?', 0);

    $isDataChanged = false;
    $stmt = $db->query($select);
    // set "None" tax class attribute for bundles with dynamic price
    while ($row = $stmt->fetch()) {
        $data  = array('value' => 0);
        $where = array(
            'attribute_id = ?' => $taxClassAttribute['attribute_id'],
            'entity_id = ?'    => $row['entity_id']
        );
        $count = $db->update($taxClassTable, $data, $where);
        if ($count > 0) {
            $isDataChanged = true;
        }
    }

    // set "Require Reindex" status for some indexes if attributes data has been modified
    if ($isDataChanged) {
        $indexerCodes = array(
            'catalog_product_attribute',
            'catalog_product_price',
            'catalog_product_flat'
        );

        $indexer = Mage::getModel('index/process');
        foreach ($indexerCodes as $code) {
            $indexer->load($code, 'indexer_code')
                    ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }
    $db->commit();

} catch (Exception $e) {
    $db->rollback();
    throw $e;
}
