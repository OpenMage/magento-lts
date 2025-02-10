<?php
/**
 * Adminhtml catalog product composite configure block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Configure extends Mage_Adminhtml_Block_Widget
{
    protected $_product;

    /**
     * Set template
     */
    protected function _construct()
    {
        $this->setTemplate('catalog/product/composite/configure.phtml');
    }

    /**
     * Retrieve product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (Mage::registry('current_product')) {
                $this->_product = Mage::registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('catalog/product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @return $this
     */
    public function setProduct(?Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }
}
