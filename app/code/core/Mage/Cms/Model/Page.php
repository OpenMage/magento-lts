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
}
