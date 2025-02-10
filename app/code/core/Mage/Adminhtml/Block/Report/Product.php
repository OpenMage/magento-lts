<?php
/**
 * Adminhtml products report page content block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_product';
        $this->_headerText = Mage::helper('reports')->__('Products Report');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
