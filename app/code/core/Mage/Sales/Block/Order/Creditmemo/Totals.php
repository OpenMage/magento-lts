<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Creditmemo_Totals extends Mage_Sales_Block_Order_Totals
{
    protected $_creditmemo = null;

    /**
     * @return null|mixed
     */
    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if ($this->hasData('creditmemo')) {
                $this->_creditmemo = $this->_getData('creditmemo');
            } elseif (Mage::registry('current_creditmemo')) {
                $this->_creditmemo = Mage::registry('current_creditmemo');
            } elseif ($this->getParentBlock()->getCreditmemo()) {
                $this->_creditmemo = $this->getParentBlock()->getCreditmemo();
            }
        }

        return $this->_creditmemo;
    }

    /**
     * @param  Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return $this
     */
    public function setCreditmemo($creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->removeTotal('base_grandtotal');
        if ((float) $this->getSource()->getAdjustmentPositive()) {
            $total = new Varien_Object([
                'code'  => 'adjustment_positive',
                'value' => $this->getSource()->getAdjustmentPositive(),
                'label' => $this->__('Adjustment Refund'),
            ]);
            $this->addTotal($total);
        }

        if ((float) $this->getSource()->getAdjustmentNegative()) {
            $total = new Varien_Object([
                'code'  => 'adjustment_negative',
                'value' => $this->getSource()->getAdjustmentNegative(),
                'label' => $this->__('Adjustment Fee'),
            ]);
            $this->addTotal($total);
        }

        /**
        <?php if ($this->getCanDisplayTotalPaid()): ?>
        <tr>
            <td colspan="6" class="a-right"><strong><?php echo $this->__('Total Paid') ?></strong></td>
            <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalPaid()) ?></strong></td>
        </tr>
        <?php endif ?>
        <?php if ($this->getCanDisplayTotalRefunded()): ?>
        <tr>
            <td colspan="6" class="a-right"><strong><?php echo $this->__('Total Refunded') ?></strong></td>
            <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalRefunded()) ?></strong></td>
        </tr>
        <?php endif ?>
        <?php if ($this->getCanDisplayTotalDue()): ?>
        <tr>
            <td colspan="6" class="a-right"><strong><?php echo $this->__('Total Due') ?></strong></td>
            <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalDue()) ?></strong></td>
        </tr>
        <?php endif ?>
         */
        return $this;
    }
}
