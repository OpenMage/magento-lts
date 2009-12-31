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
 * Catalog indexer price processor
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Indexer_Price extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    protected $_customerGroups = array();
    protected $_processChildrenForConfigurable = false;

    protected function _construct()
    {
        $this->_init('catalogindex/indexer_price');
        $this->_customerGroups = Mage::getModel('customer/group')->getCollection();
    }

    public function createIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute = null)
    {
        $data = array();

        $data['store_id'] = $attribute->getStoreId();
        $data['entity_id'] = $object->getId();
        $data['attribute_id'] = $attribute->getId();
        $data['value'] = $object->getData($attribute->getAttributeCode());

        if ($attribute->getAttributeCode() == 'price') {
            $result = array();
            foreach ($this->_customerGroups as $group) {
                $object->setCustomerGroupId($group->getId());
                $finalPrice = $object->getFinalPrice();
                $row = $data;
                $row['customer_group_id'] = $group->getId();
                $row['value'] = $finalPrice;
                $result[] = $row;
            }
            return $result;
        }

        return $data;
    }

    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getFrontendInput() != 'price') {
            return false;
        }
        if ($attribute->getAttributeCode() == 'tier_price') {
            return false;
        }
        if ($attribute->getAttributeCode() == 'minimal_price') {
            return false;
        }

        return true;
    }

    protected function _getIndexableAttributeConditions()
    {
        $conditions = "frontend_input = 'price' AND attribute_code <> 'price'";
        return $conditions;
    }
}
