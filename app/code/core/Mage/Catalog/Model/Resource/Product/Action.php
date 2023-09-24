<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Mass processing resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Action extends Mage_Catalog_Model_Resource_Abstract
{
    /**
     * Intialize connection
     *
     */
    protected function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType(Mage_Catalog_Model_Product::ENTITY)
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );
    }

    /**
     * Update attribute values for entity list per store
     *
     * @param array $entityIds
     * @param array $attrData
     * @param int $storeId
     * @return $this
     */
    public function updateAttributes($entityIds, $attrData, $storeId)
    {
        $this->_attributeValuesToSave   = [];
        $this->_attributeValuesToDelete = [];

        $object = new Varien_Object();
        $object->setIdFieldName('entity_id')
            ->setStoreId($storeId);

        $this->_getWriteAdapter()->beginTransaction();
        try {
            foreach ($attrData as $attrCode => $value) {
                $attribute = $this->getAttribute($attrCode);
                if (!$attribute->getAttributeId()) {
                    continue;
                }

                $i = 0;
                foreach ($entityIds as $entityId) {
                    $i++;
                    $object->setId($entityId);
                    // collect data for save
                    $this->_saveAttributeValue($object, $attribute, $value);
                    // save collected data every 1000 rows
                    if ($i % 1000 == 0) {
                        $this->_processAttributeValues();
                    }
                }
                $this->_processAttributeValues();
            }

            $this->_updateUpdatedAt($entityIds);
            $this->_getWriteAdapter()->commit();
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Update the "updated_at" field for all entity_ids passed
     *
     * @param array $entityIds
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _updateUpdatedAt(array $entityIds): void
    {
        $updatedAt = Varien_Date::now();
        $catalogProductTable = $this->getTable('catalog/product');
        $adapter = $this->_getWriteAdapter();

        $entityIdsChunks = array_chunk($entityIds, 1000);
        foreach ($entityIdsChunks as $entityIdsChunk) {
            $adapter->update($catalogProductTable, [
                'updated_at' => $updatedAt
            ], $adapter->quoteInto('entity_id IN (?)', $entityIdsChunk));
        }
    }
}
