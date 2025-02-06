<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml order totals block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Totals //Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->_totals['paid'] = new Varien_Object([
            'code'      => 'paid',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalPaid(),
            'base_value' => $this->getSource()->getBaseTotalPaid(),
            'label'     => $this->helper('sales')->__('Total Paid'),
            'area'      => 'footer',
        ]);
        $this->_totals['refunded'] = new Varien_Object([
            'code'      => 'refunded',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalRefunded(),
            'base_value' => $this->getSource()->getBaseTotalRefunded(),
            'label'     => $this->helper('sales')->__('Total Refunded'),
            'area'      => 'footer',
        ]);
        $this->_totals['due'] = new Varien_Object([
            'code'      => 'due',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalDue(),
            'base_value' => $this->getSource()->getBaseTotalDue(),
            'label'     => $this->helper('sales')->__('Total Due'),
            'area'      => 'footer',
        ]);
        return $this;
    }
}
