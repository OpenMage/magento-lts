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
 * @package    Mage_Sendfriend
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sendfriend Data Helper
 *
 * @category   Mage
 * @package    Mage_Sendfriend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sendfriend_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_ENABLED          = 'sendfriend/email/enabled';
    public const XML_PATH_ALLOW_FOR_GUEST  = 'sendfriend/email/allow_guest';
    public const XML_PATH_MAX_RECIPIENTS   = 'sendfriend/email/max_recipients';
    public const XML_PATH_MAX_PER_HOUR     = 'sendfriend/email/max_per_hour';
    public const XML_PATH_LIMIT_BY         = 'sendfriend/email/check_by';
    public const XML_PATH_EMAIL_TEMPLATE   = 'sendfriend/email/template';

    public const COOKIE_NAME   = 'stf';

    public const CHECK_IP      = 1;
    public const CHECK_COOKIE  = 0;

    protected $_moduleName = 'Mage_Sendfriend';

    /**
     * Check is enabled Module
     *
     * @param int $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Check allow send email for guest
     *
     * @param int $store
     * @return bool
     */
    public function isAllowForGuest($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALLOW_FOR_GUEST, $store);
    }

    /**
     * Retrieve Max Recipients
     *
     * @param int $store
     * @return int
     */
    public function getMaxRecipients($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_MAX_RECIPIENTS, $store);
    }

    /**
     * Retrieve Max Products Sent in 1 Hour
     *
     * @param int $store
     * @return int
     */
    public function getMaxEmailPerPeriod($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_MAX_PER_HOUR, $store);
    }

    /**
     * Retrieve Limitation Period in seconds (1 hour)
     *
     * @return int
     */
    public function getPeriod()
    {
        return 3600;
    }

    /**
     * Retrieve Limit Sending By
     *
     * @param int $store
     * @return int
     */
    public function getLimitBy($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_LIMIT_BY, $store);
    }

    /**
     * Retrieve Email Template
     *
     * @param int $store
     * @return mixed
     */
    public function getEmailTemplate($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $store);
    }

    /**
     * Retrieve Key Name for Cookie
     *
     * @see self::COOKIE_NAME
     * @return string
     */
    public function getCookieName()
    {
        return self::COOKIE_NAME;
    }
}
