<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms Page Model
 *
 * @category   Mage
 * @package    Mage_Cms
 *
 * @method Mage_Cms_Model_Resource_Page _getResource()
 * @method Mage_Cms_Model_Resource_Page getResource()
 * @method Mage_Cms_Model_Resource_Page_Collection getCollection()
 *
 * @method string getPreviewUrl()
 * @method bool hasStores()
 * @method array getStores()
 * @method string getStoreCode()
 */
class Mage_Cms_Model_Page extends Mage_Core_Model_Abstract implements Mage_Cms_Api_Data_PageInterface
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

    public function hasCreationTime(): bool
    {
        return $this->hasData(self::DATA_CREATION_TIME);
    }

    /**
     * @api
     */
    public function getPageId(): ?int
    {
        $pageId = $this->getDataByKey(self::DATA_ID);
        return is_null($pageId) ? null : (int) $pageId;
    }

    /**
     * @api
     * @return $this
     */
    public function setPageId(?int $pageId)
    {
        return $this->setData(self::DATA_ID, $pageId);
    }

    /**
     * @api
     */
    public function getContent(): ?string
    {
        return $this->getDataByKey(self::DATA_CONTENT);
    }

    /**
     * @api
     * @return $this
     */
    public function setContent(?string $content)
    {
        return $this->setData(self::DATA_CONTENT, $content);
    }

    /**
     * @api
     */
    public function getContentHeading(): ?string
    {
        return $this->getDataByKey(self::DATA_CONTENT_HEADING);
    }

    /**
     * @api
     * @return $this
     */
    public function setContentHeading(?string $heading)
    {
        return $this->setData(self::DATA_CONTENT_HEADING, $heading);
    }

    /**
     * @api
     */
    public function getCreationTime(): ?string
    {
        return $this->getDataByKey(self::DATA_CREATION_TIME);
    }

    /**
     * @api
     * @return $this
     */
    public function setCreationTime(?string $value)
    {
        return $this->setData(self::DATA_CREATION_TIME, $value);
    }

    /**
     * @api
     */
    public function getCustomLayoutUpdateXml(): ?string
    {
        return $this->getDataByKey(self::DATA_CUSTOM_LAYOUT_UPDATE_XML);
    }

    /**
     * @api
     * @return $this
     */
    public function setCustomLayoutUpdateXml(?string $xml)
    {
        return $this->setData(self::DATA_CUSTOM_LAYOUT_UPDATE_XML, $xml);
    }

    /**
     * @api
     */
    public function getCustomRootTemplate(): ?string
    {
        return $this->getDataByKey(self::DATA_CUSTOM_ROOT_TEMPLATE);
    }

    /**
     * @api
     * @return $this
     */
    public function setCustomRootTemplate(?string $template)
    {
        return $this->setData(self::DATA_CUSTOM_ROOT_TEMPLATE, $template);
    }

    /**
     * @api
     */
    public function getCustomTheme(): ?string
    {
        return $this->getDataByKey(self::DATA_CUSTOM_THEME);
    }

    /**
     * @api
     * @return $this
     */
    public function setCustomTheme(?string $from)
    {
        return $this->setData(self::DATA_CUSTOM_THEME, $from);
    }

    /**
     * @api
     */
    public function getCustomThemeFrom(): ?string
    {
        return $this->getDataByKey(self::DATA_CUSTOM_THEME_FROM);
    }

    /**
     * @api
     * @return $this
     */
    public function setCustomThemeFrom(?string $from)
    {
        return $this->setData(self::DATA_CUSTOM_THEME_FROM, $from);
    }

    /**
     * @api
     */
    public function getCustomThemeTo(): ?string
    {
        return $this->getDataByKey(self::DATA_CUSTOM_THEME_TO);
    }

    /**
     * @api
     * @return $this
     */
    public function setCustomThemeTo(?string $to)
    {
        return $this->setData(self::DATA_CUSTOM_THEME_TO, $to);
    }

    /**
     * @api
     */
    public function getIdentifier(): ?string
    {
        return $this->getDataByKey(self::DATA_IDENTIFIER);
    }

    /**
     * @api
     * @return $this
     */
    public function setIdentifier(?string $identifier)
    {
        return $this->setData(self::DATA_IDENTIFIER, $identifier);
    }

    /**
     * @api
     */
    public function getIsActive(): int
    {
        return (int) $this->getDataByKey(self::DATA_IS_ACTIVE);
    }

    /**
     * @api
     * @return $this
     */
    public function setIsActive(int $value)
    {
        return $this->setData(self::DATA_IS_ACTIVE, $value);
    }

    /**
     * @api
     */
    public function getLayoutUpdateXml(): ?string
    {
        return $this->getDataByKey(self::DATA_LAYOUT_UPDATE_XML);
    }

    /**
     * @api
     * @return $this
     */
    public function setLayoutUpdateXml(?string $xml)
    {
        return $this->setData(self::DATA_LAYOUT_UPDATE_XML, $xml);
    }

    /**
     * @api
     */
    public function getMetaDescription(): ?string
    {
        return $this->getDataByKey(self::DATA_META_DESCRIPTION);
    }

    /**
     * @api
     * @return $this
     */
    public function setMetaDescription(?string $description)
    {
        return $this->setData(self::DATA_META_DESCRIPTION, $description);
    }

    /**
     * @api
     */
    public function getMetaKeywords(): ?string
    {
        return $this->getDataByKey(self::DATA_META_KEYWORDS);
    }

    /**
     * @api
     * @return $this
     */
    public function setMetaKeywords(?string $keywords)
    {
        return $this->setData(self::DATA_META_KEYWORDS, $keywords);
    }

    /**
     * @api
     */
    public function getRootTemplate(): ?string
    {
        return $this->getDataByKey(self::DATA_ROOT_TEMPLATE);
    }

    /**
     * @api
     * @return $this
     */
    public function setRootTemplate(?string $template)
    {
        return $this->setData(self::DATA_ROOT_TEMPLATE, $template);
    }

    /**
     * @api
     */
    public function getSortOrder(): int
    {
        return (int) $this->getDataByKey(self::DATA_SORT_ORDER);
    }

    /**
     * @api
     * @return $this
     */
    public function setSortOrder(int $position)
    {
        return $this->setData(self::DATA_SORT_ORDER, $position);
    }

    /**
     * @api
     */
    public function getStoreId(): ?int
    {
        return (int) $this->getDataByKey(self::DATA_STORE_ID);
    }

    /**
     * @api
     * @return $this
     */
    public function setStoreId(int $storeId)
    {
        return $this->setData(self::DATA_STORE_ID, $storeId);
    }

    public function getTitle(): ?string
    {
        return $this->getDataByKey(self::DATA_TITLE);
    }

    /**
     * @return $this
     */
    public function setTitle(?string $title)
    {
        return $this->setData(self::DATA_TITLE, $title);
    }

    /**
     * @api
     */
    public function getUpdateTime(): ?string
    {
        return $this->getDataByKey(self::DATA_UPDATE_TIME);
    }

    /**
     * @api
     * @return $this
     */
    public function setUpdateTime(?string $time)
    {
        return $this->setData(self::DATA_UPDATE_TIME, $time);
    }
}
