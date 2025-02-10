<?php
/**
 * Adminhtml most viewed products report content block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Product_Viewed extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_product_viewed';
        $this->_headerText = Mage::helper('reports')->__('Most Viewed');
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
        return $this->getUrl('*/*/viewed', ['_current' => true]);
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
