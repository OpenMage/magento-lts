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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product tier price backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice
    extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_attribute_tier_price', 'value_id');
    }

    /**
     * Load product tier prices
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return array
     */
    public function loadProductPrices($product, $attribute)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array(
                'website_id', 'all_groups', 'cust_group' => 'customer_group_id',
                'price_qty' => 'qty', 'price' => 'value'
            ))
            ->where('entity_id=?', $product->getId())
            ->order('qty');
        if ($attribute->isScopeGlobal()) {
            $select->where('website_id=?', 0);
        }
        else {
            if ($storeId = $product->getStoreId()) {
                $select->where('website_id IN (?)', array(0, Mage::app()->getStore($storeId)->getWebsiteId()));
            }
        }
        return $this->_getReadAdapter()->fetchAll($select);
    }

    public function deleteProductPrices($product, $attribute)
    {
        $condition = array();

        if (!$attribute->isScopeGlobal()) {
            if ($storeId = $product->getStoreId()) {
                $condition[] = $this->_getWriteAdapter()->quoteInto('website_id IN (?)', array(0, Mage::app()->getStore($storeId)->getWebsiteId()));
            }
        }

        $condition[] = $this->_getWriteAdapter()->quoteInto('entity_id=?', $product->getId());

        $this->_getWriteAdapter()->delete($this->getMainTable(), implode(' AND ', $condition));
        return $this;
    }

    public function insertProductPrice($product, $data)
    {
        $data['entity_id'] = $product->getId();
        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }
}
