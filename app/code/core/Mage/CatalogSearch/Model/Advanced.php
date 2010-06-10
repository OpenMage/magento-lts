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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog advanced search model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Advanced extends Mage_Core_Model_Abstract
{
    /**
     * User friendly search criteria list
     *
     * @var array
     */
    private $_searchCriterias = array();

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/advanced');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Advanced
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve array of attributes used in advanced search
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $attributes Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection */
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $product = Mage::getModel('catalog/product');
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                //->addIsSearchableFilter()
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
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @return mixed
     */
    protected function _prepareCondition($attribute, $value)
    {
        $condition = false;

        if (is_array($value)) {
            if (!empty($value['from']) || !empty($value['to'])) { // range
                $condition = $value;
            } else if ($attribute->getBackendType() == 'varchar') { // multiselect
                $condition = array('in_set' => $value);
            } else if (!isset($value['from']) && !isset($value['to'])) { // select
                $condition = array('in' => $value);
            }
        } else {
            if (strlen($value) > 0) {
                if (in_array($attribute->getBackendType(), array('varchar', 'text', 'static'))) {
                    $condition = array('like' => '%' . $value . '%'); // text search
                } else {
                    $condition = $value;
                }
            }
        }

        return $condition;
    }

    /**
     * Add advanced search filters to product collection
     *
     * @param   array $values
     * @return  Mage_CatalogSearch_Model_Advanced
     */
    public function addFilters($values)
    {
        $attributes     = $this->getAttributes();
        $hasConditions  = false;
        $allConditions  = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (!isset($values[$attribute->getAttributeCode()])) {
                continue;
            }
            $value = $values[$attribute->getAttributeCode()];

            if ($attribute->getAttributeCode() == 'price') {
                if ($this->_getResource()->addPriceFilter($this, $attribute, $value)) {
                    $hasConditions = true;
                    $this->_addSearchCriteria($attribute, $value);
                }
            } else if ($attribute->isIndexable()) {
                if ($this->_getResource()->addIndexableAttributeFilter($this, $attribute, $value)) {
                    $hasConditions = true;
                    $this->_addSearchCriteria($attribute, $value);
                }
            } else {
                $condition = $this->_prepareCondition($attribute, $value);
                if ($condition === false) {
                    continue;
                }

                $this->_addSearchCriteria($attribute, $value);

                $table       = $attribute->getBackend()->getTable();
                if ($attribute->getBackendType() == 'static'){
                    $attributeId = $attribute->getAttributeCode();
                } else {
                    $attributeId = $attribute->getId();
                }
                $allConditions[$table][$attributeId] = $condition;
            }
        }

        if ($allConditions) {
            $this->getProductCollection()->addFieldsToFilter($allConditions);
        } else if (!$hasConditions) {
            Mage::throwException(Mage::helper('catalogsearch')->__('Please specify at least one search term.'));
        }

        return $this;
    }

    /**
     * Add data about search criteria to object state
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @param   mixed $value
     * @return  Mage_CatalogSearch_Model_Advanced
     */
    protected function _addSearchCriteria($attribute, $value)
    {
        $name = $attribute->getStoreLabel();

        if (is_array($value) && (isset($value['from']) || isset($value['to']))){
            if (isset($value['currency'])) {
                $currencyModel = Mage::getModel('directory/currency')->load($value['currency']);
                $from = $currencyModel->format($value['from'], array(), false);
                $to = $currencyModel->format($value['to'], array(), false);
            } else {
                $currencyModel = null;
            }

            if (strlen($value['from']) > 0 && strlen($value['to']) > 0) {
                // -
                $value = sprintf('%s - %s', ($currencyModel ? $from : $value['from']), ($currencyModel ? $to : $value['to']));
            } elseif (strlen($value['from']) > 0) {
                // and more
                $value = Mage::helper('catalogsearch')->__('%s and greater', ($currencyModel ? $from : $value['from']));
            } elseif (strlen($value['to']) > 0) {
                // to
                $value = Mage::helper('catalogsearch')->__('up to %s', ($currencyModel ? $to : $value['to']));
            }
        }

        if (($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') && is_array($value)) {
            foreach ($value as $k=>$v){
                $value[$k] = $attribute->getSource()->getOptionText($v);

                if (is_array($value[$k]))
                    $value[$k] = $value[$k]['label'];
            }
            $value = implode(', ', $value);
        } else if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
            $value = $attribute->getSource()->getOptionText($value);
            if (is_array($value))
                $value = $value['label'];
        } else if ($attribute->getFrontendInput() == 'boolean') {
            $value = $value == 1
                ? Mage::helper('catalogsearch')->__('Yes')
                : Mage::helper('catalogsearch')->__('No');
        }

        $this->_searchCriterias[] = array('name'=>$name, 'value'=>$value);
        return $this;
    }

    public function getSearchCriterias()
    {
        return $this->_searchCriterias;
    }

    /**
     * Retrieve advanced search product collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Advanced_Collection
     */
    public function getProductCollection(){
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getResourceModel('catalogsearch/advanced_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addTaxPercents()
                ->addStoreFilter();
                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);
        }

        return $this->_productCollection;
    }
}
