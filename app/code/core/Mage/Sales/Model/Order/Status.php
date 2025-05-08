<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Class Mage_Sales_Model_Order_Status
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Status _getResource()
 * @method Mage_Sales_Model_Resource_Order_Status getResource()
 * @method Mage_Sales_Model_Resource_Order_Status_Collection getCollection()
 *
 * @method string getStatus()
 * @method string getLabel()
 * @method bool hasStoreLabels()
 */
class Mage_Sales_Model_Order_Status extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_status');
    }

    /**
     * Assign order status to particular state
     *
     * @param string  $state
     * @param bool $isDefault make the status as default one for state
     * @return $this
     */
    public function assignState($state, $isDefault = false)
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_getResource()->assignState($this->getStatus(), $state, $isDefault);
            $this->_getResource()->commit();
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Unassigns order status from particular state
     *
     * @param string  $state
     * @return $this
     */
    public function unassignState($state)
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_getResource()->unassignState($this->getStatus(), $state);
            $this->_getResource()->commit();
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Getter for status labels per store
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if ($this->hasData('store_labels')) {
            return $this->_getData('store_labels');
        }
        $labels = $this->_getResource()->getStoreLabels($this);
        $this->setData('store_labels', $labels);
        return $labels;
    }

    /**
     * Get status label by store
     *
     * @param mixed $store
     * @return string
     */
    public function getStoreLabel($store = null)
    {
        $store = Mage::app()->getStore($store);
        $label = false;
        if (!$store->isAdmin()) {
            $labels = $this->getStoreLabels();
            if (isset($labels[$store->getId()])) {
                return $labels[$store->getId()];
            }
        }
        return Mage::helper('sales')->__($this->getLabel());
    }

    /**
     * Load default status per state
     *
     * @param string $state
     * @return Mage_Sales_Model_Order_Status
     */
    public function loadDefaultByState($state)
    {
        $this->load($state, 'default_state');
        return $this;
    }
}
