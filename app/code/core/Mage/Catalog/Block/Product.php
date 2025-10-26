<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product extends Mage_Core_Block_Template
{
    protected $_finalPrice = [];

    /**
     * @return mixed
     */
    public function getProduct()
    {
        if (!$this->getData('product') instanceof Mage_Catalog_Model_Product) {
            if ($this->getData('product')->getProductId()) {
                $productId = $this->getData('product')->getProductId();
            }

            if ($productId) {
                $product = Mage::getModel('catalog/product')->load($productId);
                if ($product) {
                    $this->setProduct($product);
                }
            }
        }

        return $this->getData('product');
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * @return mixed
     */
    public function getFinalPrice()
    {
        if (!isset($this->_finalPrice[$this->getProduct()->getId()])) {
            $this->_finalPrice[$this->getProduct()->getId()] = $this->getProduct()->getFinalPrice();
        }

        return $this->_finalPrice[$this->getProduct()->getId()];
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getPriceHtml($product)
    {
        $this->setTemplate('catalog/product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
}
