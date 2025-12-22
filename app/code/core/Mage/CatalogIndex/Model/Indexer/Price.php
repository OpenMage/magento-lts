<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Catalog indexer price processor
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
class Mage_CatalogIndex_Model_Indexer_Price extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    protected $_customerGroups = [];

    protected $_processChildrenForConfigurable = false;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogindex/indexer_price');
        $this->_customerGroups = Mage::getModel('customer/group')->getCollection();
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

        if ($attribute->getAttributeCode() == 'price') {
            $result = [];
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

    /**
     * @return bool
     */
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

    /**
     * @return string
     */
    protected function _getIndexableAttributeConditions()
    {
        return "frontend_input = 'price' AND attribute_code <> 'price'";
    }
}
