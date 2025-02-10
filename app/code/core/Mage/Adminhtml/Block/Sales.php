<?php
/**
 * Adminhtml sales page content block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/index.phtml');
    }

    public function _beforeToHtml()
    {
        $this->assign('createUrl', $this->getUrl('*/sales/new'));
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/sales_grid', 'sales.grid'));
        return parent::_beforeToHtml();
    }
}
