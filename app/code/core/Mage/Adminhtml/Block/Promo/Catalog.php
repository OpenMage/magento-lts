<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog price rules
 *
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
