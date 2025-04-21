<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/**
 * Catalog advanced search model
 *
 * @package    Mage_CatalogSearch
 *
 * @method Mage_CatalogSearch_Model_Resource_Advanced getResource()
 * @method Mage_CatalogSearch_Model_Resource_Advanced_Collection getCollection()
 *
 * @method int getEntityTypeId()
 * @method $this setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method $this setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method $this setTypeId(string $value)
 * @method string getSku()
 * @method $this setSku(string $value)
 * @method int getHasOptions()
 * @method $this setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method $this setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 */
class Mage_CatalogSearch_Model_Advanced extends Mage_Core_Model_Abstract
{
    /**
     * User friendly search criteria list
     *
     * @var array
     */
    protected $_searchCriterias = [];

    /**
     * Current search engine
     *
     * @var object|Mage_CatalogSearch_Model_Resource_Fulltext_Engine
     */
    protected $_engine;

    /**
     * Found products collection
     *
     * @var Mage_CatalogSearch_Model_Resource_Advanced_Collection|null
     */
    protected $_productCollection;

    protected function _construct()
    {
        $this->_getEngine();
        $this->_init('catalogsearch/advanced');
    }

    /**
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Engine|object
     */
    protected function _getEngine()
    {
        if ($this->_engine == null) {
            $this->_engine = Mage::helper('catalogsearch')->getEngine();
        }

        return $this->_engine;
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @inheritDoc
     */
    protected function _getResource()
    {
        $resourceName = $this->_engine->getResourceName();
        if ($resourceName) {
            $this->_resourceName = $resourceName;
        }
        return parent::_getResource();
    }

    /**
     * Retrieve array of attributes used in advanced search
     *
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getAttributes()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributes */
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $product = Mage::getModel('catalog/product');
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addHasOptionsFilter()
                ->addDisplayInAdvancedSearchFilter()
                ->addStoreLabel(Mage::app()->getStore()->getId())
                ->setOrder('main_table.attribute_id', 'asc')
                ->load();
            foreach ($attributes as $attribute) {
                $attribute->setEntity($product->getResource());
            }
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }

    /**
     * Prepare search condition for attribute
     *
     * @deprecated after 1.4.1.0 - use Mage_CatalogSearch_Model_Resource_Advanced->_prepareCondition()
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @return mixed
     */
    protected function _prepareCondition($attribute, $value)
    {
        return $this->_getResource()->prepareCondition($attribute, $value, $this->getProductCollection());
    }

    /**
     * Add advanced search filters to product collection
     *
     * @param   array $values
     * @return  $this
     */
    public function addFilters($values)
    {
        $attributes     = $this->getAttributes();
        $hasConditions  = false;
        $allConditions  = [];

        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            if (!isset($values[$attribute->getAttributeCode()])) {
                continue;
            }
            $value = $values[$attribute->getAttributeCode()];
            if (!is_array($value)) {
                $value = trim($value);
            }

            if ($attribute->getAttributeCode() === 'price') {
                $value['from'] = isset($value['from']) ? trim($value['from']) : '';
                $value['to'] = isset($value['to']) ? trim($value['to']) : '';
                if (is_numeric($value['from']) || is_numeric($value['to'])) {
                    if (!empty($value['currency'])) {
                        $rate = Mage::app()->getStore()->getBaseCurrency()->getRate($value['currency']);
                    } else {
                        $rate = 1;
                    }
                    if ($this->_getResource()
                        ->addRatedPriceFilter(
                            $this->getProductCollection(),
                            $attribute,
                            $value,
                            $rate,
                        )
                    ) {
                        $hasConditions = true;
                        $this->_addSearchCriteria($attribute, $value);
                    }
                }
            } elseif ($attribute->isIndexable()) {
                if (!is_string($value) || strlen($value) != 0) {
                    if ($this->_getResource()
                        ->addIndexableAttributeModifiedFilter(
                            $this->getProductCollection(),
                            $attribute,
                            $value,
                        )
                    ) {
                        $hasConditions = true;
                        $this->_addSearchCriteria($attribute, $value);
                    }
                }
            } else {
                $condition = $this->_prepareCondition($attribute, $value);
                if ($condition === false) {
                    continue;
                }

                $this->_addSearchCriteria($attribute, $value);

                $table = $attribute->getBackend()->getTable();
                if ($attribute->getBackendType() == 'static') {
                    $attributeId = $attribute->getAttributeCode();
                } else {
                    $attributeId = $attribute->getId();
                }
                $allConditions[$table][$attributeId] = $condition;
            }
        }
        if ($allConditions) {
            $this->getProductCollection()->addFieldsToFilter($allConditions);
        } elseif (!$hasConditions) {
            Mage::throwException(Mage::helper('catalogsearch')->__('Please specify at least one search term.'));
        }

