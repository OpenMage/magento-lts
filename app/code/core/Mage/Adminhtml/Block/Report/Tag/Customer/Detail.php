<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tags detail for customer report blocks content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Tag_Customer_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_tag_customer_detail';
        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
        $customerName = $this->escapeHtml($customer->getName());
        $this->_headerText = Mage::helper('reports')->__('Tags Submitted by %s', $customerName);
        parent::__construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/customer/'));
        $this->_addBackButton();
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
