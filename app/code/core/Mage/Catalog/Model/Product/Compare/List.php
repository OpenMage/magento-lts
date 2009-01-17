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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product compare list
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Compare_List extends Varien_Object
{
    public function addProduct($product)
    {
        $item = Mage::getModel('catalog/product_compare_item');
        $this->_addVisitorToItem($item);

        $item->loadByProduct($product);

        if (!$item->getId()) {
            $item->addProductData($product);
            $item->save();
        }
        return $this;
    }

    public function addProducts($productIds)
    {
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
                $this->addProduct($productId);
            }
        }
        return $this;
    }

    public function getItemCollection()
    {
        return Mage::getResourceModel('catalog/product_compare_item_collection');
    }

    public function removeProduct()
    {

    }

    protected function _addVisitorToItem($item)
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
        }
        else {
            $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
        }
        return $this;
    }

    public function hasItems($customerId, $visitorId)
    {
        return Mage::getResourceSingleton('catalog/product_compare_item')->getCount($customerId, $visitorId);
    }
}