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
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_countryCollection;
    protected $_regionCollection;
    protected $_regionJson;
    protected $_currencyCache = array();

    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }

    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getModel('directory/country')->getResourceCollection()
                ->loadByStore();
        }
        return $this->_countryCollection;
    }

    /**
     * Retrieve regions data json
     *
     * @return string
     */
    public function getRegionJson()
    {

    	Varien_Profiler::start('TEST: '.__METHOD__);
    	if (!$this->_regionJson) {
    	    $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE'.Mage::app()->getStore()->getId();
    	    if (Mage::app()->useCache('config')) {
    	        $json = Mage::app()->loadCache($cacheKey);
    	    }
    	    if (empty($json)) {
    	    	$countryIds = array();
    	    	foreach ($this->getCountryCollection() as $country) {
    	    		$countryIds[] = $country->getCountryId();
    	    	}
        		$collection = Mage::getModel('directory/region')->getResourceCollection()
        			->addCountryFilter($countryIds)
        			->load();
    	    	$regions = array();
    	    	foreach ($collection as $region) {
    	    		if (!$region->getRegionId()) {
    	    			continue;
    	    		}
    	    		$regions[$region->getCountryId()][$region->getRegionId()] = array(
    	    			'code'=>$region->getCode(),
    	    			'name'=>$region->getName()
    	    		);
    	    	}
    	    	$json = Zend_Json::encode($regions);

    	    	if (Mage::app()->useCache('config')) {
    	    	    Mage::app()->saveCache($json, $cacheKey, array('config'));
                }
    	    }
	    	$this->_regionJson = $json;
    	}

    	Varien_Profiler::stop('TEST: '.__METHOD__);
    	return $this->_regionJson;
    }

    public function currencyConvert($amount, $from, $to=null)
    {
        if (empty($this->_currencyCache[$from])) {
            $this->_currencyCache[$from] = Mage::getModel('directory/currency')->load($from);
        }
        if (is_null($to)) {
            $to = Mage::app()->getStore()->getCurrentCurrencyCode();
        }
        $converted = $this->_currencyCache[$from]->convert($amount, $to);
        return $converted;
    }
}
