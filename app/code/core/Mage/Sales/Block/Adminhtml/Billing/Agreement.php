<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Adminhtml billing agreement grid container
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize billing agreements grid container
     *
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_billing_agreement';
        $this->_blockGroup = 'sales';
        $this->_headerText = Mage::helper('sales')->__('Billing Agreements');
        parent::__construct();
        $this->_removeButton('add');
    }
}
