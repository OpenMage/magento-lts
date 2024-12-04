<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Catalog Attribute Set Main Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main extends Mage_Adminhtml_Block_Template
{
    public const BLOCK_EDIT_SET_FORM    = 'edit_set_form';
    public const BLOCK_GROUP_TREE       = 'group_tree';

    public const BUTTON_ADD_GROUP       = 'add_group_button';
    public const BUTTON_DELETE_GROUP    = 'delete_group_button';
    public const BUTTON_RENAME          = 'rename_button';

    protected $_template = 'catalog/product/attribute/set/main.phtml';

    /**
     * Prepare Global Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $setId = $this->_getSetId();

        $this->setChild(
            self::BLOCK_GROUP_TREE,
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_tree_group')
        );

        $this->setChild(
            self::BLOCK_EDIT_SET_FORM,
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formset')
        );

        $this->setChild(
            'delete_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('catalog')->__('Delete Selected Group'),
                'onclick'   => 'editSet.submit();',
                'class'     => 'delete'
            ])
        );

        $this->setChild(
            'add_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('catalog')->__('Add New'),
                'onclick'   => 'editSet.addGroup();',
                'class'     => 'add'
            ])
        );

        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('catalog')->__('Back'),
                'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/')),
                'class'     => 'back'
            ])
        );

        $this->setChild(
            'reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('catalog')->__('Reset'),
                'onclick'   => 'window.location.reload()'
            ])
        );

        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('catalog')->__('Save Attribute Set'),
                'onclick'   => 'editSet.save();',
                'class'     => 'save'
            ])
        );

        $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('catalog')->__('Delete Attribute Set'),
                'onclick'   => Mage::helper('core/js')->getDeleteConfirmJs(
                    $this->getUrlSecure('*/*/delete', ['id' => $setId]),
                    Mage::helper('catalog')->__('All products of this set will be deleted! Are you sure you want to delete this attribute set?')
                ),
                'class'     => 'delete'
            ])
        );

        $this->setChild(
            'rename_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('catalog')->__('New Set Name'),
                'onclick'   => 'editSet.rename()'
            ])
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve Attribute Set Group Tree HTML
     *
     * @return string
     */
    public function getGroupTreeHtml()
    {
        return $this->getChildHtml(self::BLOCK_GROUP_TREE);
    }

    /**
     * Retrieve Attribute Set Edit Form HTML
     *
     * @return string
     */
    public function getSetFormHtml()
    {
        return $this->getChildHtml(self::BLOCK_EDIT_SET_FORM);
    }

    /**
     * Retrieve Block Header Text
     *
     * @return string
     */
    protected function _getHeader()
    {
        return Mage::helper('catalog')->__("Edit Attribute Set '%s'", $this->_getAttributeSet()->getAttributeSetName());
    }

    /**
     * Retrieve Attribute Set Save URL
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/catalog_product_set/save', ['id' => $this->_getSetId()]);
    }

    /**
     * Retrieve Attribute Set Group Save URL
     *
     * @return string
     */
    public function getGroupUrl()
    {
        return $this->getUrl('*/catalog_product_group/save', ['id' => $this->_getSetId()]);
    }

    /**
     * Retrieve Attribute Set Group Tree as JSON format
     *
     * @return string
     */
    public function getGroupTreeJson()
    {
        $items = [];
        $setId = $this->_getSetId();

        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $groups */
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();

        $configurable = Mage::getResourceModel('catalog/product_type_configurable_attribute')
            ->getUsedAttributes($setId);

        /** @var Mage_Eav_Model_Entity_Attribute_Group $node */
        foreach ($groups as $node) {
            $item = [];
            $item['text']       = $node->getAttributeGroupName();
            $item['id']         = $node->getAttributeGroupId();
            $item['cls']        = 'folder';
            $item['allowDrop']  = true;
            $item['allowDrag']  = true;

            $nodeChildren = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()
                ->checkConfigurableProducts()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                $item['children'] = [];
                foreach ($nodeChildren->getItems() as $child) {
                    /** @var Mage_Eav_Model_Entity_Attribute $child */
                    $attr = [
                        'text'              => $child->getAttributeCode(),
                        'id'                => $child->getAttributeId(),
                        'cls'               => (!$child->getIsUserDefined()) ? 'system-leaf' : 'leaf',
                        'allowDrop'         => false,
                        'allowDrag'         => true,
                        'leaf'              => true,
                        'is_user_defined'   => $child->getIsUserDefined(),
                        'is_configurable'   => (int)in_array($child->getAttributeId(), $configurable),
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
     *
     * @return string
     */
    public function getAttributeTreeJson()
    {
        $items = [];
        $setId = $this->_getSetId();

        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->setAttributeSetFilter($setId)
            ->load();

        $attributesIds = ['0'];
        /** @var Mage_Eav_Model_Entity_Attribute $item */
        foreach ($collection->getItems() as $item) {
            $attributesIds[] = $item->getAttributeId();
        }

        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->setAttributesExcludeFilter($attributesIds)
            ->addVisibleFilter()
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
                'is_configurable'   => false,
                'entity_id'         => $child->getEntityId()
            ];

            $items[] = $attr;
        }

        if (count($items) == 0) {
            $items[] = [
                'text'      => Mage::helper('catalog')->__('Empty'),
                'id'        => 'empty',
                'cls'       => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            ];
        }

        return Mage::helper('core')->jsonEncode($items);
    }

    /**
     * @inheritDoc
     */
    public function getDeleteButtonHtml()
    {
        if ($this->getIsCurrentSetDefault()) {
            return '';
        }
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrieve Delete Group Button HTML
     *
     * @return string
     */
    public function getDeleteGroupButton()
    {
        return $this->getChildHtml(self::BUTTON_DELETE_GROUP);
    }

    /**
     * Retrieve Add New Group Button HTML
     *
     * @return string
     */
    public function getAddGroupButton()
    {
        return $this->getChildHtml(self::BUTTON_ADD_GROUP);
    }

    /**
     * Retrieve Rename Button HTML
     *
     * @return string
     */
    public function getRenameButton()
    {
        return $this->getChildHtml(self::BUTTON_RENAME);
    }

    /**
     * Retrieve current Attribute Set object
     *
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    protected function _getAttributeSet()
    {
        return Mage::registry('current_attribute_set');
    }

    /**
     * Retrieve current attribute set Id
     *
     * @return int
     */
    protected function _getSetId()
    {
        return $this->_getAttributeSet()->getId();
    }

    /**
     * Check Current Attribute Set is a default
     *
     * @return bool
     */
    public function getIsCurrentSetDefault()
    {
        $isDefault = $this->getData('is_current_set_default');
        if (is_null($isDefault)) {
            $defaultSetId = Mage::getSingleton('eav/config')
                ->getEntityType(Mage::registry('entityType'))
                ->getDefaultAttributeSetId();
            $isDefault = $this->_getSetId() == $defaultSetId;
            $this->setData('is_current_set_default', $isDefault);
        }
        return $isDefault;
    }

    /**
     * Retrieve current Attribute Set object
     *
     * @deprecated use _getAttributeSet
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    protected function _getSetData()
    {
        return $this->_getAttributeSet();
    }

    /**
     * Prepare HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_catalog_product_attribute_set_main_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
