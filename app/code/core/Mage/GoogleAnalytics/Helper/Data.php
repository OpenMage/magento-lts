<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
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
 * @author     Magento Core Team <core@magentocommerce.com>
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

    /**
     * @var string classic google analytics tracking code
     */
    public const TYPE_ANALYTICS = 'analytics';

    /**
     * @var string google analytics universal tracking code
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
     * @param null $store
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
     * @return string
     */
    public function isUseUniversalAnalytics($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TYPE, $store) == self::TYPE_UNIVERSAL;
    }
}
