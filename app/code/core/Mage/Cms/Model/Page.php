<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Cms Page Model
 *
 * @package    Mage_Cms
 *
 * @method Mage_Cms_Model_Resource_Page _getResource()
 * @method Mage_Cms_Model_Resource_Page getResource()
 * @method Mage_Cms_Model_Resource_Page_Collection getCollection()
 *
 * @method string getContentHeading()
 * @method $this setContentHeading(string $value)
 * @method string getContent()
 * @method $this setContent(string $value)
 * @method string getCreationTime()
 * @method $this setCreationTime(string $value)
 * @method int getIsActive()
 * @method $this setIsActive(int $value)
 * @method string getLayoutUpdateXml()
 * @method $this setLayoutUpdateXml(string $value)
 * @method bool hasCreationTime()
 * @method string getCustomTheme()
 * @method $this setCustomTheme(string $value)
 * @method string getCustomRootTemplate()
 * @method $this setCustomRootTemplate(string $value)
 * @method string getCustomLayoutUpdateXml()
 * @method $this setCustomLayoutUpdateXml(string $value)
 * @method string getCustomThemeFrom()
 * @method $this setCustomThemeFrom(string $value)
 * @method string getCustomThemeTo()
 * @method $this setCustomThemeTo(string $value)
 * @method string getIdentifier()
 * @method $this setIdentifier(string $value)
 * @method string getMetaDescription()
 * @method $this setMetaDescription(string $value)
 * @method string getMetaKeywords()
 * @method $this setMetaKeywords(string $value)
 * @method string getPreviewUrl()
 * @method string getRootTemplate()
 * @method $this setRootTemplate(string $value)
 * @method $this setStoreId(int $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method bool hasStores()
 * @method array getStores()
 * @method string getStoreCode()
 * @method string getStoreId()
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getUpdateTime()
 * @method $this setUpdateTime(string $value)
 */
class Mage_Cms_Model_Page extends Mage_Core_Model_Abstract
{
    public const NOROUTE_PAGE_ID = 'no-route';

    /**
     * Page's Statuses
     */
    public const STATUS_ENABLED  = 1;
    public const STATUS_DISABLED = 0;

    public const CACHE_TAG       = 'cms_page';
    protected $_cacheTag         = 'cms_page';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'cms_page';

    protected function _construct()
    {
        $this->_init('cms/page');
    }

    /**
     * @inheritDoc
     */
    public function load($id, $field = null)
    {
        if (is_null($id)) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Page
     *
     * @return $this
     */
    public function noRoutePage()
    {
        return $this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName());
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return string
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Retrieves cms page title from DB by passed identifier.
     */
    public function getCmsPageTitleByIdentifier(string $identifier): string
    {
        return $this->_getResource()->getCmsPageTitleByIdentifier($identifier);
    }

    /**
     * Retrieves cms page title from DB by passed id.
     *
     * @param string|int $id
     */
    public function getCmsPageTitleById($id): string
    {
        return $this->_getResource()->getCmsPageTitleById($id);
    }

    /**
     * Retrieves cms page identifier from DB by passed id.
     *
     * @param string|int $id
     */
    public function getCmsPageIdentifierById($id): string
    {
        return $this->_getResource()->getCmsPageIdentifierById($id);
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object([
            self::STATUS_ENABLED => Mage::helper('cms')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('cms')->__('Disabled'),
        ]);

        Mage::dispatchEvent('cms_page_get_available_statuses', ['statuses' => $statuses]);

        return $statuses->getData();
    }

    public function getUsedInStoreConfigCollection(?array $paths = []): Mage_Core_Model_Resource_Db_Collection_Abstract
    {
        return $this->_getResource()->getUsedInStoreConfigCollection($this, $paths);
    }

    public function isUsedInStoreConfig(?array $paths = []): bool
    {
        return $this->_getResource()->isUsedInStoreConfig($this, $paths);
    }

    /**
     * Checks if the CMS page is used as a default page (Home, No Route, No Cookies) for any store view or website,
     * and prevents disabling or changing the URL key with a warning message and blocks save.
     *
     * - For disabling, blocks if the page is referenced in config and shows detailed warning.
     * - For URL Key change, blocks if the old identifier is referenced in config and shows detailed warning.
     *
     * The list of usages is formatted using Mage_Cms_Helper_Data::getUsageScopes for proper English grammar.
     *
     * Throws a Mage_Core_Exception if the page is in use as a default page and disabling or changing URL key is attempted.
     *
     * @return Mage_Cms_Model_Page
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Prevent disabling if the page is used in store configuration as Home Page, No Route Page, or No Cookies Page
        if ($this->getIsActive() == self::STATUS_DISABLED) {
            $usedIn = Mage::helper('cms')->getUsageScopes($this->getIdentifier());
            if (count($usedIn)) {
                $this->_throwPageUsedException($usedIn, 'disabling');
            }
        }

        // Prevent changing the URL key if the page is used in store configuration as Home Page, No Route Page, or No Cookies Page
        $origIdentifier = $this->getOrigData('identifier');
        $newIdentifier = $this->getIdentifier();
        if ($origIdentifier !== null && $origIdentifier !== $newIdentifier) {
            $usedIn = Mage::helper('cms')->getUsageScopes($origIdentifier);
            if (count($usedIn)) {
                $this->_throwPageUsedException($usedIn, 'changing');
            }
        }

        return $this;
    }

    /**
     * Throws an exception if a CMS page is used in store configuration (e.g. Home Page, No Route Page, No Cookies Page).
     * Builds and formats the exception message including usage scopes and configuration link.
     *
     * @param array $usedIn Array of usage scopes (strings) where the page is currently set
     * @param string $action Action attempted on the page (e.g. "disabling", "changing")
     * @throws Mage_Core_Exception
     */
    protected function _throwPageUsedException(array $usedIn, $action) :void
    {
        $configUrl = Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/web');
        $configLink = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            $configUrl,
            Mage::helper('cms')->__('Default Pages')
        );
        $message = sprintf(
            Mage::helper('cms')->__('This page is used as %s.'),
            Mage::helper('cms')->joinWithCommaAnd($usedIn)
        );
        $message .= ' ' . sprintf(
                Mage::helper('cms')->__('Please change the %s configuration per scope before %s.'),
                $configLink,
                $action
            );
        Mage::throwException($message);
    }

}
