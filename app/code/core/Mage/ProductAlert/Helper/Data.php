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
 * @package     Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ProductAlert data helper
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Helper_Data extends Mage_Core_Helper_Url
{
    /**
     * Current product instance (override registry one)
     *
     * @var null|Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Get current product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!is_null($this->_product)) {
            return $this->_product;
        }
        return Mage::registry('product');
    }

    /**
     * Set current product instance
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_ProductAlert_Helper_Data
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getCustomer()
    {
        return Mage::getSingleton('customer/session');
    }

    public function getStore()
    {
        return Mage::app()->getStore();
    }

    public function getSaveUrl($type)
    {
        return $this->_getUrl('productalert/add/' . $type, array(
            'product_id'    => $this->getProduct()->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        ));
    }

    public function createBlock($block)
    {
        $error = Mage::helper('core')->__('Invalid block type: %s', $block);
        if (is_string($block)) {
            if (strpos($block, '/') !== false) {
                if (!$block = Mage::getConfig()->getBlockClassName($block)) {
                    Mage::throwException($error);
                }
            }
            $fileName = mageFindClassFile($block);
            if ($fileName!==false) {
                include_once ($fileName);
                $block = new $block(array());
            }
        }
        if (!$block instanceof Mage_Core_Block_Abstract) {
            Mage::throwException($error);
        }
        return $block;
    }

    /**
     * Check whether stock alert is allowed
     *
     * @return bool
     */
    public function isStockAlertAllowed()
    {
        return Mage::getStoreConfigFlag(Mage_ProductAlert_Model_Observer::XML_PATH_STOCK_ALLOW);
    }

    /**
     * Check whether price alert is allowed
     *
     * @return bool
     */
    public function isPriceAlertAllowed()
    {
        return Mage::getStoreConfigFlag(Mage_ProductAlert_Model_Observer::XML_PATH_PRICE_ALLOW);
    }
}
