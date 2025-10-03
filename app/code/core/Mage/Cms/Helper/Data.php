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
     * Check if <link rel="canonical"> can be used for CMS pages
     *
     * @param int|string|null|Mage_Core_Model_Store $store
     */
    public function canUseCanonicalTag($store = null): bool
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_CMS_CANONICAL_TAG, $store);
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
            return $items[0] . ' and ' . $items[1];
        } else {
            return implode(', ', array_slice($items, 0, -1)) . ', and ' . $items[$count - 1];
        }
    }
}
