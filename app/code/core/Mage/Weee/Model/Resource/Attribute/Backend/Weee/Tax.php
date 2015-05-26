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
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product WEEE tax backend attribute model
 *
 * @category    Mage
 * @package     Mage_Weee
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Weee_Model_Resource_Attribute_Backend_Weee_Tax extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Defines main resource table and table identifier field
     *
     */
    protected function _construct()
    {
        $this->_init('weee/tax', 'value_id');
    }

    /**
     * Load product data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return array
     */
    public function loadProductData($product, $attribute)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array(
                'website_id',
                'country',
                'state',
                'value'
            ))
            ->where('entity_id = ?', (int)$product->getId())
            ->where('attribute_id = ?', (int)$attribute->getId());
        if ($attribute->isScopeGlobal()) {
            $select->where('website_id = ?', 0);
        } else {
            $storeId = $product->getStoreId();
            if ($storeId) {
                $select->where('website_id IN (?)', array(0, Mage::app()->getStore($storeId)->getWebsiteId()));
            }
        }
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Delete product data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Weee_Model_Resource_Attribute_Backend_Weee_Tax
     */
    public function deleteProductData($product, $attribute)
    {
        $where = array(
            'entity_id = ?'    => (int)$product->getId(),
            'attribute_id = ?' => (int)$attribute->getId()
        );

        $adapter   = $this->_getWriteAdapter();
        if (!$attribute->isScopeGlobal()) {
            $storeId = $product->getStoreId();
            if ($storeId) {
                $where['website_id IN(?)'] =  array(0, Mage::app()->getStore($storeId)->getWebsiteId());
            }
        }
        $adapter->delete($this->getMainTable(), $where);
        return $this;
    }

    /**
     * Insert product data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return Mage_Weee_Model_Resource_Attribute_Backend_Weee_Tax
     */
    public function insertProductData($product, $data)
    {
        $data['entity_id']      = (int)$product->getId();
        $data['entity_type_id'] = (int)$product->getEntityTypeId();

        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }
}

