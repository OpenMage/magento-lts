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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('catalog/product/attribute/set/main.phtml');
    }

    protected function _prepareLayout()
    {
        $setId = $this->_getSetId();

        $this->setChild('group_tree',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_tree_group')
        );

        $this->setChild('edit_set_form',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formset')
        );

        $this->setChild('delete_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Delete Selected Group'),
                    'onclick'   => 'editSet.submit();',
                    'class' => 'delete'
        )));

        $this->setChild('add_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add New'),
                    'onclick'   => 'editSet.addGroup();',
                    'class' => 'add'
        )));

        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
                    'class' => 'back'
        )));

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Reset'),
                    'onclick'   => 'window.location.reload()'
        )));

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Save Attribute Set'),
                    'onclick'   => 'editSet.save();',
                    'class' => 'save'
        )));

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Delete Attribute Set'),
                    'onclick'   => 'deleteConfirm(\''. $this->jsQuoteEscape(Mage::helper('catalog')->__('All products of this set will be deleted! Are you sure you want to delete this attribute set?')) . '\', \'' . $this->getUrl('*/*/delete', array('id' => $setId)) . '\')',
                    'class' => 'delete'
        )));

        $this->setChild('rename_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('New Set Name'),
                    'onclick'   => 'editSet.rename()'
                ))
        );
        return parent::_prepareLayout();
    }

    public function getGroupTreeHtml()
    {
        return $this->getChildHtml('group_tree');
    }

    public function getSetFormHtml()
    {
        return $this->getChildHtml('edit_set_form');
    }

    protected function _getHeader()
    {
        return Mage::helper('catalog')->__("Edit Attribute Set '%s'", $this->_getSetData()->getAttributeSetName());
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/catalog_product_set/save', array('id' => $this->_getSetId()));
    }

    public function getGroupUrl()
    {
        return $this->getUrl('*/catalog_product_group/save', array('id' => $this->_getSetId()));
    }

    public function getGroupTreeJson()
    {
        $setId = $this->_getSetId();

        $groups = Mage::getModel('eav/entity_attribute_group')
                    ->getResourceCollection()
                    ->setAttributeSetFilter($setId)
                    ->load();

        $items = array();
        foreach( $groups as $node ) {
            $item = array();
            $item['text']= $node->getAttributeGroupName();
            $item['id']  = $node->getAttributeGroupId();
            $item['cls'] = 'folder';
            $item['allowDrop'] = true;
            $item['allowDrag'] = true;

            $nodeChildren = Mage::getModel('eav/entity_attribute')
                                ->getResourceCollection()
                                ->setAttributeGroupFilter($node->getAttributeGroupId())
                                ->addVisibleFilter()
                                /**
                                 * TODO: issue #5126
                                 * @see Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
                                 */
                                ->checkConfigurableProducts()
                                ->load();

            if ( $nodeChildren->getSize() > 0 ) {
                $item['children'] = array();
                foreach( $nodeChildren->getItems() as $child ) {
                    $tmpArr = array();
                    $tmpArr['text'] = $child->getAttributeCode();
                    $tmpArr['id']  = $child->getAttributeId();
                    $tmpArr['cls'] = ( $child->getIsUserDefined() == 0 ) ? 'system-leaf' : 'leaf';
                    $tmpArr['allowDrop'] = false;
                    $tmpArr['allowDrag'] = true;
                    $tmpArr['leaf'] = true;
                    $tmpArr['is_user_defined'] = $child->getIsUserDefined();
                    // TODO: issue #5126. Template already has reuqired changes
                    $tmpArr['is_used_in_configurable'] = false; // (bool)$child->getIsUsedInConfigurable(); // TODO: issue #5126
                    $tmpArr['entity_id'] = $child->getEntityAttributeId();

                    $item['children'][] = $tmpArr;
                }
            }

            $items[] = $item;
        }

        return Zend_Json::encode($items);
    }

    public function getAttributeTreeJson()
    {
        $setId = $this->_getSetId();

        $attributesIdsObj = Mage::getModel('eav/entity_attribute')
                            ->getResourceCollection()
                            ->setAttributeSetFilter($setId)
                            ->load();
        $attributesIds = array('0');
        foreach( $attributesIdsObj->getItems() as $item ) {
            $attributesIds[] = $item->getAttributeId();
        }
        $attributes = Mage::getModel('eav/entity_attribute')
                            ->getResourceCollection()
                            ->setEntityTypeFilter(Mage::registry('entityType'))
                            ->setAttributesExcludeFilter($attributesIds)
                            ->addVisibleFilter()
                            ->load();

        $items = array();
        foreach( $attributes as $node ) {
            $item = array();
            $item['text']= $node->getAttributeCode();
            $item['id']  = $node->getAttributeId();
            $item['cls'] = 'leaf';
            $item['allowDrop'] = false;
            $item['allowDrag'] = true;
            $item['leaf'] = true;
            $item['is_user_defined'] = $node->getIsUserDefined();
            $item['is_used_in_configurable'] = false;

            $items[] = $item;
        }

        if( count($items) == 0 ) {
            $items[] = array(
                'text' => Mage::helper('catalog')->__('Empty'),
                'id' => 'empty',
                'cls' => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            );
        }

        return Zend_Json::encode($items);
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getDeleteButtonHtml()
    {
        if ($this->getIsCurrentSetDefault()) {
            return '';
        }
        return $this->getChildHtml('delete_button');
    }

    public function getDeleteGroupButton()
    {
        return $this->getChildHtml('delete_group_button');
    }

    public function getAddGroupButton()
    {
        return $this->getChildHtml('add_group_button');
    }

    public function getRenameButton()
    {
        return $this->getChildHtml('rename_button');
    }

    protected function _getSetId()
    {
        return Mage::registry('current_attribute_set')->getId();
    }

    public function getIsCurrentSetDefault()
    {
        $isDefault = $this->getData('is_current_set_default');
        if (is_null($isDefault)) {
            $defaultSetId = Mage::getModel('eav/entity_type')
                ->load(Mage::registry('entityType'))
                ->getDefaultAttributeSetId();
            $isDefault = $this->_getSetId() == $defaultSetId;
            $this->setData('is_current_set_default', $isDefault);
        }
        return $isDefault;
    }

    protected function _getSetData()
    {
        return Mage::getModel('eav/entity_attribute_set')->load( $this->_getSetId() );
    }
}