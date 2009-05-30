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
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog advanced search model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Advanced extends Varien_Object
{
    /**
     * User friendly search criteria list
     *
     * @var array
     */
    private $_searchCriterias = array();

    public function getAttributes()
    {
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $product = Mage::getModel('catalog/product');
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($product->getResource()->getTypeId())
                //->addIsSearchableFilter()
                ->addHasOptionsFilter()
                ->addDisplayInAdvancedSearchFilter()
                ->setOrder('attribute_id', 'asc')
                ->load();
            foreach ($attributes as $attribute) {
                $attribute->setEntity($product->getResource());
            }
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }

    /**
     * Add advanced search filters to product collection
     *
     * @param   array $values
     * @return  Mage_CatalogSearch_Model_Advanced
     */
    public function addFilters($values)
    {
        $attributes = $this->getAttributes();
        $allConditions = array();
        $filteredAttributes = array();
        $indexFilters = Mage::getModel('catalogindex/indexer')->buildEntityFilter(
            $attributes,
            $values,
            $filteredAttributes,
            $this->getProductCollection()
        );

        foreach ($indexFilters as $filter) {
            $this->getProductCollection()->addFieldToFilter('entity_id', array('in'=>new Zend_Db_Expr($filter)));
        }

        $priceFilters = Mage::getModel('catalogindex/indexer')->buildEntityPriceFilter(
            $attributes,
            $values,
            $filteredAttributes,
            $this->getProductCollection()
        );

        foreach ($priceFilters as $code=>$filter) {
            $this->getProductCollection()->getSelect()->joinInner(
                array("_price_filter_{$code}"=>$filter),
                "`_price_filter_{$code}`.`entity_id` = `e`.`entity_id`",
                array()
            );
        }

        foreach ($attributes as $attribute) {
            $code      = $attribute->getAttributeCode();
            $condition = false;

            if (isset($values[$code])) {
                $value = $values[$code];

                if (is_array($value)) {
                    if ((isset($value['from']) && strlen($value['from']) > 0)
                        || (isset($value['to']) && strlen($value['to']) > 0)) {
                        $condition = $value;
                    }
                    elseif ($attribute->getBackend()->getType() == 'varchar') {
                        $condition = array('in_set'=>$value);
                    }
                    elseif (!isset($value['from']) && !isset($value['to'])) {
                        $condition = array('in'=>$value);
                    }
                } else {
                    if (strlen($value)>0) {
                        if (in_array($attribute->getBackend()->getType(), array('varchar', 'text'))) {
                            $condition = array('like'=>'%'.$value.'%');
                        } elseif ($attribute->getFrontendInput() == 'boolean') {
                            $condition = array('in' => array('0','1'));
                        } else {
                            $condition = $value;
                        }
                    }
                }
            }

            if (false !== $condition) {
                $this->_addSearchCriteria($attribute, $value);

                if (in_array($code, $filteredAttributes))
                    continue;

                $table = $attribute->getBackend()->getTable();
                $attributeId = $attribute->getId();
                if ($attribute->getBackendType() == 'static'){
                    $attributeId = $attribute->getAttributeCode();
                    $condition = array('like'=>"%{$condition}%");
                }

                $allConditions[$table][$attributeId] = $condition;
            }
        }
        if ($allConditions) {
            $this->getProductCollection()->addFieldsToFilter($allConditions);
        } else if (!count($filteredAttributes)) {
            Mage::throwException(Mage::helper('catalogsearch')->__('You have to specify at least one search term'));
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
        $name = $attribute->getFrontend()->getLabel();

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
