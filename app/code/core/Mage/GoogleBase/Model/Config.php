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
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Base Config model
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Config extends Varien_Object
{
    /**
     *  Return config var
     *
     *  @param    string $key Var path key
     *  @param    int $storeId Store View Id
     *  @return	  mixed
     */
    public function getConfigData($key, $storeId = null)
    {
        if (!$this->hasData($key)) {
            $value = Mage::getStoreConfig('google/googlebase/' . $key, $storeId);
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     * Google Account login
     *
     * @param int $storeId
     * @return string
     */
    public function getAccountLogin($storeId = null)
    {
        return $this->getConfigData('login', $storeId);
    }

    /**
     * Google Account password
     *
     * @param int $storeId
     * @return string
     */
    public function getAccountPassword($storeId = null)
    {
        return $this->getConfigData('password', $storeId);
    }

    /**
     * Google Account type
     *
     * @param int $storeId
     * @return string
     */
    public function getAccountType($storeId = null)
    {
        return $this->getConfigData('account_type', $storeId);
    }

    /**
     * Google Account target country info
     *
     * @param int $storeId
     * @return array
     */
    public function getTargetCountryInfo($storeId = null)
    {
        return $this->getCountryInfo($this->getTargetCountry($storeId), null, $storeId);
    }

    /**
     * Google Account target country
     *
     * @param int $storeId
     * @return string Two-letters country ISO code
     */
    public function getTargetCountry($storeId = null)
    {
        return $this->getConfigData('target_country', $storeId);
    }

    /**
     * Google Account target currency (for target country)
     *
     * @param int $storeId
     * @return string Three-letters currency ISO code
     */
    public function getTargetCurrency($storeId = null)
    {
        $country = $this->getTargetCountry($storeId);
        return $this->getCountryInfo($country, 'currency');
    }

    /**
     * Check whether System Base currency equals Google Base target currency or not
     *
     * @param int $storeId
     * @return boolean
     */
    public function isValidBaseCurrencyCode($storeId = null)
    {
        return Mage::app()->getStore($storeId)->getBaseCurrencyCode() == $this->getTargetCurrency($storeId);
    }

    /**
     * Default Item Type for country
     *
     * @param int $storeId
     * @return string
     */
    public function getDefaultItemType($storeId = null)
    {
        $country = $this->getTargetCountry($storeId);
        return $this->getCountryInfo($country, 'default_item_type');
    }

    /**
     * Google Base supported countries
     *
     * @param int $storeId
     * @return array
     */
    public function getAllowedCountries($storeId = null)
    {
        return $this->getConfigData('allowed_countries', $storeId);
    }

    /**
     * Country info such as name, locale, language etc.
     *
     * @param string $iso two-letters country ISO code
     * @param string $field If specified, return value for field
     * @param int $storeId
     * @return array|string
     */
    public function getCountryInfo($iso, $field = null, $storeId = null)
    {
        $countries = $this->getAllowedCountries($storeId);
        $country = isset($countries[$iso]) ? $countries[$iso] : null;
        return is_null($field) ? $country : ( isset($country[$field]) ? $country[$field] : null );
    }
}
