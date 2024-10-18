<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Attribute _getResource()
 * @method Mage_Catalog_Model_Resource_Attribute getResource()
 * @method string getFrontendInputRenderer()
 * @method $this setFrontendInputRenderer(string $value)
 * @method $this setIsGlobal(int $value)
 * @method int getIsVisible()
 * @method $this setIsVisible(int $value)
 * @method int getIsSearchable()
 * @method $this setIsSearchable(int $value)
 * @method int getSearchWeight()
 * @method $this setSearchWeight(int $value)
 * @method $this setIsFilterable(int $value)
 * @method int getIsComparable()
 * @method $this setIsComparable(int $value)
 * @method $this setIsVisibleOnFront(int $value)
 * @method int getIsHtmlAllowedOnFront()
 * @method $this setIsHtmlAllowedOnFront(int $value)
 * @method int getIsUsedForPriceRules()
 * @method $this setIsUsedForPriceRules(int $value)
 * @method int getIsFilterableInSearch()
 * @method $this setIsFilterableInSearch(int $value)
 * @method int getUsedInProductListing()
 * @method $this setUsedInProductListing(int $value)
 * @method int getUsedForSortBy()
 * @method $this setUsedForSortBy(int $value)
 * @method int getIsConfigurable()
 * @method $this setIsConfigurable(int $value)
 * @method $this setApplyTo(string|array $value)
 * @method int getIsVisibleInAdvancedSearch()
 * @method $this setIsVisibleInAdvancedSearch(int $value)
 * @method int getPosition()
 * @method $this setPosition(int $value)
 * @method int getIsWysiwygEnabled()
 * @method $this setIsWysiwygEnabled(int $value)
 * @method int getIsUsedForPromoRules()
 * @method $this setIsUsedForPromoRules(int $value)
 * @method int getIsUsedForCustomerSegment()
 * @method $this setIsUsedForCustomerSegment(int $value)
 * @method int getIsUsedForTargetRules()
 * @method $this setIsUsedForTargetRules(int $value)
 * @method bool getIsUserDefined()
 */
class Mage_Catalog_Model_Resource_Eav_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    public const SCOPE_STORE                           = 0;
    public const SCOPE_GLOBAL                          = 1;
    public const SCOPE_WEBSITE                         = 2;

    public const MODULE_NAME                           = 'Mage_Catalog';
    public const ENTITY                                = 'catalog_eav_attribute';

    /**
     * @var string
     */
    protected $_eventPrefix                     = 'catalog_entity_attribute';
    /**
     * @var string
     */
    protected $_eventObject                     = 'attribute';

    /**
     * Array with labels
     *
     * @var array|null
     */
    protected static $_labels                   = null;

    protected function _construct()
    {
        $this->_init('catalog/attribute');
    }

    /**
     * Processing object before save data
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->setData('modulePrefix', self::MODULE_NAME);
        if (isset($this->_origData['is_global'])) {
            if (!isset($this->_data['is_global'])) {
                $this->_data['is_global'] = self::SCOPE_GLOBAL;
            }
            if (($this->_data['is_global'] != $this->_origData['is_global'])
                && $this->_getResource()->isUsedBySuperProducts($this)
            ) {
                Mage::throwException(Mage::helper('catalog')->__('Scope must not be changed, because the attribute is used in configurable products.'));
            }
        }
        if ($this->getFrontendInput() == 'price') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('catalog/product_attribute_backend_price');
            }
        }
        if ($this->getFrontendInput() == 'textarea') {
            if ($this->getIsWysiwygEnabled()) {
                $this->setIsHtmlAllowedOnFront(1);
            }
        }
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @inheritDoc
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        Mage::getSingleton('eav/config')->clear();

        return parent::_afterSave();
    }

    /**
     * Register indexing event before delete catalog eav attribute
     *
     * @inheritDoc
     */
    protected function _beforeDelete()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            Mage::throwException(Mage::helper('catalog')->__('This attribute is used in configurable products.'));
        }
        Mage::getSingleton('index/indexer')->logEvent(
            $this,
            self::ENTITY,
            Mage_Index_Model_Event::TYPE_DELETE
        );
        return parent::_beforeDelete();
    }

    /**
     * Init indexing process after catalog eav attribute delete commit
     *
     * @return $this
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        Mage::getSingleton('index/indexer')->indexEvents(
            self::ENTITY,
            Mage_Index_Model_Event::TYPE_DELETE
        );
        return $this;
    }

    /**
     * Return is attribute global
     *
     * @return int
     */
    public function getIsGlobal()
    {
        return $this->_getData('is_global');
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return (int)$this->getData('store_id');
    }

    /**
     * Retrieve apply to products array
     * Return empty array if applied to all products
     *
     * @return array
     */
    public function getApplyTo()
    {
        if ($this->getData('apply_to')) {
            if (is_array($this->getData('apply_to'))) {
                return $this->getData('apply_to');
            }
            return explode(',', $this->getData('apply_to'));
        } else {
            return [];
        }
    }

    /**
     * Retrieve source model
     *
     * @return string
     */
    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    /**
     * Check is allow for rule condition
     *
     * @return bool
     */
    public function isAllowedForRuleCondition()
    {
        $allowedInputTypes = ['text', 'multiselect', 'textarea', 'date', 'datetime', 'select', 'boolean', 'price'];
        return $this->getIsVisible() && in_array($this->getFrontendInput(), $allowedInputTypes);
    }

    /**
     * Retrieve don't translated frontend label
     *
     * @return string|array
     */
    public function getFrontendLabel()
    {
        return $this->_getData('frontend_label');
    }

    /**
     * Retrieve is_filterable value
     * @return int
     */
    public function getIsFilterable()
    {
        return $this->_getData('is_filterable');
    }

    /**
     * Get Attribute translated label for store
     *
     * @deprecated
     * @return string
     */
    protected function _getLabelForStore()
    {
        return $this->getFrontendLabel();
    }

    /**
     * Initialize store Labels for attributes
     *
     * @deprecated
     * @param int $storeId
     */
    public static function initLabels($storeId = null)
    {
        if (is_null(self::$_labels)) {
            if (is_null($storeId)) {
                $storeId = Mage::app()->getStore()->getId();
            }
            $attributeLabels = [];
            $attributes = Mage::getResourceSingleton('catalog/product')->getAttributesByCode();
            foreach ($attributes as $attribute) {
                if (strlen($attribute->getData('frontend_label')) > 0) {
                    $attributeLabels[] = $attribute->getData('frontend_label');
                }
            }

            self::$_labels = Mage::app()->getTranslator()->getResource()
                ->getTranslationArrayByStrings($attributeLabels, $storeId);
        }
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    /**
     * Check is an attribute used in EAV index
     *
     * @return bool
     */
    public function isIndexable()
    {
        // exclude price attribute
        if ($this->getAttributeCode() == 'price') {
            return false;
        }

        if (!$this->getIsFilterableInSearch() && !$this->getIsVisibleInAdvancedSearch() && !$this->getIsFilterable()) {
            return false;
        }

        $backendType    = $this->getBackendType();
        $frontendInput  = $this->getFrontendInput();

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } elseif (($backendType == 'varchar' || $backendType == 'text') && $frontendInput == 'multiselect') {
            return true;
        } elseif ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * Retrieve index type for indexable attribute
     *
     * @return string|false
     */
    public function getIndexType()
    {
        if (!$this->isIndexable()) {
            return false;
        }
        if ($this->getBackendType() == 'decimal') {
            return 'decimal';
        }

        return 'source';
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return $this
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);

        return $this;
    }
}
