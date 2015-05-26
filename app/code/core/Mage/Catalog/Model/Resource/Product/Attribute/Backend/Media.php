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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product media gallery attribute backend resource
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media extends Mage_Core_Model_Resource_Db_Abstract
{
    const GALLERY_TABLE       = 'catalog/product_attribute_media_gallery';
    const GALLERY_VALUE_TABLE = 'catalog/product_attribute_media_gallery_value';
    const GALLERY_IMAGE_TABLE = 'catalog/product_attribute_media_gallery_image';

    protected $_eventPrefix = 'catalog_product_attribute_backend_media';

    private $_attributeId = null;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(self::GALLERY_TABLE, 'value_id');
    }

    /**
     * Load gallery images for product using reusable select method
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Product_Attribute_Backend_Media $object
     * @return array
     */
    public function loadGallery($product, $object)
    {
        $eventObjectWrapper = new Varien_Object(
            array(
                'product' => $product,
                'backend_attribute' => $object
            )
        );
        Mage::dispatchEvent(
            $this->_eventPrefix . '_load_gallery_before',
            array('event_object_wrapper' => $eventObjectWrapper)
        );

        if ($eventObjectWrapper->hasProductIdsOverride()) {
            $productIds = $eventObjectWrapper->getProductIdsOverride();
        } else {
            $productIds = array($product->getId());
        }

        $select = $this->_getLoadGallerySelect($productIds, $product->getStoreId(), $object->getAttribute()->getId());

        $adapter = $this->_getReadAdapter();
        $result = $adapter->fetchAll($select);
        $this->_removeDuplicates($result);
        return $result;
    }

    /**
     * Remove duplicates
     *
     * @param array $result
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media
     */
    protected function _removeDuplicates(&$result)
    {
        $fileToId = array();

        foreach (array_keys($result) as $index) {
            if (!isset($fileToId[$result[$index]['file']])) {
                $fileToId[$result[$index]['file']] = $result[$index]['value_id'];
            } elseif ($fileToId[$result[$index]['file']] != $result[$index]['value_id']) {
                $this->deleteGallery($result[$index]['value_id']);
                unset($result[$index]);
            }
        }

        $result = array_values($result);
        return $this;
    }

    /**
     * Insert gallery value to db and retrive last id
     *
     * @param array $data
     * @return interger
     */
    public function insertGallery($data)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = $this->_prepareDataForTable(new Varien_Object($data), $this->getMainTable());
        $adapter->insert($this->getMainTable(), $data);

        return $adapter->lastInsertId($this->getMainTable());
    }

    /**
     * Delete gallery value in db
     *
     * @param array|integer $valueId
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media
     */
    public function deleteGallery($valueId)
    {
        if (is_array($valueId) && count($valueId)>0) {
            $condition = $this->_getWriteAdapter()->quoteInto('value_id IN(?) ', $valueId);
        } elseif (!is_array($valueId)) {
            $condition = $this->_getWriteAdapter()->quoteInto('value_id = ? ', $valueId);
        } else {
            return $this;
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    /**
     * Insert gallery value for store to db
     *
     * @param array $data
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media
     */
    public function insertGalleryValueInStore($data)
    {
        $data = $this->_prepareDataForTable(new Varien_Object($data), $this->getTable(self::GALLERY_VALUE_TABLE));
        $this->_getWriteAdapter()->insert($this->getTable(self::GALLERY_VALUE_TABLE), $data);

        return $this;
    }

    /**
     * Delete gallery value for store in db
     *
     * @param integer $valueId
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media
     */
    public function deleteGalleryValueInStore($valueId, $storeId)
    {
        $adapter = $this->_getWriteAdapter();

        $conditions = implode(' AND ', array(
            $adapter->quoteInto('value_id = ?', (int) $valueId),
            $adapter->quoteInto('store_id = ?', (int) $storeId),
        ));

        $adapter->delete($this->getTable(self::GALLERY_VALUE_TABLE), $conditions);

        return $this;
    }

    /**
     * Duplicates gallery db values
     *
     * @param Mage_Catalog_Model_Product_Attribute_Backend_Media $object
     * @param array $newFiles
     * @param int $originalProductId
     * @param int $newProductId
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media
     */
    public function duplicate($object, $newFiles, $originalProductId, $newProductId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('value_id', 'value'))
            ->where('attribute_id = ?', $object->getAttribute()->getId())
            ->where('entity_id = ?', $originalProductId);

        $valueIdMap = array();
        // Duplicate main entries of gallery
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $data = array(
                'attribute_id' => $object->getAttribute()->getId(),
                'entity_id'    => $newProductId,
                'value'        => (isset($newFiles[$row['value_id']]) ? $newFiles[$row['value_id']] : $row['value'])
            );

            $valueIdMap[$row['value_id']] = $this->insertGallery($data);
        }

        if (count($valueIdMap) == 0) {
            return $this;
        }

        // Duplicate per store gallery values
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable(self::GALLERY_VALUE_TABLE))
            ->where('value_id IN(?)', array_keys($valueIdMap));

        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $row['value_id'] = $valueIdMap[$row['value_id']];
            $this->insertGalleryValueInStore($row);
        }

        return $this;
    }

    /**
     * Get select to retrieve media gallery images
     * for given product IDs.
     *
     * @param array $productIds
     * @param $storeId
     * @param int $attributeId
     * @return Varien_Db_Select
     */
    protected function _getLoadGallerySelect(array $productIds, $storeId, $attributeId) {
        $adapter = $this->_getReadAdapter();

        $positionCheckSql = $adapter->getCheckSql('value.position IS NULL', 'default_value.position', 'value.position');

        // Select gallery images for product
        $select = $adapter->select()
            ->from(
                array('main'=>$this->getMainTable()),
                array('value_id', 'value AS file', 'product_id' => 'entity_id')
            )
            ->joinLeft(
                array('value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                $adapter->quoteInto('main.value_id = value.value_id AND value.store_id = ?', (int)$storeId),
                array('label','position','disabled')
            )
            ->joinLeft( // Joining default values
                array('default_value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                'main.value_id = default_value.value_id AND default_value.store_id = 0',
                array(
                    'label_default' => 'label',
                    'position_default' => 'position',
                    'disabled_default' => 'disabled'
                )
            )
            ->where('main.attribute_id = ?', $attributeId)
            ->where('main.entity_id in (?)', $productIds)
            ->order($positionCheckSql . ' ' . Varien_Db_Select::SQL_ASC);

        return $select;
    }

    /**
     * Get attribute ID
     *
     * @return int
     */
    protected function _getAttributeId() {
        if(is_null($this->_attributeId)) {
            $attribute = Mage::getModel('eav/entity_attribute')
                ->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'media_gallery');

            $this->_attributeId = $attribute->getId();
        }
        return $this->_attributeId;
    }

    /**
     * Get media gallery set for given product IDs
     *
     * @param array $productIds
     * @param $storeId
     * @return array
     */
    public function loadGallerySet(array $productIds, $storeId) {
        $select = $this->_getLoadGallerySelect($productIds, $storeId, $this->_getAttributeId());

        $adapter = $this->_getReadAdapter();
        $result = $adapter->fetchAll($select);
        $this->_removeDuplicates($result);
        return $result;
    }
}
