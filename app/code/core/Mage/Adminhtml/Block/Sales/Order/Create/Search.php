<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create search block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Search extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_search');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Please Select Products to Add');
    }

    /**
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => Mage::helper('sales')->__('Add Selected Product(s) to Order'),
            'onclick' => 'order.productGridAddSelected()',
            'class' => 'add',
        ];
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }
}
