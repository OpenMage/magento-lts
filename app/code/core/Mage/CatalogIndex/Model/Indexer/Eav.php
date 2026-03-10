<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Catalog indexer eav processor
 *
 * @package    Mage_CatalogIndex
 *
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Eav _getResource()
 * @method int                                          getAttributeId()
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Eav getResource()
 * @method int                                          getStoreId()
 * @method int                                          getValue()
 * @method $this                                        setAttributeId(int $value)
 * @method $this                                        setEntityId(int $value)
 * @method $this                                        setStoreId(int $value)
 * @method $this                                        setValue(int $value)
 */
class Mage_CatalogIndex_Model_Indexer_Eav extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogindex/indexer_eav');
        parent::_construct();
    }

    /**
     * @return array
     */
    public function createIndexData(Mage_Catalog_Model_Product $object, ?Mage_Eav_Model_Entity_Attribute_Abstract $attribute = null)
    {
        $data = [];

        $data['store_id'] = $attribute->getStoreId();
        $data['entity_id'] = $object->getId();
        $data['attribute_id'] = $attribute->getId();
        $data['value'] = $object->getData($attribute->getAttributeCode());

        if ($attribute->getFrontendInput() == 'multiselect') {
            $origData = $data;
            $data = [];

            $value = explode(',', $origData['value']);
            foreach ($value as $item) {
                $row = $origData;
                $row['value'] = $item;
                $data[] = $row;
            }
        }

        //return $this->_spreadDataForStores($object, $attribute, $data);
        return $data;
    }

    /**
     * @return bool
     */
    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getIsFilterable() == 0 && $attribute->getIsVisibleInAdvancedSearch() == 0) {
            return false;
        }

        if ($attribute->getFrontendInput() != 'select' && $attribute->getFrontendInput() != 'multiselect') {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function _getIndexableAttributeConditions()
    {
        return "main_table.frontend_input IN ('select', 'multiselect') AND (additional_table.is_filterable IN (1, 2) OR additional_table.is_visible_in_advanced_search = 1)";
    }
}
