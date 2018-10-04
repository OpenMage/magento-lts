<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order status history collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Status_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('sales/order_status');
    }

    /**
     * Get collection data as options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('status', 'label');
    }

    /**
     * Get collection data as options hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('status', 'label');
    }

    /**
     * Join order states table
     */
    public function joinStates()
    {
        if (!$this->getFlag('states_joined')) {
            $this->_idFieldName = 'status_state';
            $this->getSelect()->joinLeft(
                array('state_table' => $this->getTable('sales/order_status_state')),
                'main_table.status=state_table.status',
                array('state', 'is_default')
            );
            $this->setFlag('states_joined', true);
        }
        return $this;
    }

    /**
     * add state code filter to collection
     *
     * @param string $state
     * @return Mage_Sales_Model_Resource_Order_Status_Collection
     */
    public function addStateFilter($state)
    {
        $this->joinStates();
        $this->getSelect()->where('state_table.state=?', $state);
        return $this;
    }

    /**
     * add status code filter to collection
     *
     * @param string $status
     * @return Mage_Sales_Model_Resource_Order_Status_Collection
     */
    public function addStatusFilter($status)
    {
        $this->joinStates();
        $this->getSelect()->where('state_table.status=?', $status);
        return $this;
    }

    /**
     * Define label order
     *
     * @param string $dir
     * @return Mage_Sales_Model_Mysql4_Order_Status_Collection
     */
    public function orderByLabel($dir = 'ASC')
    {
        $this->getSelect()->order('main_table.label '.$dir);
        return $this;
    }
}
