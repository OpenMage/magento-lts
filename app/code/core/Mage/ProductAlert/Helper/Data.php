<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * ProductAlert data helper
 *
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Helper_Data extends Mage_Core_Helper_Url
{
    protected $_moduleName = 'Mage_ProductAlert';

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
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * @param string $type
     * @return string
     */
    public function getSaveUrl($type)
    {
        return $this->_getUrl('productalert/add/' . $type, [
            'product_id'    => $this->getProduct()->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(),
        ]);
    }

    /**
     * @param string $block
     * @return Mage_ProductAlert_Block_Email_Price|Mage_ProductAlert_Block_Email_Stock
     * @throws Mage_Core_Exception
     */
    public function createBlock($block)
    {
        $error = Mage::helper('core')->__('Invalid block type: %s', $block);
        if (is_string($block)) {
            if (str_contains($block, '/')) {
                if (!$block = Mage::getConfig()->getBlockClassName($block)) {
                    Mage::throwException($error);
                }
            }

            $fileName = mageFindClassFile($block);
            if ($fileName !== false) {
                include_once($fileName);
                $block = new $block([]);
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
