<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Adminhtml billing agreement related orders tab
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Tab_Orders extends Mage_Adminhtml_Block_Sales_Order_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Initialize grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('billing_agreement_orders');
    }

    /**
     * Prepare related orders collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_grid_collection');
        $collection->addBillingAgreementsFilter(Mage::registry('current_billing_agreement')->getId());
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Related Orders');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Related Orders');
    }

    /**
     * Can show tab in tabs
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ordersGrid', ['_current' => true]);
    }

    /**
     * Remove import/export field from grid
     *
     * @return bool
     */
    public function getExportTypes()
    {
        return false;
    }

    /**
     * Disable massaction in grid
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }
}
