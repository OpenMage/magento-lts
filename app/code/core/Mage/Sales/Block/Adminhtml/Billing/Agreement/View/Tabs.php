<?php
/**
 * Adminhtml billing agreements tabs view
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('billing_agreement_view_tabs');
        $this->setDestElementId('billing_agreement_view');
        $this->setTitle($this->__('Billing Agreement View'));
    }
}
