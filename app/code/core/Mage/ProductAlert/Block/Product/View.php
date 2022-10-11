<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product view price and stock alerts
 * @method $this setSignupUrl(string $value)
 */
class Mage_ProductAlert_Block_Product_View extends Mage_Core_Block_Template
{
    /**
     * Current product instance
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Helper instance
     *
     * @var Mage_ProductAlert_Helper_Data
     */
    protected $_helper = null;

    /**
     * Check whether the stock alert data can be shown and prepare related data
     */
    public function prepareStockAlertData()
    {
        if (!$this->_getHelper()->isStockAlertAllowed() || !$this->_product || $this->_product->isSalable()) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl($this->_getHelper()->getSaveUrl('stock'));
    }

    /**
     * Check whether the price alert data can be shown and prepare related data
     */
    public function preparePriceAlertData()
    {
        if (!$this->_getHelper()->isPriceAlertAllowed()
            || !$this->_product || $this->_product->getCanShowPrice() === false
        ) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl($this->_getHelper()->getSaveUrl('price'));
    }

    /**
     * Get current product instance
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $product = Mage::registry('current_product');
        if ($product && $product->getId()) {
            $this->_product = $product;
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve helper instance
     *
     * @return Mage_ProductAlert_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('productalert');
        }
        return $this->_helper;
    }
}
