<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core Cookie helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_Cookie extends Mage_Core_Helper_Abstract
{
    /**
     * Cookie name for users who allowed cookie save
     */
    public const IS_USER_ALLOWED_SAVE_COOKIE  = 'user_allowed_save_cookie';

    /**
     * Path to configuration, check is enable cookie restriction mode
     */
    public const XML_PATH_COOKIE_RESTRICTION  = 'web/cookie/cookie_restriction';

    /**
     * Cookie restriction lifetime configuration path
     */
    public const XML_PATH_COOKIE_RESTRICTION_LIFETIME = 'web/cookie/cookie_restriction_lifetime';

    /**
     * Cookie restriction notice cms block identifier
     */
    public const COOKIE_RESTRICTION_NOTICE_CMS_BLOCK_IDENTIFIER = 'cookie_restriction_notice_block';

    protected $_moduleName = 'Mage_Core';

    /**
     * Store instance
     *
     * @var Mage_Core_Model_Store
     */
    protected $_currentStore;

    /**
     * Cookie instance
     *
     * @var Mage_Core_Model_Cookie
     */
    protected $_cookieModel;

    /**
     * Website instance
     *
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * Initializes store, cookie and website objects.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = [])
    {
        $this->_currentStore = $data['current_store'] ?? Mage::app()->getStore();

        if (!$this->_currentStore instanceof Mage_Core_Model_Store) {
            throw new InvalidArgumentException('Required store object is invalid');
        }

        $this->_cookieModel = $data['cookie_model'] ?? Mage::getSingleton('core/cookie');

        if (!$this->_cookieModel instanceof Mage_Core_Model_Cookie) {
            throw new InvalidArgumentException('Required cookie object is invalid');
        }

        $this->_website = $data['website'] ?? Mage::app()->getWebsite();

        if (!$this->_website instanceof Mage_Core_Model_Website) {
            throw new InvalidArgumentException('Required website object is invalid');
        }
    }

    /**
     * Check if cookie restriction notice should be displayed
     *
     * @return bool
     */
    public function isUserNotAllowSaveCookie()
    {
        $acceptedSaveCookiesWebsites = $this->_getAcceptedSaveCookiesWebsites();
        return $this->_currentStore->getConfig(self::XML_PATH_COOKIE_RESTRICTION)
            && empty($acceptedSaveCookiesWebsites[$this->_website->getId()]);
    }

    /**
     * Return serialized list of accepted save cookie website
     *
     * @return string
     */
    public function getAcceptedSaveCookiesWebsiteIds()
    {
        $acceptedSaveCookiesWebsites = $this->_getAcceptedSaveCookiesWebsites();
        $acceptedSaveCookiesWebsites[$this->_website->getId()] = 1;
        return json_encode($acceptedSaveCookiesWebsites);
    }

    /**
     * Get accepted save cookies websites
     *
     * @return array
     */
    protected function _getAcceptedSaveCookiesWebsites()
    {
        $serializedList = $this->_cookieModel->get(self::IS_USER_ALLOWED_SAVE_COOKIE);
        $unSerializedList = $serializedList ? json_decode($serializedList, true) : null;
        return is_array($unSerializedList) ? $unSerializedList : [];
    }

    /**
     * Get cookie restriction lifetime (in seconds)
     *
     * @return int
     */
    public function getCookieRestrictionLifetime()
    {
        return (int) $this->_currentStore->getConfig(self::XML_PATH_COOKIE_RESTRICTION_LIFETIME);
    }

    /**
     * Get cookie restriction notice cms block identifier
     *
     * @return string
     */
    public function getCookieRestrictionNoticeCmsBlockIdentifier()
    {
        return self::COOKIE_RESTRICTION_NOTICE_CMS_BLOCK_IDENTIFIER;
    }
}
