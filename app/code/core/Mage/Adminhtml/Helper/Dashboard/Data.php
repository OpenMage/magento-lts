<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Data helper for dashboard
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Dashboard_Data extends Mage_Core_Helper_Data
{
    protected $_moduleName = 'Mage_Adminhtml';

    protected $_locale = null;
    protected $_stores = null;

    /**
     * Retrieve stores configured in system.
     *
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function getStores()
    {
        if (!$this->_stores) {
            $this->_stores = Mage::app()->getStore()->getResourceCollection()->load();
        }

        return $this->_stores;
    }

    /**
     * Retrieve number of loaded stores
     *
     * @return int
     */
    public function countStores()
    {
        return count($this->getStores()->getItems());
    }

    /**
     * Prepare array with periods for dashboard graphs
     *
     * @return array
     */
    public function getDatePeriods()
    {
        return [
            '24h' => $this->__('Last 24 Hours'),
            '7d'  => $this->__('Last 7 Days'),
            '1m'  => $this->__('Current Month'),
            '1y'  => $this->__('YTD'),
            '2y'  => $this->__('2YTD')
        ];
    }

    /**
     * Create data hash to ensure that we got valid
     * data, and it is not changed by someone else.
     *
     * @param string $data
     * @return string
     */
    public function getChartDataHash($data)
    {
        $secret = (string)Mage::getConfig()->getNode(Mage_Core_Model_App::XML_PATH_INSTALL_DATE);
        return md5($data . $secret);
    }
}
