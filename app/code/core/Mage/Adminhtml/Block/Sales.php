<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales page content block
 *
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
