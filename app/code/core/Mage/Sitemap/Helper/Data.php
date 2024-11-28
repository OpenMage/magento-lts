<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sitemap
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_CATEGORY_ENABLED          = 'sitemap/category/enabled';
    public const XML_PATH_CMS_PAGE_ENABLED          = 'sitemap/page/enabled';
    public const XML_PATH_PRODUCT_ENABLED           = 'sitemap/product/enabled';

    protected $_moduleName = 'Mage_Sitemap';

    /**
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     */
    public function isCategoryEnabled($store = null): bool
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATEGORY_ENABLED, $store);
    }

    /**
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     */
    public function isCmsPageEnabled($store = null): bool
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CMS_PAGE_ENABLED, $store);
    }

    /**
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     */
    public function isProductEnabled($store = null): bool
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_ENABLED, $store);
    }
}
