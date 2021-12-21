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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Tier Price indexer
 *
 * @property Mage_Directory_Model_Currency $_currencyModel
 * @property Mage_Customer_Model_Resource_Group_Collection $_customerGroups
 *
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Price _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Price getResource()
 *
 * @method $this setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method $this setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method $this setTaxClassId(int $value)
 * @method $this setPrice(float $value)
 * @method $this setFinalPrice(float $value)
 * @method $this setMinPrice(float $value)
 * @method $this setMaxPrice(float $value)
 * @method $this setTierPrice(float $value)
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
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

    /**
     * @param Mage_Catalog_Model_Product $object
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|null $attribute
     * @return array
     */
    public function createIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute = null)
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

    /**
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return bool
     */
    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getAttributeCode() != 'tier_price') {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    protected function _getIndexableAttributeConditions()
    {
        $conditions = array();
        $conditions['attribute_code'] = 'tier_price';

        return $conditions;
    }
    /**
     * @return float
     */
    public function getPrice()
    {
        return (float) $this->_getData('price');
    }

    /**
     * @return float
     */
    public function getFinalPrice()
    {
        return (float) $this->_getData('final_price');
    }

    /**
     * @return float
     */
    public function getMinPrice()
    {
        return (float) $this->_getData('min_price');
    }

    /**
     * @return float
     */
    public function getMaxPrice()
    {
        return (float) $this->_getData('max_price');
    }

    /**
     * @return float
     */
    public function getTierPrice()
    {
        return (float) $this->_getData('tier_price');
    }
}
