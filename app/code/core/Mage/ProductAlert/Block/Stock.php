<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * @package    Mage_ProductAlert
 * @deprecated after 1.4.1.0
 * @see Mage_ProductAlert_Block_Product_View
 */
class Mage_ProductAlert_Block_Stock extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productalert/stock.phtml');
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        if (!Mage::getStoreConfig('catalog/productalert/allow_stock')) {
            return false;
        }

        if (!$product = Mage::helper('productalert')->getProduct()) {
            return false;
        }

        /** @var Mage_Catalog_Model_Product $product */

        return !$product->isSaleable();
    }

    /**
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return Mage::helper('productalert')->getSaveUrl('stock');
    }
}
