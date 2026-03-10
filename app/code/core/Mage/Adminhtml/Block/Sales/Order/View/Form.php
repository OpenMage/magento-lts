<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order view plane
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View_Form extends Mage_Adminhtml_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/view/form.phtml');
    }
}
