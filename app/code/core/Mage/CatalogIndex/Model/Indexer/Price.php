<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog indexer price processor
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Price _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Price getResource()
 * @method $this setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method $this setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method $this setTaxClassId(int $value)
 * @method float getPrice()
 * @method $this setPrice(float $value)
 * @method float getFinalPrice()
 * @method $this setFinalPrice(float $value)
 * @method float getMinPrice()
 * @method $this setMinPrice(float $value)
 * @method float getMaxPrice()
 * @method $this setMaxPrice(float $value)
 * @method float getTierPrice()
 * @method $this setTierPrice(float $value)
 */
class Mage_CatalogIndex_Model_Indexer_Price extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    protected $_customerGroups = [];
    protected $_processChildrenForConfigurable = false;

    protected function _construct()
    {
        $this->_init('catalogindex/indexer_price');
        $this->_customerGroups = Mage::getModel('customer/group')->getCollection();
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|null $attribute
     * @return array
     */
    public function createIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute = null)
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
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
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
