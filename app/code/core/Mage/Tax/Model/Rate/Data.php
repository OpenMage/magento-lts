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
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here...
 *
 * attributes:
 * - customer_class_id
 * - product_class_id
 * - country_id
 * - region_id
 * - postcode
 */
class Mage_Tax_Model_Rate_Data extends Mage_Core_Model_Abstract
{
    protected $_cache = array();

    protected function _construct()
    {
        $this->_init('tax/rate_data');
    }

    public function getRate()
    {
        if (!$this->getCountryId()
            //|| !$this->getPostcode()
            //|| !$this->getRegionId()
            || !$this->getCustomerClassId()
            || !$this->getProductClassId()
            ) {
            return 0;
            #throw Mage::exception('Mage_Tax', Mage::helper('tax')->__('Invalid data for tax rate calculation'));
        }

        $cacheKey = $this->getCustomerClassId()
            .'|'.$this->getProductClassId()
            .'|'.$this->getCountryId()
            .'|'.$this->getRegionId()
            .'|'.$this->getPostcode();

        if (!isset($this->_cache[$cacheKey])) {
            $this->unsRateValue();
            Mage::dispatchEvent('tax_rate_data_fetch', array('request'=>$this));
            if (!$this->hasRateValue()) {
                $this->setRateValue($this->_getResource()->fetchRate($this));
            }
            $this->_cache[$cacheKey] = $this->getRateValue();
        }

        return $this->_cache[$cacheKey];
    }

    /**
     * Remove USA post code
     */
    /*public function getRegionId()
    {
        if (!$this->getData('region_id') && $this->getPostcode()) {
            $regionId = Mage::getModel('usa/postcode')->load($this->getPostcode())->getRegionId();
            if ($regionId) {
                $this->setRegionId($regionId);
            }
        }
        return $this->getData('region_id');
    }*/

    public function getCustomerClassId()
    {
        if (!$this->getData('customer_class_id')) {
            $this->setCustomerClassId(Mage::getSingleton('customer/session')->getCustomer()->getTaxClassId());
        }
        return $this->getData('customer_class_id');
    }
}
