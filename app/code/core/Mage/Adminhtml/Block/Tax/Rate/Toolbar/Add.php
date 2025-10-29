<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Admin tax class product toolbar
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rate_Toolbar_Add extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tax/toolbar/rate/add.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'addButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('tax')->__('Add New Tax Rate'),
                    'onclick' => "window.location.href='" . $this->getUrl('*/tax_rate/add') . "'",
                    'class' => 'add',
                ]),
        );
        return parent::_prepareLayout();
    }
}
