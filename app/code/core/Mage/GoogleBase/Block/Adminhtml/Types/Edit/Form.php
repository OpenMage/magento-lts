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
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Google Base types mapping form block
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_GoogleBase_Block_Adminhtml_Types_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $itemType = Mage::registry('current_item_type');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->__('Attribute Set and Item Type')
        ));

        $attributeSelect = $fieldset->addField('select_attribute_set', 'select', array(
            'label'     => $this->__('Attribute Set'),
            'title'     => $this->__('Attribute Set'),
            'name'      => 'attribute_set_id',
            'required'  => true,
            'options'   => $this->_getAttributeSetsArray(),
            'value'     => $itemType->getAttributeSetId(),
        ));
        if ($itemType->getAttributeSetId()) {
            $attributeSelect->setValue($itemType->getAttributeSetId())
                ->setDisabled(true);
        }

        $itemTypeSelect = $fieldset->addField('select_itemtype', 'select', array(
            'label'     => $this->__('Google Base Item Type'),
            'title'     => $this->__('Google Base Item Type'),
            'name'      => 'gbase_itemtype',
            'required'  => true,
            'options'   => $this->_getGbaseItemTypesArray(),
            'value'     => $itemType->getGbaseItemtype(),
        ));
        if ($itemType->getGbaseItemtype()) {
            $itemTypeSelect->setValue($itemType->getGbaseItemtype())
                ->setDisabled(true);
        }

        $attributesBlock = $this->getLayout()->createBlock('googlebase/adminhtml_types_edit_attributes');
        if ($itemType->getId()) {
            $attributesBlock->setAttributeSetId($itemType->getAttributeSetId())
                ->setGbaseItemtype($itemType->getGbaseItemtype())
                ->setAttributeSetSelected(true);

        }

        $attributes = Mage::registry('attributes');
        if (is_array($attributes) && count($attributes) > 0) {
            $attributesBlock->setAttributesData($attributes);
        }

        $fieldset->addField('attributes_box', 'note', array(
            'label'     => $this->__('Attributes mapping'),
            'text'      => '<div id="attributes_details">' . $attributesBlock->toHtml() . '</div>',
        ));

        $form->addValues($itemType->getData());
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getSaveUrl());
        $this->setForm($form);
    }

    protected function _getAttributeSetsArray()
    {
        $entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityType->getId());

        $ids = array();
        $itemType = Mage::registry('current_item_type');
        if (!$itemType->getId()) {
            $typesCollection = Mage::getResourceModel('googlebase/type_collection')->load();
            foreach ($typesCollection as $type) {
                $ids[] = $type->getAttributeSetId();
            }
        }

        $result = array('' => '');
        foreach ($collection as $attributeSet) {
            if (!in_array($attributeSet->getId(), $ids)) {
                $result[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
            }
        }
        return $result;
    }
    protected function _getGbaseItemTypesArray()
    {
        $itemTypes = Mage::getModel('googlebase/service_feed')->getItemTypes();
        $result = array('' => '');
        foreach ($itemTypes as $type) {
            $result[$type->getId()] = $type->getName();
        }
        return $result;
    }

    public function getItemType()
    {
        return Mage::registry('current_item_type');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('type_id' => $this->getItemType()->getId()));
    }
}