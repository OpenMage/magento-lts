<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Shopping cart customers report page content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Shopcart_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_shopcart_customer';
        $this->_headerText = Mage::helper('reports')->__('Customers');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
