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
class Mage_Adminhtml_Block_Promo_Catalog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_addButton('apply_rules', [
            'label'     => Mage::helper('catalogrule')->__('Apply Rules'),
            'onclick'   => "location.href='" . $this->getUrl('*/*/applyRules') . "'",
            'class'     => '',
        ]);

        $this->_controller = 'promo_catalog';
        $this->_headerText = Mage::helper('catalogrule')->__('Catalog Price Rules');
        $this->_addButtonLabel = Mage::helper('catalogrule')->__('Add New Rule');
        parent::__construct();
    }
}
