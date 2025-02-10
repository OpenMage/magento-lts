<?php
/**
 * Adminhtml report filter form
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Refresh_Statistics extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_refresh_statistics';
        $this->_headerText = Mage::helper('reports')->__('Refresh Statistics');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
