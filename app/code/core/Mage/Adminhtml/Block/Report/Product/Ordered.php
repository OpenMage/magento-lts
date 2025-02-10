<?php
/**
 * Adminhtml bestsellers products report content block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 * @deprecated after 1.4.0.1
 */
class Mage_Adminhtml_Block_Report_Product_Ordered extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_product_ordered';
        $this->_headerText = Mage::helper('reports')->__('Bestsellers');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
