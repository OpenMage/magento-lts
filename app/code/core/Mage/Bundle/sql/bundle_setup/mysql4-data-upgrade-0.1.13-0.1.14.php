<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$priceTypeAttribute = $installer->getAttribute('catalog_product', 'price_type');
$taxClassAttribute  = $installer->getAttribute('catalog_product', 'tax_class_id');

$productTable   = $installer->getTable('catalog/product');
$priceTypeTable = $productTable . '_' . $priceTypeAttribute['backend_type'];
$taxClassTable  = $productTable . '_' . $taxClassAttribute['backend_type'];

$db = $installer->getConnection();
$db->beginTransaction();
try {
    // select bundle product ids with dynamic price
    $select = $db->select()
        ->from(['attr' => $priceTypeTable], 'attr.entity_id')
        ->joinLeft(['e' => $productTable], 'attr.entity_id = e.entity_id', '')
        ->where('attr.attribute_id = ?', $priceTypeAttribute['attribute_id'])
        ->where('e.type_id = ?', 'bundle')
        ->where('attr.value = ?', 0);

    $isDataChanged = false;
    $stmt = $db->query($select);
    // set "None" tax class attribute for bundles with dynamic price
    while ($row = $stmt->fetch()) {
        $data  = ['value' => 0];
        $where = [
            'attribute_id = ?' => $taxClassAttribute['attribute_id'],
            'entity_id = ?'    => $row['entity_id'],
        ];
        $count = $db->update($taxClassTable, $data, $where);
        if ($count > 0) {
            $isDataChanged = true;
        }
    }

    // set "Require Reindex" status for some indexes if attributes data has been modified
    if ($isDataChanged) {
        $indexerCodes = [
            'catalog_product_attribute',
            'catalog_product_price',
            'catalog_product_flat',
        ];

        $indexer = Mage::getModel('index/process');
        foreach ($indexerCodes as $code) {
            $indexer->load($code, 'indexer_code')
                    ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    throw $e;
}