        return $this;
    }

    /**
     * Add data about search criteria to object state
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @param   mixed $value
     * @return  $this
     */
    protected function _addSearchCriteria($attribute, $value)
    {
        $name = $attribute->getStoreLabel();

        if (is_array($value)) {
            if (isset($value['from']) && isset($value['to'])) {
                if (!empty($value['from']) || !empty($value['to'])) {
                    if (isset($value['currency'])) {
                        $currencyModel = Mage::getModel('directory/currency')->load($value['currency']);
                        $from = $currencyModel->format($value['from'], [], false);
                        $to = $currencyModel->format($value['to'], [], false);
                    } else {
                        $currencyModel = null;
                    }

                    if (strlen($value['from']) > 0 && strlen($value['to']) > 0) {
                        $value = sprintf(
                            '%s - %s',
                            ($currencyModel ? $from : $value['from']),
                            ($currencyModel ? $to : $value['to']),
                        );
                    } elseif (strlen($value['from']) > 0) {
                        // and more
                        $value = Mage::helper('catalogsearch')->__('%s and greater', ($currencyModel ? $from : $value['from']));
                    } elseif (strlen($value['to']) > 0) {
                        // to
                        $value = Mage::helper('catalogsearch')->__('up to %s', ($currencyModel ? $to : $value['to']));
                    }
                } else {
                    return $this;
                }
            }
        }

        if (($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect')
            && is_array($value)
        ) {
            foreach ($value as $key => $val) {
                $value[$key] = $attribute->getSource()->getOptionText($val);

                if (is_array($value[$key])) {
                    $value[$key] = $value[$key]['label'];
                }
            }
            $value = implode(', ', $value);
        } elseif ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
            $value = $attribute->getSource()->getOptionText($value);
            if (is_array($value)) {
                $value = $value['label'];
            }
        } elseif ($attribute->getFrontendInput() == 'boolean') {
            $value = $value == 1
                ? Mage::helper('catalogsearch')->__('Yes')
                : Mage::helper('catalogsearch')->__('No');
        }

        $this->_searchCriterias[] = ['name' => $name, 'value' => $value];
        return $this;
    }

    /**
     * Returns prepared search criteria in text
     *
     * @return array
     */
    public function getSearchCriterias()
    {
        return $this->_searchCriterias;
    }

    /**
     * Retrieve advanced search product collection
     *
     * @return Mage_CatalogSearch_Model_Resource_Advanced_Collection
     */
    public function getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = $this->_engine->getAdvancedResultCollection();
            $this->prepareProductCollection($collection);
            if (!$collection) {
                return $collection;
            }
            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    /**
     * Prepare product collection
     *
     * @param Mage_CatalogSearch_Model_Resource_Advanced_Collection $collection
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function prepareProductCollection($collection)
    {
        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setStore(Mage::app()->getStore())
            ->addMinimalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addAttributeToFilter('status', [
                'in' => Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(),
            ]);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

        return $this;
    }
}
