<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Admin rating left menu
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Rating_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('rating_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rating')->__('Rating Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', [
            'label'     => Mage::helper('rating')->__('Rating Information'),
            'title'     => Mage::helper('rating')->__('Rating Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/rating_edit_tab_form')->toHtml(),
        ])
        ;

        return parent::_beforeToHtml();
    }
}
