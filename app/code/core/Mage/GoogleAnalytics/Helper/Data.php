<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleAnalytics data helper
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 */
class Mage_GoogleAnalytics_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    public const XML_PATH_ACTIVE        = 'google/analytics/active';
    public const XML_PATH_TYPE          = 'google/analytics/type';
    public const XML_PATH_ACCOUNT       = 'google/analytics/account';
    public const XML_PATH_ANONYMIZATION = 'google/analytics/anonymization';
    public const XML_PATH_ECOMM         = 'google/analytics/enhanced_ecommerce';


    /**
     * @var string google analytics 4
     */
    public const TYPE_ANALYTICS4 = 'analytics4';

    /**
     * @var string classic google analytics tracking code
     * @deprecated
     */
    public const TYPE_ANALYTICS = 'analytics';

    /**
     * @var string google analytics universal tracking code
     * @deprecated
     */
    public const TYPE_UNIVERSAL = 'universal';

    protected $_moduleName = 'Mage_GoogleAnalytics';

    /**
     * Whether GA is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isGoogleAnalyticsAvailable($store = null)
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
        return $accountId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }

    /**
     * Whether GA IP Anonymization is enabled
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $store $store
     * @return bool
     */
    public function isIpAnonymizationEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ANONYMIZATION, $store);
    }

    /**
     * Get GA account id
     *
     * @param string $store
     * @return string
     */
    public function getAccountId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
    }

    /**
     * Returns true if should use Google Universal Analytics
     *
     * @param string $store
     * @return bool
     * @deprecated
     */
    public function isUseUniversalAnalytics($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TYPE, $store) == self::TYPE_UNIVERSAL;
    }

    /**
     * Returns true if should use Google Universal Analytics 4
     *
     * @param string $store
     * @return bool
     */
    public function isUseAnalytics4($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TYPE, $store) == self::TYPE_ANALYTICS4;
    }

    /**
     * Whether GA Enhanced eCommerce data should be submitted
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $store $store
     * @return bool
     */
    public function isEnhancedECommEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ECOMM, $store);
    }
}
