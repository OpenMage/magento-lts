<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Currency edit tabs
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Currency_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('currency_edit_tabs');
        $this->setDestElementId('currency_edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Currency'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', [
            'label'     => Mage::helper('adminhtml')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/system_currency_edit_tab_main')->toHtml(),
            'active'    => true,
        ]);

        $this->addTab('currency_rates', [
            'label'     => Mage::helper('adminhtml')->__('Rates'),
            'content'   => $this->getLayout()->createBlock('adminhtml/system_currency_edit_tab_rates')->toHtml(),
        ]);

        return parent::_beforeToHtml();
    }
}
