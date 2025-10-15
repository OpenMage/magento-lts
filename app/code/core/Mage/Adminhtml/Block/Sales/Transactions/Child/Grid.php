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
class Mage_Adminhtml_Block_Sales_Transactions_Child_Grid extends Mage_Adminhtml_Block_Sales_Transactions_Grid
{
    /**
     * Columns, that should be removed from grid
     *
     * @var array
     */
    protected $_columnsToRemove = ['parent_id', 'parent_txn_id'];

    /**
     * Disable pager and filter
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('transactionChildGrid');
        $this->setDefaultSort('created_at');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Add filter by parent transaction ID
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_payment_transaction_collection');
        $collection->addParentIdFilter(Mage::registry('current_transaction')->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Remove some columns and make other not sortable
     *
     */
    protected function _prepareColumns()
    {
        $result = parent::_prepareColumns();

        foreach (array_keys($this->_columns) as $key) {
            if (in_array($key, $this->_columnsToRemove)) {
                unset($this->_columns[$key]);
            } else {
                $this->_columns[$key]->setData('sortable', false);
            }
        }

        return $result;
    }
}
