<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml new accounts report page content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Customer_Accounts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_customer_accounts';
        $this->_headerText = Mage::helper('reports')->__('New Accounts');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
