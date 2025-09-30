<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml invoiced report page content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Sales_Invoiced extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_sales_invoiced';
        $this->_headerText = Mage::helper('reports')->__('Total Invoiced vs. Paid Report');
        parent::__construct();
        $this->setTemplate('report/grid/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', [
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()',
        ]);
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/invoiced', ['_current' => true]);
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
