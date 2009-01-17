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
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping data helper
 */
class Mage_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getTrackingAjaxUrl()
    {
        return $this->_getUrl('shipping/tracking/ajax');
    }

    public function getTrackingPopUpUrlByOrderId($oid='')
    {
        return $this->_getUrl('shipping/tracking/popup',array("order_id"=>$oid));
    }

    public function getTrackingPopUpUrlByTrackID($tracknum='')
    {
        return $this->_getUrl('shipping/tracking/popup',array("track_id"=>$tracknum));
    }

    public function getTrackingPopUpUrlByShipId($shipid='')
    {
        return $this->_getUrl('shipping/tracking/popup',array("ship_id"=>$shipid));
    }

    public function isFreeMethod($method, $storeId=null)
    {
        $arr = explode('_', $method, 2);
        if (!isset($arr[1])) {
            return false;
        }
        $freeMethod = Mage::getStoreConfig('carriers/'.$arr[0].'/free_method', $storeId);
        return $freeMethod == $arr[1];
    }
}
