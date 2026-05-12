<?php

declare(strict_types=1);

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
 * @method Mage_Cms_Model_Resource_Page            _getResource()
 * @method Mage_Cms_Model_Resource_Page_Collection getCollection()
 * @method string                                  getPreviewUrl()
 * @method Mage_Cms_Model_Resource_Page            getResource()
 * @method Mage_Cms_Model_Resource_Page_Collection getResourceCollection()
 * @method string                                  getStoreCode()
 * @method array                                   getStores()
 * @method bool                                    hasCreationTime()
 * @method bool                                    hasStores()
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

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('cms/page');
    }

    public function getContent(): string
    {
        return (string) $this->_getData('content');
    }

    public function getContentHeading(): ?string
    {
        $value = $this->_getData('content_heading');
        return $value !== null ? (string) $value : null;
    }

    public function getCreationTime(): ?string
    {
        $value = $this->_getData('creation_time');
        return $value !== null ? (string) $value : null;
    }

    public function getCustomLayoutUpdateXml(): ?string
    {
        $value = $this->_getData('custom_layout_update_xml');
        return $value !== null ? (string) $value : null;
    }

    public function getCustomRootTemplate(): ?string
    {
        $value = $this->_getData('custom_root_template');
        return $value !== null ? (string) $value : null;
    }

    public function getCustomTheme(): ?string
    {
        $value = $this->_getData('custom_theme');
        return $value !== null ? (string) $value : null;
    }

    public function getCustomThemeFrom(): ?string
    {
        $value = $this->_getData('custom_theme_from');
        return $value !== null ? (string) $value : null;
    }

    public function getCustomThemeTo(): ?string
    {
        $value = $this->_getData('custom_theme_to');
        return $value !== null ? (string) $value : null;
    }

    public function getIdentifier(): ?string
    {
        $value = $this->_getData('identifier');
        return $value !== null ? (string) $value : null;
    }

    public function getIsActive(): int
    {
        return (int) $this->_getData('is_active');
    }

    public function getLayoutUpdateXml(): ?string
    {
        $value = $this->_getData('layout_update_xml');
        return $value !== null ? (string) $value : null;
    }

    public function getMetaDescription(): ?string
    {
        $value = $this->_getData('meta_description');
        return $value !== null ? (string) $value : null;
    }

    public function getMetaKeywords(): ?string
    {
        $value = $this->_getData('meta_keywords');
        return $value !== null ? (string) $value : null;
    }

    public function getRootTemplate(): ?string
    {
        $value = $this->_getData('root_template');
        return $value !== null ? (string) $value : null;
    }

    public function getSortOrder(): int
    {
        return (int) $this->_getData('sort_order');
    }

    public function getTitle(): ?string
    {
        $value = $this->_getData('title');
        return $value !== null ? (string) $value : null;
    }

    public function getUpdateTime(): ?string
    {
        $value = $this->_getData('update_time');
        return $value !== null ? (string) $value : null;
    }

    public function setContent(string $value): static
    {
        return $this->setData('content', $value);
    }

    public function setContentHeading(?string $value): static
    {
        return $this->setData('content_heading', $value);
    }

    public function setCreationTime(?string $value): static
    {
        return $this->setData('creation_time', $value);
    }

    public function setCustomLayoutUpdateXml(?string $value): static
    {
        return $this->setData('custom_layout_update_xml', $value);
    }

    public function setCustomRootTemplate(?string $value): static
    {
        return $this->setData('custom_root_template', $value);
    }

    public function setCustomTheme(?string $value): static
    {
        return $this->setData('custom_theme', $value);
    }

    public function setCustomThemeFrom(?string $value): static
    {
        return $this->setData('custom_theme_from', $value);
    }

    public function setCustomThemeTo(?string $value): static
    {
        return $this->setData('custom_theme_to', $value);
    }

    public function setIdentifier(?string $value): static
    {
        return $this->setData('identifier', $value);
    }

    public function setIsActive(int $value): static
    {
        return $this->setData('is_active', $value);
    }

    public function setLayoutUpdateXml(?string $value): static
    {
        return $this->setData('layout_update_xml', $value);
    }

    public function setMetaDescription(?string $value): static
    {
        return $this->setData('meta_description', $value);
    }

    public function setMetaKeywords(?string $value): static
    {
        return $this->setData('meta_keywords', $value);
    }

    public function setRootTemplate(?string $value): static
    {
        return $this->setData('root_template', $value);
    }

    public function setSortOrder(int $value): static
    {
        return $this->setData('sort_order', $value);
    }

    public function setTitle(?string $value): static
    {
        return $this->setData('title', $value);
    }

    public function setUpdateTime(?string $value): static
    {
        return $this->setData('update_time', $value);
    }

    /**
     * @inheritDoc
     */
    #[Override]
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
     * @throws Mage_Core_Exception
     */
    public function noRoutePage()
    {
        return $this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName());
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param  string              $identifier
     * @param  int                 $storeId
     * @return string
     * @throws Mage_Core_Exception
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Retrieves cms page title from DB by passed identifier.
     * @throws Mage_Core_Exception
     */
    public function getCmsPageTitleByIdentifier(string $identifier): string
    {
        return $this->_getResource()->getCmsPageTitleByIdentifier($identifier);
    }

    /**
     * Retrieves cms page title from DB by passed id.
     *
     * @param  int|string          $id
     * @throws Mage_Core_Exception
     */
    public function getCmsPageTitleById($id): string
    {
        return $this->_getResource()->getCmsPageTitleById($id);
    }

    /**
     * Retrieves cms page identifier from DB by passed id.
     *
     * @param  int|string          $id
     * @throws Mage_Core_Exception
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

    /**
     * @throws Mage_Core_Exception
     */
    public function getUsedInStoreConfigCollection(?array $paths = []): Mage_Core_Model_Resource_Db_Collection_Abstract
    {
        return $this->_getResource()->getUsedInStoreConfigCollection($this, $paths);
    }

    /**
     * @throws Mage_Core_Exception
     */
    public function isUsedInStoreConfig(?array $paths = []): bool
    {
        return $this->_getResource()->isUsedInStoreConfig($this, $paths);
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }
}
