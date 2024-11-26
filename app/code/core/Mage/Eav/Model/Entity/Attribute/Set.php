<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav attribute set model
 *
 * @category   Mage
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Set _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Set getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection getCollection()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection getResourceCollection()
 *
 * @method int getAttributeSetId(string $value)
 * @method string getAttributeSetName()
 * @method $this setAttributeSetName(string $value)
 * @method int getEntityTypeId()
 * @method $this setEntityTypeId(int $value)
 * @method Mage_Eav_Model_Entity_Attribute_Group[] getGroups()
 * @method $this setGroups(Mage_Eav_Model_Entity_Attribute_Group[] $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method Mage_Eav_Model_Entity_Attribute[] getRemoveAttributes()
 * @method $this setRemoveAttributes(Mage_Eav_Model_Entity_Attribute[] $value)
 * @method Mage_Eav_Model_Entity_Attribute_Group[] getRemoveGroups()
 * @method $this setRemoveGroups(Mage_Eav_Model_Entity_Attribute_Group[] $value)
 */
class Mage_Eav_Model_Entity_Attribute_Set extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'eav_entity_attribute_set';

    protected function _construct()
    {
        $this->_init('eav/entity_attribute_set');
    }

    /**
     * Init attribute set from skeleton (another attribute set)
     *
     * @param int $skeletonId
     * @return $this
     */
    public function initFromSkeleton($skeletonId)
    {
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($skeletonId)
            ->load();

        $newGroups = [];
        /** @var Mage_Eav_Model_Entity_Attribute_Group $group */
        foreach ($groups as $group) {
            $newGroup = clone $group;
            $newGroup->setId(null)
                ->setAttributeSetId($this->getId())
                ->setDefaultId($group->getDefaultId());

            $groupAttributesCollection = Mage::getModel('eav/entity_attribute')
                ->getResourceCollection()
                ->setAttributeGroupFilter($group->getId())
                ->load();

            $newAttributes = [];
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            foreach ($groupAttributesCollection as $attribute) {
                $newAttribute = Mage::getModel('eav/entity_attribute')
                    ->setId($attribute->getId())
                    //->setAttributeGroupId($newGroup->getId())
                    ->setAttributeSetId($this->getId())
                    ->setEntityTypeId($this->getEntityTypeId())
                    ->setSortOrder($attribute->getSortOrder());
                $newAttributes[] = $newAttribute;
            }
            $newGroup->setAttributes($newAttributes);
            $newGroups[] = $newGroup;
        }
        $this->setGroups($newGroups);

        return $this;
    }

    /**
     * Collect data for save
     *
     * @param array $data
     * @return $this
     */
    public function organizeData($data)
    {
        $modelGroupArray = [];
        $modelAttributeArray = [];
        $attributeIds = [];
        if ($data['attributes']) {
            $ids = [];
            foreach ($data['attributes'] as $attribute) {
                $ids[] = $attribute[0];
            }
            $attributeIds = Mage::getResourceSingleton('eav/entity_attribute')
                ->getValidAttributeIds($ids);
        }
        if ($data['groups']) {
            foreach ($data['groups'] as $group) {
                $modelGroup = Mage::getModel('eav/entity_attribute_group');
                $modelGroup->setId(is_numeric($group[0]) && $group[0] > 0 ? $group[0] : null)
                    ->setAttributeGroupName($group[1])
                    ->setAttributeSetId($this->getId())
                    ->setSortOrder($group[2]);

                if ($data['attributes']) {
                    foreach ($data['attributes'] as $attribute) {
                        if ($attribute[1] == $group[0] && in_array($attribute[0], $attributeIds)) {
                            $modelAttribute = Mage::getModel('eav/entity_attribute');
                            $modelAttribute->setId($attribute[0])
                                ->setAttributeGroupId($attribute[1])
                                ->setAttributeSetId($this->getId())
                                ->setEntityTypeId($this->getEntityTypeId())
                                ->setSortOrder($attribute[2]);
                            $modelAttributeArray[] = $modelAttribute;
                        }
                    }
                    $modelGroup->setAttributes($modelAttributeArray);
                    $modelAttributeArray = [];
                }
                $modelGroupArray[] = $modelGroup;
            }
            $this->setGroups($modelGroupArray);
        }

        if ($data['not_attributes']) {
            $modelAttributeArray = [];
            foreach ($data['not_attributes'] as $attributeId) {
                $modelAttribute = Mage::getModel('eav/entity_attribute');

                $modelAttribute->setEntityAttributeId($attributeId);
                $modelAttributeArray[] = $modelAttribute;
            }
            $this->setRemoveAttributes($modelAttributeArray);
        }

        if ($data['removeGroups']) {
            $modelGroupArray = [];
            foreach ($data['removeGroups'] as $groupId) {
                $modelGroup = Mage::getModel('eav/entity_attribute_group');
                $modelGroup->setId($groupId);

                $modelGroupArray[] = $modelGroup;
            }
            $this->setRemoveGroups($modelGroupArray);
        }
        $this->setAttributeSetName($data['attribute_set_name'])
            ->setEntityTypeId($this->getEntityTypeId());

        return $this;
    }

    /**
     * Validate attribute set name
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validate()
    {
        if (!$this->_getResource()->validate($this, $this->getAttributeSetName())) {
            throw Mage::exception(
                'Mage_Eav',
                Mage::helper('eav')->__('Attribute set with the "%s" name already exists.', $this->getAttributeSetName())
            );
        }

        return true;
    }

    /**
     * Add set info to attributes
     *
     * @param string|Mage_Eav_Model_Entity_Type $entityType
     * @param int $setId
     * @return $this
     */
    public function addSetInfo($entityType, array $attributes, $setId = null)
    {
        $attributeIds   = [];
        $config         = Mage::getSingleton('eav/config');
        $entityType     = $config->getEntityType($entityType);
        foreach ($attributes as $attribute) {
            $attribute = $config->getAttribute($entityType, $attribute);
            if ($setId && is_array($attribute->getAttributeSetInfo($setId))) {
                continue;
            }
            if (!$attribute->getAttributeId()) {
                continue;
            }
            $attributeIds[] = $attribute->getAttributeId();
        }

        if ($attributeIds) {
            $setInfo = $this->_getResource()
                ->getSetInfo($attributeIds, $setId);

            foreach ($attributes as $attribute) {
                $attribute = $config->getAttribute($entityType, $attribute);
                if (!$attribute->getAttributeId()) {
                    continue;
                }
                if (!in_array($attribute->getAttributeId(), $attributeIds)) {
                    continue;
                }
                if (is_numeric($setId)) {
                    $attributeSetInfo = $attribute->getAttributeSetInfo();
                    if (!is_array($attributeSetInfo)) {
                        $attributeSetInfo = [];
                    }
                    if (isset($setInfo[$attribute->getAttributeId()][$setId])) {
                        $attributeSetInfo[$setId] = $setInfo[$attribute->getAttributeId()][$setId];
                    }
                    $attribute->setAttributeSetInfo($attributeSetInfo);
                } else {
                    if (isset($setInfo[$attribute->getAttributeId()])) {
                        $attribute->setAttributeSetInfo($setInfo[$attribute->getAttributeId()]);
                    } else {
                        $attribute->setAttributeSetInfo([]);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Return default Group Id for current or defined Attribute Set
     *
     * @param int $setId
     * @return int|null
     */
    public function getDefaultGroupId($setId = null)
    {
        if ($setId === null) {
            $setId = $this->getId();
        }
        if ($setId) {
            $groupId = $this->_getResource()->getDefaultGroupId($setId);
        } else {
            $groupId = null;
        }
        return $groupId;
    }
}
