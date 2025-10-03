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
     * and prevents disabling it with a warning message.
     *
     * The warning message format is configurable via delimiter variables:
     * - $delimiter: between page type and store views
     * - $labelDelimiter: between website and store view
     * The list of usages is formatted using _joinWithCommaAnd for proper English grammar.
     *
     * Throws a Mage_Core_Exception if the page is in use as a default page.
     *
     * @return Mage_Cms_Model_Page
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Delimiters (easy to edit)
        $delimiter        = ' ';    // Between page type and scope label, it can be used ' in '
        $labelDelimiter   = ' > ';   // Between website and store view

        if ($this->getIsActive() == self::STATUS_DISABLED) {
            /** @var array<string, array<string, bool>> $usage */
            $usage = [
                'web/default/cms_home_page'    => [],
                'web/default/cms_no_route'     => [],
                'web/default/cms_no_cookies'   => [],
            ];

            $configPaths = [
                'web/default/cms_home_page'    => Mage::helper('cms')->__('Home Page'),
                'web/default/cms_no_route'     => Mage::helper('cms')->__('No Route Page'),
                'web/default/cms_no_cookies'   => Mage::helper('cms')->__('No Cookies Page'),
            ];

            $collection = Mage::getModel('core/config_data')->getCollection()
                ->addFieldToFilter('path', ['in' => array_keys($configPaths)])
                ->addFieldToFilter('value', $this->getIdentifier());

            // Build associative array by website for grouping
            $scopesByType = [
                'Default Config' => [],
                // website name => [null => true, store view name => true]
            ];

            foreach ($collection as $item) {
                $scope = $item->getScope();
                $scopeId = $item->getScopeId();
                $path = $item->getPath();

                if ($scope === 'stores') {
                    $store = Mage::app()->getStore($scopeId);
                    $website = $store->getWebsite();
                    $websiteName = $website->getName();
                    $storeViewName = $store->getName();

                    if (!isset($scopesByType[$websiteName])) {
                        $scopesByType[$websiteName] = [];
                    }
                    $scopesByType[$websiteName][$storeViewName] = true;

                    if (isset($usage[$path])) {
                        $usage[$path][$websiteName . $labelDelimiter . $storeViewName] = true;
                    }
                } elseif ($scope === 'websites') {
                    $website = Mage::app()->getWebsite($scopeId);
                    $websiteName = $website->getName();

                    if (!isset($scopesByType[$websiteName])) {
                        $scopesByType[$websiteName] = [];
                    }
                    $scopesByType[$websiteName][null] = true;

                    if (isset($usage[$path])) {
                        $usage[$path][$websiteName] = true;
                    }
                } else {
                    // Default Config
                    $scopesByType['Default Config'][null] = true;

                    if (isset($usage[$path])) {
                        $usage[$path]['Default Config'] = true;
                    }
                }
            }

            $usedIn = [];
            // Build ordered labels for each page type.
            // Ordered as follows: "Default Config" first, then for each website, the website name, followed by each store view under that website.
            foreach ($usage as $path => $scopeLabels) {
                if ($scopeLabels) {
                    $labels = [];
                    // Default Config first
                    if (array_key_exists('Default Config', $scopeLabels)) {
                        $labels[] = 'Default Config';
                    }

                    // Then each website, first website name, then store views
                    foreach ($scopesByType as $websiteName => $storeViews) {
                        if ($websiteName === 'Default Config') {
                            continue;
                        }
                        // Website only
                        if (array_key_exists($websiteName, $scopeLabels)) {
                            $labels[] = $websiteName;
                        }
                        // Store views
                        foreach (array_keys($storeViews) as $storeViewName) {
                            $fullLabel = $websiteName . $labelDelimiter . $storeViewName;
                            if (array_key_exists($fullLabel, $scopeLabels)) {
                                $labels[] = $fullLabel;
                            }
                        }
                    }

                    $usedIn[] = sprintf(
                        '%s%s(%s)',
                        $configPaths[$path],
                        $delimiter,
                        implode(', ', $labels),
                    );
                }
            }

            if (count($usedIn)) {
                $configUrl = Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/web');
                $configLink = '<a href="' . $configUrl . '" target="_blank">' . Mage::helper('cms')->__('Default Pages') . '</a>';
                $message = Mage::helper('cms')->__(
                    'This page is used as %s.',
                    Mage::helper('cms')->joinWithCommaAnd($usedIn),
                );
                $message .= ' ' . Mage::helper('cms')->__(
                    'Please change the %s configuration per scope before disabling.',
                    $configLink,
                );
                Mage::throwException($message);
            }
        }

        return $this;
    }
}
