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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Dashboard visitors section data helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Dashboard_Visitor extends Mage_Adminhtml_Helper_Dashboard_Abstract
{

    protected $_onlineDataCache = null;

    protected function _initCollection()
    {
        $range = $this->_getRangeAndPeriod($this->getParam('range'));
        $this->_collection = Mage::getResourceSingleton('log/visitor_collection');
        if($this->getParam('store')) {
            $this->_collection = $this->_collection->addFieldToFilter('store_id', $this->getParam('store'));
        }
        $this->_collection->getAggregatedData($range['period'], $range['range'], $this->getParam('custom_from'), $this->getParam('custom_to'));
    }

    protected function _getRangeAndPeriod($type)
    {
        switch ($type) {
            case "24h":
                $period = 24;
                $range = 'hour';
                break;
            case "7d":
                $period = 7;
                $range = 'day';
                break;
            case "1m":
                $period = 30;
                $range = 'day';
                break;
            case "2m":
                $period = 60;
                $range = 'day';
                break;
            case "1y":
                $period = 12;
                $range = 'month';
                break;
            default:
                $range = 0;
                $range = 'month';
                break;
        }

        return array('range'=>$range, 'period'=>$period);
    }

    protected function _initOnlineData()
    {
        $this->_onlineDataCache = array();
        $customersCollection = Mage::getResourceModel('log/visitor_collection')->useOnlineFilter()->addFieldToFilter('type', '1');
        $visitorsCollection = Mage::getResourceModel('log/visitor_collection')->useOnlineFilter()->addFieldToFilter('type', 'v');
        $this->_onlineDataCache['customers'] = $customersCollection->getSize();
        $this->_onlineDataCache['visitors']     = $visitorsCollection->getSize();
        $this->_onlineDataCache['total']        = $this->_onlineDataCache['customers'] + $this->_onlineDataCache['visitors'];

    }

    public function getOnlineData($part)
    {
        if(is_null($this->_onlineDataCache)) {
            $this->_initOnlineData();
        }

        return isset($this->_onlineDataCache[$part]) ? $this->_onlineDataCache[$part] : null;
    }

}
