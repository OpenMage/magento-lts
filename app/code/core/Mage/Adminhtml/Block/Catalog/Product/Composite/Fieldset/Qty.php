<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml block for fieldset of product custom options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Qty extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setIsLastFieldset(true);
    }

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * Return selected qty
     *
     * @return int
     */
    public function getQtyValue()
    {
        $qty = $this->getProduct()->getPreconfiguredValues()->getQty();
        if (!$qty) {
            $qty = 1;
        }
        return $qty;
    }
}
