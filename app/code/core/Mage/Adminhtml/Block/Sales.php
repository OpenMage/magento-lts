<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales page content block
 *
 * @category   Mage
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
