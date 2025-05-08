<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml order creditmemo totals block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals extends Mage_Adminhtml_Block_Sales_Totals
{
    protected $_creditmemo;

    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if ($this->hasData('creditmemo')) {
                $this->_creditmemo = $this->_getData('creditmemo');
            } elseif (Mage::registry('current_creditmemo')) {
                $this->_creditmemo = Mage::registry('current_creditmemo');
            } elseif ($this->getParentBlock() && $this->getParentBlock()->getCreditmemo()) {
                $this->_creditmemo = $this->getParentBlock()->getCreditmemo();
            }
        }
        return $this->_creditmemo;
    }

    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Initialize creditmemo totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->addTotal(new Varien_Object([
            'code'      => 'adjustment_positive',
            'value'     => $this->getSource()->getAdjustmentPositive(),
            'base_value' => $this->getSource()->getBaseAdjustmentPositive(),
            'label'     => $this->helper('sales')->__('Adjustment Refund'),
        ]));
        $this->addTotal(new Varien_Object([
            'code'      => 'adjustment_negative',
            'value'     => $this->getSource()->getAdjustmentNegative(),
            'base_value' => $this->getSource()->getBaseAdjustmentNegative(),
            'label'     => $this->helper('sales')->__('Adjustment Fee'),
        ]));
        return $this;
    }
}
