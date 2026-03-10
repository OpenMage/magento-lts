<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Flat Helper
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Product_Flat extends Mage_Catalog_Helper_Flat_Abstract
{
    /**
     * Catalog Product Flat Config
     */
    public const XML_PATH_USE_PRODUCT_FLAT          = 'catalog/frontend/flat_catalog_product';

    public const XML_NODE_ADD_FILTERABLE_ATTRIBUTES = 'global/catalog/product/flat/add_filterable_attributes';

    public const XML_NODE_ADD_CHILD_DATA            = 'global/catalog/product/flat/add_child_data';

    /**
     * Path for flat flag model
     */
    public const XML_PATH_FLAT_FLAG                 = 'global/catalog/product/flat/flag/model';

    /**
     * Catalog Flat Product index process code
     */
    public const CATALOG_FLAT_PROCESS_CODE = 'catalog_product_flat';

    protected $_moduleName = 'Mage_Catalog';

    /**
     * Catalog Product Flat index process code
     *
     * @var string
     */
    protected $_indexerCode = self::CATALOG_FLAT_PROCESS_CODE;

    /**
     * Catalog Product Flat index process instance
     *
     * @var null|Mage_Index_Model_Process
     */
    protected $_process = null;

    /**
     * Store flags which defines if Catalog Product Flat functionality is enabled
     *
     * @deprecated after 1.7.0.0
     *
     * @var array
     */
    protected $_isEnabled = [];

    /**
     * Catalog Product Flat Flag object
     *
     * @var null|Mage_Catalog_Model_Product_Flat_Flag
     */
    protected $_flagObject;

    /**
     * Catalog Product Flat force status enable/disable
     * to force EAV for products in quote
     * store settings will be used by default
     *
     * @var bool
     */
    protected $_forceFlatStatus = false;

    /**
     * Old Catalog Product Flat forced status
     *
     * @var null|bool
     */
    protected $_forceFlatStatusOld;

    /**
     * Retrieve Catalog Product Flat Flag object
     *
     * @return Mage_Catalog_Model_Product_Flat_Flag
     * @throws Mage_Core_Exception
     */
    public function getFlag()
    {
        if (is_null($this->_flagObject)) {
            $className = (string) Mage::getConfig()->getNode(self::XML_PATH_FLAT_FLAG);
            /** @var Mage_Catalog_Model_Product_Flat_Flag $classInstance */
            $classInstance = Mage::getSingleton($className);
            $this->_flagObject = $classInstance;
            $this->_flagObject->loadSelf();
        }

        return $this->_flagObject;
    }

    /**
     * Check Catalog Product Flat functionality is enabled
     *
     * @param null|int|Mage_Core_Model_Store|string $store this parameter is deprecated and no longer in use
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
     * @param  null|bool|int|Mage_Core_Model_Store $store Store(id) for which the value is checked
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
        foreach (Mage::app()->getStores(false) as $store) {
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
        return (int) Mage::getConfig()->getNode(self::XML_NODE_ADD_FILTERABLE_ATTRIBUTES);
    }

    /**
     * Is add child data to Flat
     *
     * @return int
     */
    public function isAddChildData()
    {
        return (int) Mage::getConfig()->getNode(self::XML_NODE_ADD_CHILD_DATA);
    }

    /**
     * Enable Catalog Product Flat
     *
     * @param bool $save
     */
    public function enableFlatCollection($save = false)
    {
        if ($save) {
            $this->_forceFlatStatusOld = $this->_forceFlatStatus;
        }

        $this->_forceFlatStatus = false;
    }

    /**
     * Disable Catalog Product Flat
     *
     * @param bool $save
     */
    public function disableFlatCollection($save = false)
    {
        $this->_forceFlatStatus = true;

        if ($save) {
            $this->_forceFlatStatusOld = $this->_forceFlatStatus;
        }
    }

    /**
     * Reset Catalog Product Flat
     */
    public function resetFlatCollection()
    {
        if (isset($this->_forceFlatStatusOld)) {
            $this->_forceFlatStatus = $this->_forceFlatStatusOld;
        } else {
            $this->_forceFlatStatus = false;
        }
    }

    /**
     * Checks if Catalog Product Flat was forced disabled
     *
     * @return bool
     */
    public function isFlatCollectionDisabled()
    {
        return $this->_forceFlatStatus;
    }
}
