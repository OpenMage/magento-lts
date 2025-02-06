<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Settlement reports transaction details
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Details extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Block construction
     * Initialize titles, buttons
     */
    public function __construct()
    {
        parent::__construct();
        $this->_controller = '';
        $this->_headerText = Mage::helper('paypal')->__('View Transaction Details');
        $this->_removeButton('reset')
            ->_removeButton('delete')
            ->_removeButton('save');
    }

    /**
     * Initialize form
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild('form', $this->getLayout()->createBlock('paypal/adminhtml_settlement_details_form'));
        return $this;
    }
}
