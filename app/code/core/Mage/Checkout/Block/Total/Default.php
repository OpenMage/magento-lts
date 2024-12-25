<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default Total Row Renderer
 *
 * @category   Mage
 * @package    Mage_Checkout
 *
 * @method Mage_Sales_Model_Quote_Address_Total getTotal()
 */
class Mage_Checkout_Block_Total_Default extends Mage_Checkout_Block_Cart_Totals
{
    protected $_template = 'checkout/total/default.phtml';

    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    protected function _construct()
    {
        $this->setTemplate($this->_template);
        $this->_store = Mage::app()->getStore();
    }

    /**
     * Get style assigned to total object
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->getTotal()->getStyle();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address_Total $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->setData('total', $total);
        if ($total->getAddress()) {
            $this->_store = $total->getAddress()->getQuote()->getStore();
        }
        return $this;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_store;
    }
}
