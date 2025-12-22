<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Tier Price indexer
 *
 * @package    Mage_CatalogIndex
 *
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Price _getResource()
 * @method int                                            getCustomerGroupId()
 * @method float                                          getFinalPrice()
 * @method float                                          getMaxPrice()
 * @method float                                          getMinPrice()
 * @method float                                          getPrice()
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Price getResource()
 * @method int                                            getTaxClassId()
 * @method float                                          getTierPrice()
 * @method int                                            getWebsiteId()
 * @method $this                                          setCustomerGroupId(int $value)
 * @method $this                                          setEntityId(int $value)
 * @method $this                                          setFinalPrice(float $value)
 * @method $this                                          setMaxPrice(float $value)
 * @method $this                                          setMinPrice(float $value)
 * @method $this                                          setPrice(float $value)
 * @method $this                                          setTaxClassId(int $value)
 * @method $this                                          setTierPrice(float $value)
 * @method $this                                          setWebsiteId(int $value)
 */
class Mage_CatalogIndex_Model_Indexer_Tierprice extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    /**
     * @var Mage_Directory_Model_Currency
     */
    protected $_currencyModel;

    /**
     * @var Mage_Customer_Model_Resource_Group_Collection
     */
    protected $_customerGroups;

    protected $_processChildren = false;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogindex/indexer_price');
        $this->_currencyModel = Mage::getModel('directory/currency');
        $this->_customerGroups = Mage::getModel('customer/group')->getCollection();

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

        $result = [];
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
        return ['attribute_code' => 'tier_price'];
    }
}
