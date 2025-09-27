<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Default Total Row Renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default extends Mage_Adminhtml_Block_Sales_Order_Create_Totals
{
    protected $_template = 'sales/order/create/totals/default.phtml';

    protected function _construct()
    {
        $this->setTemplate($this->_template);
    }

    /**
     * Retrieve quote session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve store model object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
    }

    public function formatPrice($value)
    {
        return $this->getStore()->formatPrice($value);
    }
}
