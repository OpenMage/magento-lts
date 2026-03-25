<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Adminhtml billing agreement view plane
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Form extends Mage_Adminhtml_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/billing/agreement/view/form.phtml');
    }
}
