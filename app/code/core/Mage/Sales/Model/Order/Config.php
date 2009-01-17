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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order configuration model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Config extends Mage_Core_Model_Config_Base
{
    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global/sales/order'));
    }

    protected function _getStatus($status)
    {
        return $this->getNode('statuses/'.$status);
    }

    protected function _getState($state)
    {
        return $this->getNode('states/'.$state);
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
        if ($stateNode = $this->_getState($state)) {
            if ($stateNode->statuses) {
                foreach ($stateNode->statuses->children() as $statusNode) {
                    if (!$status) {
                        $status = $statusNode->getName();
                    }
                    $attributes = $statusNode->attributes();
                    if (isset($attributes['default'])) {
                        $status = $statusNode->getName();
                    }
                }
            }
        }
        return $status;
    }

    /**
     * Retrieve status label
     *
     * @param   string $status
     * @return  string
     */
    public function getStatusLabel($status)
    {
        if ($statusNode = $this->_getStatus($status)) {
            $status = (string) $statusNode->label;
            return Mage::helper('sales')->__($status);
        }
        return $status;
    }

    /**
     * Retrieve all statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        $statuses = array();
        foreach ($this->getNode('statuses')->children() as $status) {
            $label = (string) $status->label;
            $statuses[$status->getName()] = Mage::helper('sales')->__($label);
        }
        return $statuses;
    }

    /**
     * Retrieve statuses available for state
     *
     * @return array
     */
    public function getStateStatuses($state)
    {
        $statuses = array();
        if ($stateNode = $this->_getState($state)) {
            foreach ($stateNode->statuses->children() as $statusNode) {
                $status = $statusNode->getName();
                $statuses[$status] = $this->getStatusLabel($status);
            }
        }
        return $statuses;
    }
}
