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
 * @method Mage_Cms_Model_Resource_Page_Collection getCollection()
 * @method string getContent()
 * @method string getContentHeading()
 * @method string getCreationTime()
 * @method string getCustomLayoutUpdateXml()
 * @method string getCustomRootTemplate()
 * @method string getCustomTheme()
 * @method string getCustomThemeFrom()
 * @method string getCustomThemeTo()
 * @method string getIdentifier()
 * @method int getIsActive()
 * @method string getLayoutUpdateXml()
 * @method string getMetaDescription()
 * @method string getMetaKeywords()
 * @method string getPreviewUrl()
 * @method Mage_Cms_Model_Resource_Page getResource()
 * @method Mage_Cms_Model_Resource_Page_Collection getResourceCollection()
 * @method string getRootTemplate()
 * @method int getSortOrder()
 * @method string getStoreCode()
 * @method string getStoreId()
 * @method array getStores()
 * @method string getTitle()
 * @method string getUpdateTime()
 * @method bool hasCreationTime()
 * @method bool hasStores()
 * @method $this setContent(string $value)
 * @method $this setContentHeading(string $value)
 * @method $this setCreationTime(string $value)
 * @method $this setCustomLayoutUpdateXml(string $value)
 * @method $this setCustomRootTemplate(string $value)
 * @method $this setCustomTheme(string $value)
 * @method $this setCustomThemeFrom(string $value)
 * @method $this setCustomThemeTo(string $value)
 * @method $this setIdentifier(string $value)
 * @method $this setIsActive(int $value)
 * @method $this setLayoutUpdateXml(string $value)
 * @method $this setMetaDescription(string $value)
 * @method $this setMetaKeywords(string $value)
 * @method $this setRootTemplate(string $value)
 * @method $this setSortOrder(int $value)
 * @method $this setStoreId(int $value)
 * @method $this setTitle(string $value)
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
     * @param int|string $id
     */
    public function getCmsPageTitleById($id): string
    {
        return $this->_getResource()->getCmsPageTitleById($id);
    }

    /**
     * Retrieves cms page identifier from DB by passed id.
     *
     * @param int|string $id
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
}
