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
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog indexer eav processor
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Indexer_Eav extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogindex/indexer_eav');
        return parent::_construct();
    }

    public function createIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute = null)
    {
        $data = array();

        $data['store_id'] = $attribute->getStoreId();
        $data['entity_id'] = $object->getId();
        $data['attribute_id'] = $attribute->getId();
        $data['value'] = $object->getData($attribute->getAttributeCode());

        if ($attribute->getFrontendInput() == 'multiselect') {
            $origData = $data;
            $data = array();

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

    protected function _getIndexableAttributeConditions()
    {
        $conditions = "main_table.frontend_input IN ('select', 'multiselect') AND (additional_table.is_filterable IN (1, 2) OR additional_table.is_visible_in_advanced_search = 1)";
        return $conditions;

        $conditions = array();
        $conditions['frontend_input'] = array('select', 'multiselect');
        $conditions['or']['is_filterable'] = array(1, 2);
        $conditions['or']['is_visible_in_advanced_search'] = 1;
    }
}
