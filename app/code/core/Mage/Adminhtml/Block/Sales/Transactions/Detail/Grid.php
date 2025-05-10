<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml transaction details grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Transactions_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize default sorting and html ID
     */
    protected function _construct()
    {
        $this->setId('transactionDetailsGrid');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();
        foreach ($this->getTransactionAdditionalInfo() as $key => $value) {
            $data = new Varien_Object(['key' => $key, 'value' => $value]);
            $collection->addItem($data);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('key', [
            'header'    => Mage::helper('sales')->__('Key'),
            'index'     => 'key',
            'sortable'  => false,
            'type'      => 'text',
            'width'     => '50%',
        ]);

        $this->addColumn('value', [
            'header'    => Mage::helper('sales')->__('Value'),
            'index'     => 'value',
            'sortable'  => false,
            'type'      => 'text',
            'escape'    => true,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Transaction additional info
     *
     * @return array
     */
    public function getTransactionAdditionalInfo()
    {
        $info = Mage::registry('current_transaction')->getAdditionalInformation(
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
        );
        return (is_array($info)) ? $info : [];
    }
}
