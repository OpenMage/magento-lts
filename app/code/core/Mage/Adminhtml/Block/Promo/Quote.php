<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Catalog price rules
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'promo_quote';
        $this->_headerText = Mage::helper('salesrule')->__('Shopping Cart Price Rules');
        $this->_addButtonLabel = Mage::helper('salesrule')->__('Add New Rule');
        parent::__construct();
    }
}
