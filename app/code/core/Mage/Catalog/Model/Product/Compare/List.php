<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Compare List Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Compare_List extends Varien_Object
{
    /**
     * Add product to Compare List
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function addProduct($product)
    {
        /** @var Mage_Catalog_Model_Product_Compare_Item $item */
        $item = Mage::getModel('catalog/product_compare_item');
        $this->_addVisitorToItem($item);
        $item->loadByProduct($product);

        if (!$item->getId()) {
            $item->addProductData($product);
            $item->save();
        }

        return $this;
    }

    /**
     * Add products to compare list
     *
     * @param array $productIds
     * @return $this
     */
    public function addProducts($productIds)
    {
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
                $this->addProduct($productId);
            }
        }
        return $this;
    }

    /**
     * Retrieve Compare Items Collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Compare_Item_Collection
     */
    public function getItemCollection()
    {
        return Mage::getResourceModel('catalog/product_compare_item_collection');
    }

    /**
     * Remove product from compare list
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function removeProduct($product)
    {
        /** @var Mage_Catalog_Model_Product_Compare_Item $item */
        $item = Mage::getModel('catalog/product_compare_item');
        $this->_addVisitorToItem($item);
        $item->loadByProduct($product);

        if ($item->getId()) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Add visitor and customer data to compare item
     *
     * @param Mage_Catalog_Model_Product_Compare_Item $item
     * @return $this
     */
    protected function _addVisitorToItem($item)
    {
        $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
        }

        return $this;
    }

    /**
     * Check has compare items by visitor/customer
     *
     * @param int $customerId
     * @param int $visitorId
     * @return bool
     */
    public function hasItems($customerId, $visitorId)
    {
        return Mage::getResourceSingleton('catalog/product_compare_item')
            ->getCount($customerId, $visitorId);
    }
}
