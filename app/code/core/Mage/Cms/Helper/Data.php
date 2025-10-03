<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * CMS Data helper
 *
 * @package    Mage_Cms
 */
class Mage_Cms_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_NODE_PAGE_TEMPLATE_FILTER     = 'global/cms/page/tempate_filter';
    public const XML_NODE_BLOCK_TEMPLATE_FILTER    = 'global/cms/block/tempate_filter';
    public const XML_NODE_ALLOWED_STREAM_WRAPPERS  = 'global/cms/allowed_stream_wrappers';
    public const XML_NODE_ALLOWED_MEDIA_EXT_SWF    = 'adminhtml/cms/browser/extensions/media_allowed/swf';
    public const XML_PATH_USE_CMS_CANONICAL_TAG    = 'web/seo/cms_canonical_tag';

    protected $_moduleName = 'Mage_Cms';

    /**
     * Retrieve Template processor for Page Content
     *
     * @return Mage_Core_Model_Abstract|Varien_Filter_Template
     */
    public function getPageTemplateProcessor()
    {
        $model = (string) Mage::getConfig()->getNode(self::XML_NODE_PAGE_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }

    /**
     * Retrieve Template processor for Block Content
     *
     * @return Mage_Core_Model_Abstract|Varien_Filter_Template
     */
    public function getBlockTemplateProcessor()
    {
        $model = (string) Mage::getConfig()->getNode(self::XML_NODE_BLOCK_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }

    /**
     * Return list with allowed stream wrappers
     *
     * @return array
     */
    public function getAllowedStreamWrappers()
    {
        $allowedStreamWrappers = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_STREAM_WRAPPERS);
        if ($allowedStreamWrappers instanceof Mage_Core_Model_Config_Element) {
            $allowedStreamWrappers = $allowedStreamWrappers->asArray();
        }

        return is_array($allowedStreamWrappers) ? $allowedStreamWrappers : [];
    }

    /**
     * Check is swf file extension disabled
     *
     * @return true
     * @deprecated since 19.5.0
     */
    public function isSwfDisabled()
    {
        return true;
    }

    /**
     * Check if <link rel="canonical"> can be used for CMS pages
     *
     * @param int|string|null|Mage_Core_Model_Store $store
     */
    public function canUseCanonicalTag($store = null): bool
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_CMS_CANONICAL_TAG, $store);
    }

    /**
     * Returns an array of usages for a CMS page identifier in critical config (Home, No Route, No Cookies)
     * Each entry describes where the page is used (Default Config, Website, Store View), ordered like in admin config.
     *
     * @param string $identifier
     * @return array
     */
    public function getUsageScopes($identifier)
    {
        // Delimiters (easy to edit)
        $delimiter      = ' '; // Between page type and scope label, it can be
        $labelDelimiter = ' > '; // Between website and store view

        $configPaths = [
            'web/default/cms_home_page'    => $this->__('Home Page'),
            'web/default/cms_no_route'     => $this->__('No Route Page'),
            'web/default/cms_no_cookies'   => $this->__('No Cookies Page'),
        ];

        /** @var array<string, array<string, bool>> $usage */
        $usage = [
            'web/default/cms_home_page'    => [],
            'web/default/cms_no_route'     => [],
            'web/default/cms_no_cookies'   => [],
        ];

        // Build associative array by website for grouping
        $scopesByType = [
            'Default Config' => [],
            // website name => [null => true, store view name => true]
        ];

        $collection = Mage::getModel('core/config_data')->getCollection()
            ->addFieldToFilter('path', ['in' => array_keys($configPaths)])
            ->addFieldToFilter('value', $identifier);

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
                    // Iterate store view names for each website.
                    // Note: array_keys($storeViews) may contain null when a website-level configuration exists.
                    // Skip null store view names to avoid invalid labels like "Website Name > ".
                    foreach (array_keys($storeViews) as $storeViewName) {
                        if ($storeViewName === null) {
                            continue;
                        }
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

        return $usedIn;
    }

    /**
     * Joins an array of strings into a grammatically correct English list using commas and 'and'.
     *
     * Examples:
     * - ['A']         => 'A'
     * - ['A', 'B']    => 'A and B'
     * - ['A', 'B', 'C'] => 'A, B, and C'
     *
     * @param array $items Array of strings to join
     * @return string
     */
    public function joinWithCommaAnd(array $items)
    {
        $count = count($items);
        if ($count === 0) {
            return '';
        } elseif ($count === 1) {
            return $items[0];
        } elseif ($count === 2) {
            return $items[0] . $this->__(' and ') . $items[1];
        } else {
            return implode(', ', array_slice($items, 0, -1)) . $this->__(', and ') . $items[$count - 1];
        }
    }
}
