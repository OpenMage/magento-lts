<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml shipping report page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Sales_Shipping extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_sales_shipping';
        $this->_headerText = Mage::helper('reports')->__('Total Shipped Report');
        parent::__construct();
        $this->setTemplate('report/grid/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', [
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ]);
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/shipping', ['_current' => true]);
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
