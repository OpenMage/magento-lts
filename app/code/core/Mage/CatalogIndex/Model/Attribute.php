<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Attribute index model
 *
 * @package    Mage_CatalogIndex
 *
 * @method Mage_CatalogIndex_Model_Resource_Attribute _getResource()
 * @method int                                        getAttributeId()
 * @method Mage_CatalogIndex_Model_Resource_Attribute getResource()
 * @method int                                        getStoreId()
 * @method int                                        getValue()
 * @method $this                                      setAttributeId(int $value)
 * @method $this                                      setEntityId(int $value)
 * @method $this                                      setStoreId(int $value)
 * @method $this                                      setValue(int $value)
 */
class Mage_CatalogIndex_Model_Attribute extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogindex/attribute');
        $this->_getResource()->setStoreId(Mage::app()->getStore()->getId());
    }

    /**
     * @param  Mage_Eav_Model_Entity_Attribute $attribute
     * @param  string                          $filter
     * @param  array|int                       $entityFilter
     * @return array
     */
    public function getFilteredEntities($attribute, $filter, $entityFilter)
    {
        return $this->_getResource()->getFilteredEntities($attribute, $filter, $entityFilter);
    }

    /**
     * @param  Mage_Eav_Model_Entity_Attribute $attribute
     * @param  Zend_Db_Select                  $entityFilter
     * @return array
     */
    public function getCount($attribute, $entityFilter)
    {
        return $this->_getResource()->getCount($attribute, $entityFilter);
    }

    /**
     * @param  array                           $optionIds
     * @param  Mage_Eav_Model_Entity_Attribute $attribute
     * @param  mixed                           $entityFilter
     * @return mixed
     */
    public function checkCount($optionIds, $attribute, $entityFilter)
    {
        return $this->_getResource()->checkCount($optionIds, $attribute, $entityFilter);
    }

    /**
     * @param  Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @param  Mage_Eav_Model_Entity_Attribute                     $attribute
     * @param  string                                              $value
     * @return $this
     */
    public function applyFilterToCollection($collection, $attribute, $value)
    {
        $this->_getResource()->applyFilterToCollection($collection, $attribute, $value);
        return $this;
    }
}
