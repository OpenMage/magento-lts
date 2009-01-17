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
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Tier Price indexer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Indexer_Tierprice extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    protected $_processChildren = false;

    protected function _construct()
    {
        $this->_init('catalogindex/indexer_price');
        $this->_currencyModel = Mage::getModel('directory/currency');
        $this->_customerGroups = Mage::getModel('customer/group')->getCollection();

        return parent::_construct();
    }

    public function createIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $data = array();

        $data['store_id'] = $attribute->getStoreId();
        $data['entity_id'] = $object->getId();
        $data['attribute_id'] = $attribute->getId();

        $result = array();
        $values = $object->getData($attribute->getAttributeCode());

        if (!is_array($values)) {
            return $result;
        }

        foreach ($values as $row) {
            if (isset($row['delete']) && $row['delete']) {
                continue;
            }

            $data['qty'] = $row['price_qty'];
            $data['value'] = $row['price'];
            if ($row['cust_group'] == Mage_Customer_Model_Group::CUST_GROUP_ALL) {
                foreach ($this->_customerGroups as $group) {
                    $data['customer_group_id'] = $group->getId();
                    $result[] = $data;
                }
            } else {
                $data['customer_group_id'] = $row['cust_group'];
                $result[] = $data;
            }

        }

        return $result;
    }

    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getAttributeCode() != 'tier_price') {
            return false;
        }

        return true;
    }

    protected function _getIndexableAttributeConditions()
    {
        $conditions = array();
        $conditions['attribute_code'] = 'tier_price';

        return $conditions;
    }
}