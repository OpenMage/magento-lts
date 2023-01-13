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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
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
                    $storeIds[] = (int)$storeNode->system->store->id;
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
     * @param Mage_Eav_Model_Entity_Attribute_Source_Interface $source
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
