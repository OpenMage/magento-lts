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
 * Catalog indexer price processor
 *
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Price _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Price getResource()
 * @method Mage_CatalogIndex_Model_Indexer_Price setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method Mage_CatalogIndex_Model_Indexer_Price setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method Mage_CatalogIndex_Model_Indexer_Price setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method Mage_CatalogIndex_Model_Indexer_Price setTaxClassId(int $value)
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
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

    /**
     * @param Mage_Catalog_Model_Product $object
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|null $attribute
     * @return array|mixed
     */
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
     * @return array|string
     */
    protected function _getIndexableAttributeConditions()
    {
        $conditions = "frontend_input = 'price' AND attribute_code <> 'price'";
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

    /**
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->setData('price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setFinalPrice($value)
    {
        return $this->setData('final_price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setMinPrice($value)
    {
        return $this->setData('min_price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setMaxPrice($value)
    {
        return $this->setData('max_price', (float) $value);
    }

    /**
     * @return $this
     */
    public function setTierPrice($value)
    {
        return $this->setData('tier_price', (float) $value);
    }
}
