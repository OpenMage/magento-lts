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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * Adminhtml Attribute Set Main Block
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Block_Adminhtml_Attribute_Set_Main extends Mage_Adminhtml_Block_Template
{
    /**
     * Initialize template
     */
    protected function _construct()
    {
        $this->setTemplate('eav/attribute/set/main.phtml');
    }

    /**
     * Prepare Global Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $setId = $this->_getSetId();

        $this->setChild(
            'group_tree',
            $this->getLayout()->createBlock('eav/adminhtml_attribute_set_main_tree_group')
        );

        $this->setChild(
            'edit_set_form',
            $this->getLayout()->createBlock('eav/adminhtml_attribute_set_main_formset')
        );

        $this->setChild(
            'delete_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('eav')->__('Delete Selected Group'),
                'onclick'   => 'editSet.submit();',
                'class'     => 'delete'
            ])
        );

        $this->setChild(
            'add_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('eav')->__('Add New'),
                'onclick'   => 'editSet.addGroup();',
                'class'     => 'add'
            ])
        );

        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('eav')->__('Back'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/') . '\')',
                'class'     => 'back'
            ])
        );

        $this->setChild(
            'reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('eav')->__('Reset'),
                'onclick'   => 'window.location.reload()'
            ])
        );

        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('eav')->__('Save Attribute Set'),
                'onclick'   => 'editSet.save();',
                'class'     => 'save'
            ])
        );

        if (Mage::registry('entity_type')) {
            $deleteConfirmMessage = $this->jsQuoteEscape(Mage::helper('eav')
                ->__('All %s of this set will be deleted! Are you sure you want to delete this attribute set?'));
        } else {
            $deleteConfirmMessage = $this->jsQuoteEscape(Mage::helper('eav')
                ->__('All items of this set will be deleted! Are you sure you want to delete this attribute set?'));
        }
        $deleteUrl = $this->getUrlSecure('*/*/delete', ['id' => $setId]);
        $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('eav')->__('Delete Attribute Set'),
                'onclick'   => 'deleteConfirm(\'' . $deleteConfirmMessage . '\', \'' . $deleteUrl . '\')',
                'class'     => 'delete'
            ])
        );

        $this->setChild(
            'rename_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('eav')->__('New Set Name'),
                'onclick'   => 'editSet.rename()'
            ])
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve Attribute Set Group Tree HTML
     */
    public function getGroupTreeHtml(): string
    {
        return $this->getChildHtml('group_tree');
    }

    /**
     * Retrieve Attribute Set Edit Form HTML
     */
    public function getSetFormHtml(): string
    {
        return $this->getChildHtml('edit_set_form');
    }

    /**
     * Retrieve Block Header Text
     */
    protected function _getHeader(): string
    {
        return Mage::helper('eav')->__("Edit Attribute Set '%s'", $this->_getAttributeSet()->getAttributeSetName());
    }

    /**
     * Retrieve Attribute Set Save URL
     */
    public function getMoveUrl(): string
    {
        return $this->getUrl('*/*/save', ['id' => $this->_getSetId()]);
    }

    /**
     * Retrieve Attribute Set Group Save URL
     */
    public function getGroupUrl(): string
    {
        return $this->getUrl('*/*/save', ['id' => $this->_getSetId()]);
    }

    /**
     * Retrieve Attribute Set Group Tree as JSON format
     *
     * @throws Mage_Core_Exception
     */
    public function getGroupTreeJson(): string
    {
        $items = [];
        $setId = $this->_getSetId();

        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();

        /* @var $entity_type Mage_Eav_Model_Entity_Type */
        $entity_type = Mage::registry('entity_type');

        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
            $item = [];
            $item['text']       = $node->getAttributeGroupName();
            $item['id']         = $node->getAttributeGroupId();
            $item['cls']        = 'folder';
            $item['allowDrop']  = true;
            $item['allowDrag']  = true;

            /** @var Mage_Eav_Model_Entity_Attribute $nodeChildren */
            $nodeChildren = Mage::getResourceModel($entity_type->getEntityAttributeCollection());
            $nodeChildren->setEntityTypeFilter($entity_type->getEntityTypeId())
                         ->setAttributeGroupFilter($node->getId())
                         ->load();

            if ($nodeChildren->getSize() > 0) {
                $item['children'] = [];
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */
                    $attr = [
                        'text'              => $child->getAttributeCode(),
                        'id'                => $child->getAttributeId(),
                        'cls'               => (!$child->getIsUserDefined()) ? 'system-leaf' : 'leaf',
                        'allowDrop'         => false,
                        'allowDrag'         => true,
                        'leaf'              => true,
                        'is_user_defined'   => $child->getIsUserDefined(),
                        'entity_id'         => $child->getEntityAttributeId()
                    ];

                    $item['children'][] = $attr;
                }
            }

            $items[] = $item;
        }

        return Mage::helper('core')->jsonEncode($items);
    }

    /**
     * Retrieve Unused in Attribute Set Attribute Tree as JSON
     */
    public function getAttributeTreeJson(): string
    {
        $items = [];
        $setId = $this->_getSetId();

        /* @var $entity_type Mage_Eav_Model_Entity_Type */
        $entity_type = Mage::registry('entity_type');

        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection */
        $collection = Mage::getResourceModel($entity_type->getEntityAttributeCollection());
        $collection->setEntityTypeFilter($entity_type->getEntityTypeId())
                   ->setAttributeSetFilter($setId)
                   ->load();

        $attributesIds = ['0'];
        foreach ($collection->getItems() as $item) {
            $attributesIds[] = $item->getAttributeId();
        }

        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributes */
        $attributes = Mage::getResourceModel($entity_type->getEntityAttributeCollection());
        $attributes->setEntityTypeFilter($entity_type->getEntityTypeId())
                   ->setAttributesExcludeFilter($attributesIds)
                   ->setOrder('attribute_code', 'asc')
                   ->load();

        foreach ($attributes as $child) {
            $attr = [
                'text'              => $child->getAttributeCode(),
                'id'                => $child->getAttributeId(),
                'cls'               => 'leaf',
                'allowDrop'         => false,
                'allowDrag'         => true,
                'leaf'              => true,
                'is_user_defined'   => $child->getIsUserDefined(),
                'entity_id'         => $child->getEntityId()
            ];

            $items[] = $attr;
        }

        if (count($items) == 0) {
            $items[] = [
                'text'      => Mage::helper('eav')->__('Empty'),
                'id'        => 'empty',
                'cls'       => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            ];
        }

        return Mage::helper('core')->jsonEncode($items);
    }

    /**
     * Retrieve Back Button HTML
     */
    public function getBackButtonHtml(): string
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve Reset Button HTML
     */
    public function getResetButtonHtml(): string
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve Save Button HTML
     */
    public function getSaveButtonHtml(): string
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve Delete Button HTML
     */
    public function getDeleteButtonHtml(): string
    {
        if ($this->getIsCurrentSetDefault()) {
            return '';
        }
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrieve Delete Group Button HTML
     */
    public function getDeleteGroupButton(): string
    {
        return $this->getChildHtml('delete_group_button');
    }

    /**
     * Retrieve Add New Group Button HTML
     */
    public function getAddGroupButton(): string
    {
        return $this->getChildHtml('add_group_button');
    }

    /**
     * Retrieve Rename Button HTML
     */
    public function getRenameButton(): string
    {
        return $this->getChildHtml('rename_button');
    }

    /**
     * Retrieve current Attribute Set object
     */
    protected function _getAttributeSet(): ?Mage_Eav_Model_Entity_Attribute_Set
    {
        return Mage::registry('current_attribute_set');
    }

    /**
     * Retrieve current attribute set Id
     */
    protected function _getSetId(): string
    {
        return $this->_getAttributeSet()->getId();
    }

    /**
     * Check Current Attribute Set is a default
     */
    public function getIsCurrentSetDefault(): bool
    {
        $isDefault = $this->getData('is_current_set_default');
        if (is_null($isDefault)) {
            $defaultSetId = Mage::registry('entity_type')->getDefaultAttributeSetId();
            $isDefault = $this->_getSetId() == $defaultSetId;
            $this->setData('is_current_set_default', $isDefault);
        }
        return $isDefault;
    }

    /**
     * Prepare HTML
     */
    protected function _toHtml(): string
    {
        $type = Mage::registry('entity_type')->getEntityTypeCode();
        Mage::dispatchEvent("adminhtml_{$type}_attribute_set_main_html_before", ['block' => $this]);
        return parent::_toHtml();
    }
}
