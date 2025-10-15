<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 */
abstract class Mage_Eav_Model_Convert_Parser_Abstract extends Mage_Dataflow_Model_Convert_Parser_Abstract
{
    protected $_storesById;

    protected $_attributeSetsById;

    protected $_attributeSetsByName;

    /**
     * @param array $stores
     * @return array|false
     */
    public function getStoreIds($stores)
    {
        if (empty($stores)) {
            $storeIds = [0];
        } else {
            $storeIds = [];
            foreach (explode(',', $stores) as $store) {
                if (is_numeric($store)) {
                    $storeIds[] = $store;
                } else {
                    $storeNode = Mage::getConfig()->getNode('stores/' . $store);
                    if (!$storeNode) {
                        return false;
                    }

                    $storeIds[] = (int) $storeNode->system->store->id;
                }
            }
        }

        return $storeIds;
    }

    /**
     * @param int $storeId
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStoreCode($storeId)
    {
        return Mage::app()->getStore($storeId ? $storeId : 0)->getCode();
    }

    /**
     * @param int $entityTypeId
     * @return $this
     */
    public function loadAttributeSets($entityTypeId)
    {
        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load();
        $this->_attributeSetsById = [];
        $this->_attributeSetsByName = [];
        /**
         * @var int $id
         * @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet
         */
        foreach ($attributeSetCollection as $id => $attributeSet) {
            $name = $attributeSet->getAttributeSetName();
            $this->_attributeSetsById[$id] = $name;
            $this->_attributeSetsByName[$name] = $id;
        }

        return $this;
    }

    /**
     * @param int $entityTypeId
     * @param int $id
     * @return bool
     */
    public function getAttributeSetName($entityTypeId, $id)
    {
        if (!$this->_attributeSetsById) {
            $this->loadAttributeSets($entityTypeId);
        }

        return $this->_attributeSetsById[$id] ?? false;
    }

    /**
     * @param int $entityTypeId
     * @param string $name
     * @return bool
     */
    public function getAttributeSetId($entityTypeId, $name)
    {
        if (!$this->_attributeSetsByName) {
            $this->loadAttributeSets($entityTypeId);
        }

        return $this->_attributeSetsByName[$name] ?? false;
    }

    /**
     * @param string $value
     * @return string|null
     */
    public function getSourceOptionId(Mage_Eav_Model_Entity_Attribute_Source_Interface $source, $value)
    {
        foreach ($source->getAllOptions() as $option) {
            if (strcasecmp($option['label'], $value) == 0) {
                return $option['value'];
            }
        }

        return null;
    }
}
