<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog flat helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Category_Flat extends Mage_Catalog_Helper_Flat_Abstract
{
    /**
     * Catalog Category Flat Is Enabled Config
     */
    const XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY = 'catalog/frontend/flat_catalog_category';

    /**
     * Catalog Flat Category index process code
     */
    const CATALOG_CATEGORY_FLAT_PROCESS_CODE = 'catalog_category_flat';

    /**
     * Catalog Category Flat index process code
     *
     * @var string
     */
    protected $_indexerCode = self::CATALOG_CATEGORY_FLAT_PROCESS_CODE;

    /**
     * Store catalog Category Flat index process instance
     *
     * @var Mage_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @param bool $skipAdminCheck this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    public function isEnabled($skipAdminCheck = false)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY);
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @param null|bool|int|Mage_Core_Model_Store $store Store(id) for which the value is checked
     * @return bool
     */
    public function isBuilt($store = null)
    {
        return Mage::getResourceSingleton('catalog/category_flat')->isBuilt($store);
    }

    /**
     * Check if Catalog Category Flat Data has been initialized
     *
     * @deprecated after 1.7.0.0 use Mage_Catalog_Helper_Category_Flat::isBuilt() instead
     *
     * @return bool
     */
    public function isRebuilt()
    {
        return $this->isBuilt();
    }
}
