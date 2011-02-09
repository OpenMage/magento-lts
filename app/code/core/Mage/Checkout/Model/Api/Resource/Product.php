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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ${CLASS_DESCRIPTION}
 */
class Mage_Checkout_Model_Api_Resource_Product extends Mage_Checkout_Model_Api_Resource
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

    /**
     * Return loaded product instance
     *
     * @param  int|string $productId (SKU or ID)
     * @param  int|string $store
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct($productId, $store = null, $identifierType = null)
    {
        $loadByIdOnFalse = false;
        if ($identifierType == null) {
            $identifierType = 'sku';
            $loadByIdOnFalse = true;
        }
        $product = Mage::getModel('catalog/product');
        if ($store !== null) {
            $product->setStoreId($this->_getStoreId($store));
        }
        /* @var $product Mage_Catalog_Model_Product */
        if ($identifierType == 'sku') {
            $idBySku = $product->getIdBySku($productId);
            if ($idBySku) {
                $productId = $idBySku;
            }
            if ($idBySku || $loadByIdOnFalse) {
                $product->load($productId);
            }
        } elseif ($identifierType == 'id') {
            $product->load($productId);
        }
        return $product;
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object();
            $request->setQty($requestInfo);
        } else {
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }
        return $request;
    }
}
