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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Flat Helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Product_Flat extends Mage_Catalog_Helper_Flat_Abstract
{
    /**
     * Catalog Product Flat Config
     */
    const XML_PATH_USE_PRODUCT_FLAT          = 'catalog/frontend/flat_catalog_product';
    const XML_NODE_ADD_FILTERABLE_ATTRIBUTES = 'global/catalog/product/flat/add_filterable_attributes';
    const XML_NODE_ADD_CHILD_DATA            = 'global/catalog/product/flat/add_child_data';

    /**
     * Path for flat flag model
     */
    const XML_PATH_FLAT_FLAG                 = 'global/catalog/product/flat/flag/model';

    /**
     * Catalog Flat Product index process code
     */
    const CATALOG_FLAT_PROCESS_CODE = 'catalog_product_flat';

    /**
     * Catalog Product Flat index process code
     *
     * @var string
     */
    protected $_indexerCode = self::CATALOG_FLAT_PROCESS_CODE;

    /**
     * Catalog Product Flat index process instance
     *
     * @var Mage_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Store flags which defines if Catalog Product Flat functionality is enabled
     *
     * @deprecated after 1.7.0.0
     *
     * @var array
     */
    protected $_isEnabled = array();

    /**
     * Catalog Product Flat Flag object
     *
     * @var Mage_Catalog_Model_Product_Flat_Flag
     */
    protected $_flagObject;

    /**
     * Retrieve Catalog Product Flat Flag object
     *
     * @return Mage_Catalog_Model_Product_Flat_Flag
     */
    public function getFlag()
    {
        if (is_null($this->_flagObject)) {
            $className = (string)Mage::getConfig()->getNode(self::XML_PATH_FLAT_FLAG);
            $this->_flagObject = Mage::getSingleton($className)
                ->loadSelf();
        }
        return $this->_flagObject;
    }

    /**
     * Check Catalog Product Flat functionality is enabled
     *
     * @param int|string|null|Mage_Core_Model_Store $store this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_PRODUCT_FLAT);
    }

    /**
     * Check if Catalog Product Flat Data has been initialized
     *
     * @param null|bool|int|Mage_Core_Model_Store $store Store(id) for which the value is checked
     * @return bool
     */
    public function isBuilt($store = null)
    {
        if ($store !== null) {
            return $this->getFlag()->isStoreBuilt(Mage::app()->getStore($store)->getId());
        }
        return $this->getFlag()->getIsBuilt();
    }

    /**
     * Check if Catalog Product Flat Data has been initialized for all stores
     *
     * @return bool
     */
    public function isBuiltAllStores()
    {
        $isBuildAll = true;
        foreach(Mage::app()->getStores(false) as $store) {
            /** @var $store Mage_Core_Model_Store */
            $isBuildAll = $isBuildAll && $this->isBuilt($store->getId());
        }
        return $isBuildAll;
    }

    /**
     * Is add filterable attributes to Flat table
     *
     * @return int
     */
    public function isAddFilterableAttributes()
    {
        return intval(Mage::getConfig()->getNode(self::XML_NODE_ADD_FILTERABLE_ATTRIBUTES));
    }

    /**
     * Is add child data to Flat
     *
     * @return int
     */
    public function isAddChildData()
    {
        return intval(Mage::getConfig()->getNode(self::XML_NODE_ADD_CHILD_DATA));
    }
}
