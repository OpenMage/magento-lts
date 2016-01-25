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
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
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

        $itemType = $this->getItemType();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->__('Attribute Set and Item Type')
        ));

        if ( !($targetCountry = $itemType->getTargetCountry()) ) {
            $isoKeys = array_keys($this->_getCountriesArray());
            $targetCountry = isset($isoKeys[0]) ? $isoKeys[0] : null;
        }
        $countrySelect = $fieldset->addField('select_target_country', 'select', array(
            'label'     => $this->__('Target Country'),
            'title'     => $this->__('Target Country'),
            'name'      => 'target_country',
            'required'  => true,
            'options'   => $this->_getCountriesArray(),
            'value'     => $targetCountry,
        ));
        if ($itemType->getTargetCountry()) {
            $countrySelect->setDisabled(true);
        }

        $attributeSetsSelect = $this->getAttributeSetsSelectElement($targetCountry)->setValue($itemType->getAttributeSetId());
        if ($itemType->getAttributeSetId()) {
            $attributeSetsSelect->setDisabled(true);
        }

        $fieldset->addField('attribute_set', 'note', array(
            'label'     => $this->__('Attribute Set'),
            'title'     => $this->__('Attribute Set'),
            'required'  => true,
            'text'      => '<div id="attribute_set_select">' . $attributeSetsSelect->toHtml() . '</div>',
        ));

        $itemTypesSelect = $this->getItemTypesSelectElement($targetCountry)->setValue($itemType->getGbaseItemtype());
        if ($itemType->getGbaseItemtype()) {
            $itemTypesSelect->setDisabled(true);
        }

        $fieldset->addField('itemtype', 'note', array(
            'label'     => $this->__('Google Base Item Type'),
            'title'     => $this->__('Google Base Item Type'),
            'required'  => true,
            'text'      => '<div id="gbase_itemtype_select">' . $itemTypesSelect->toHtml() . '</div>',
        ));

        $attributesBlock = $this->getLayout()
            ->createBlock('googlebase/adminhtml_types_edit_attributes')
            ->setTargetCountry($targetCountry);
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
            'label'     => $this->__('Attributes Mapping'),
            'text'      => '<div id="attributes_details">' . $attributesBlock->toHtml() . '</div>',
        ));

        $form->addValues($itemType->getData());
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getSaveUrl());
        $this->setForm($form);
    }

    public function getAttributeSetsSelectElement($targetCountry)
    {
        $field = new Varien_Data_Form_Element_Select();
        $field->setName('attribute_set_id')
            ->setId('select_attribute_set')
            ->setForm(new Varien_Data_Form())
            ->addClass('required-entry')
            ->setValues($this->_getAttributeSetsArray($targetCountry));
        return $field;
    }

    public function getItemTypesSelectElement($targetCountry)
    {
        $field = new Varien_Data_Form_Element_Select();
        $field->setName('gbase_itemtype')
            ->setId('select_itemtype')
            ->setForm(new Varien_Data_Form())
            ->addClass('required-entry')
            ->setValues($this->_getGbaseItemTypesArray($targetCountry));
        return $field;
    }

    protected function _getCountriesArray()
    {
        $_allowed = Mage::getSingleton('googlebase/config')->getAllowedCountries();
        $result = array();
        foreach ($_allowed as $iso => $info) {
            $result[$iso] = $info['name'];
        }
        return $result;
    }

    protected function _getAttributeSetsArray($targetCountry)
    {
        $entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityType->getId());

        $ids = array();
        $itemType = $this->getItemType();
        if ( !($itemType instanceof Varien_Object && $itemType->getId()) ) {
            $typesCollection = Mage::getResourceModel('googlebase/type_collection')
                ->addCountryFilter($targetCountry)
                ->load();
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

    protected function _getGbaseItemTypesArray($targetCountry)
    {
        $itemTypes = Mage::getModel('googlebase/service_feed')->getItemTypes($targetCountry);
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
