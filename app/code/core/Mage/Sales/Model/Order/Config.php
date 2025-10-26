<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order configuration model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Config extends Mage_Core_Model_Config_Base
{
    /**
     * Statuses per state array
     *
     * @var array
     */
    protected $_stateStatuses;

    /**
     * Statuses array
     *
     * @var array
     */
    protected $_statuses;

    /**
     * States array
     *
     * @var array
     */
    private $_states;

    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global/sales/order'));
    }

    /**
     * @param string $status
     * @return Varien_Simplexml_Element
     */
    protected function _getStatus($status)
    {
        return $this->getNode('statuses/' . $status);
    }

    /**
     * @param string $state
     * @return Varien_Simplexml_Element
     */
    protected function _getState($state)
    {
        return $this->getNode('states/' . $state);
    }

    /**
     * Retrieve default status for state
     *
     * @param   string $state
     * @return  string
     */
    public function getStateDefaultStatus($state)
    {
        $status = false;
        $stateNode = $this->_getState($state);
        if ($stateNode) {
            $status = Mage::getModel('sales/order_status')
                ->loadDefaultByState($state);
            $status = $status->getStatus();
        }

        return $status;
    }

    /**
     * Retrieve status label
     *
     * @param   string $code
     * @return  string
     */
    public function getStatusLabel($code)
    {
        $key = $code . '/' . Mage::app()->getStore()->getStoreId();
        if (!isset($this->_statuses[$key])) {
            $status = Mage::getModel('sales/order_status')->load($code);
            $this->_statuses[$key] = $status->getStoreLabel();
        }

        return $this->_statuses[$key];
    }

    /**
     * State label getter
     *
     * @param   string $state
     * @return  string
     */
    public function getStateLabel($state)
    {
        $stateNode = $this->_getState($state);
        if ($stateNode) {
            $state = (string) $stateNode->label;
            return Mage::helper('sales')->__($state);
        }

        return $state;
    }

    /**
     * Retrieve all statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        return Mage::getResourceModel('sales/order_status_collection')
            ->toOptionHash();
    }

    /**
     * Order states getter
     *
     * @return array
     */
    public function getStates()
    {
        $states = [];
        foreach ($this->getNode('states')->children() as $state) {
            $label = (string) $state->label;
            $states[$state->getName()] = Mage::helper('sales')->__($label);
        }

        return $states;
    }

    /**
     * Retrieve statuses available for state
     * Get all possible statuses, or for specified state, or specified states array
     * Add labels by default. Return plain array of statuses, if no labels.
     *
     * @param mixed $state
     * @param bool $addLabels
     * @return array
     */
    public function getStateStatuses($state, $addLabels = true)
    {
        if (is_array($state)) {
            $key = implode('|', $state) . $addLabels;
        } else {
            $key = $state . $addLabels;
        }

        if (isset($this->_stateStatuses[$key])) {
            return $this->_stateStatuses[$key];
        }

        $statuses = [];
        if (empty($state) || !is_array($state)) {
            $state = [$state];
        }

        foreach ($state as $_state) {
            $stateNode = $this->_getState($_state);
            if ($stateNode) {
                $collection = Mage::getResourceModel('sales/order_status_collection')
                    ->addStateFilter($_state)
                    ->orderByLabel();
                /** @var Mage_Sales_Model_Order_Status $status */
                foreach ($collection as $status) {
                    $code = $status->getStatus();
                    if ($addLabels) {
                        $statuses[$code] = $status->getStoreLabel();
                    } else {
                        $statuses[] = $code;
                    }
                }
            }
        }

        $this->_stateStatuses[$key] = $statuses;
        return $statuses;
    }

    /**
     * Retrieve state available for status
     * Get all assigned states for specified status
     *
     * @param string $status
     * @return array
     */
    public function getStatusStates($status)
    {
        $states = [];
        $collection = Mage::getResourceModel('sales/order_status_collection')->addStatusFilter($status);
        foreach ($collection as $state) {
            $states[] = $state;
        }

        return $states;
    }

    /**
     * Retrieve states which are visible on front end
     *
     * @return array
     */
    public function getVisibleOnFrontStates()
    {
        $this->_getStates();
        return $this->_states['visible'];
    }

    /**
     * Get order states, visible on frontend
     *
     * @return array
     */
    public function getInvisibleOnFrontStates()
    {
        $this->_getStates();
        return $this->_states['invisible'];
    }

    /**
     * If not yet initialized, loads the "_states" array object.
     */
    private function _getStates()
    {
        if ($this->_states === null) {
            $this->_states = [
                'all'       => [],
                'visible'   => [],
                'invisible' => [],
                'statuses'  => [],
            ];
            foreach ($this->getNode('states')->children() as $state) {
                $name = $state->getName();
                $this->_states['all'][] = $name;
                $isVisibleOnFront = (string) $state->visible_on_front;
                if ((bool) $isVisibleOnFront || ($state->visible_on_front && $isVisibleOnFront == '')) {
                    $this->_states['visible'][] = $name;
                } else {
                    $this->_states['invisible'][] = $name;
                }

                foreach ($state->statuses->children() as $status) {
                    $this->_states['statuses'][$name][] = $status->getName();
                }
            }
        }
    }
}
