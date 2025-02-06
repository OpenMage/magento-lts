<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tax rule Edit Container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Checkout_Agreement_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'checkout_agreement';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('checkout')->__('Save Condition'));
        $this->_updateButton('delete', 'label', Mage::helper('checkout')->__('Delete Condition'));
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('checkout_agreement')->getId()) {
            return Mage::helper('checkout')->__('Edit Terms and Conditions');
        }
        return Mage::helper('checkout')->__('New Terms and Conditions');
    }
}
