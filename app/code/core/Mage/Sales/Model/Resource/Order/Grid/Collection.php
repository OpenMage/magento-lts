<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order grid collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Grid_Collection extends Mage_Sales_Model_Resource_Order_Collection
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_grid_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_grid_collection';

    /**
     * @var bool
     */
    protected $_customerModeFlag = false;

    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales/order_grid');
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        if ($this->getIsCustomerMode()) {
            $this->_renderFilters();

            $unionSelect = clone $this->getSelect();

            $unionSelect->reset(Zend_Db_Select::ORDER);
            $unionSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $unionSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

            $countSelect = clone $this->getSelect();
            $countSelect->reset();
            $countSelect->from(['a' => $unionSelect], 'COUNT(*)');
        } else {
            $countSelect = parent::getSelectCountSql();
        }

        return $countSelect;
    }

    /**
     * Set customer mode flag value
     *
     * @param bool $value
     * @return $this
     */
    public function setIsCustomerMode($value)
    {
        $this->_customerModeFlag = (bool) $value;
        return $this;
    }

    /**
     * Get customer mode flag value
     *
     * @return bool
     */
    public function getIsCustomerMode()
    {
        return $this->_customerModeFlag;
    }
}
