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
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget Instance Main tab block
 *
 * @category   Mage
 * @package    Mage_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('widget')->__('Frontend Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('widget')->__('Frontend Properties');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return $this->getWidgetInstance()->isCompleteToCreate();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Getter
     *
     * @return Mage_Widget_Model_Widget_Instance
     */
    public function getWidgetInstance()
    {
        return Mage::registry('current_widget_instance');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $widgetInstance = $this->getWidgetInstance();
        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ]);

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => Mage::helper('widget')->__('Frontend Properties')]
        );

        if ($widgetInstance->getId()) {
            $fieldset->addField('instance_id', 'hidden', [
                'name' => 'isntance_id',
            ]);
        }

        $this->_addElementTypes($fieldset);

        $fieldset->addField('type', 'select', [
            'name'  => 'type',
            'label' => Mage::helper('widget')->__('Type'),
            'title' => Mage::helper('widget')->__('Type'),
            'class' => '',
            'values' => $this->getTypesOptionsArray(),
            'disabled' => true
        ]);

        $fieldset->addField('package_theme', 'select', [
            'name'  => 'package_theme',
            'label' => Mage::helper('widget')->__('Design Package/Theme'),
            'title' => Mage::helper('widget')->__('Design Package/Theme'),
            'required' => false,
            'values'   => $this->getPackegeThemeOptionsArray(),
            'disabled' => true
        ]);

        $fieldset->addField('title', 'text', [
            'name'  => 'title',
            'label' => Mage::helper('widget')->__('Widget Instance Title'),
            'title' => Mage::helper('widget')->__('Widget Instance Title'),
            'class' => '',
            'required' => true,
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_ids', 'multiselect', [
                'name'      => 'store_ids[]',
                'label'     => Mage::helper('widget')->__('Assign to Store Views'),
                'title'     => Mage::helper('widget')->__('Assign to Store Views'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ]);
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('sort_order', 'text', [
            'name'  => 'sort_order',
            'label' => Mage::helper('widget')->__('Sort Order'),
            'title' => Mage::helper('widget')->__('Sort Order'),
            'class' => '',
            'required' => false,
            'note' => Mage::helper('widget')->__('Sort Order of widget instances in the same block reference')
        ]);

        $layoutBlock = $this->getLayout()
            ->createBlock('widget/adminhtml_widget_instance_edit_tab_main_layout')
            ->setWidgetInstance($widgetInstance);
        $fieldset = $form->addFieldset(
            'layout_updates_fieldset',
            ['legend' => Mage::helper('widget')->__('Layout Updates')]
        );
        $fieldset->addField('layout_updates', 'note', [
        ]);
        $form->getElement('layout_updates_fieldset')->setRenderer($layoutBlock);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve array (widget_type => widget_name) of available widgets
     *
     * @return array
     */
    public function getTypesOptionsArray()
    {
        return $this->getWidgetInstance()->getWidgetsOptionArray();
    }

    /**
     * Retrieve design package/theme options array
     *
     * @return array
     */
    public function getPackegeThemeOptionsArray()
    {
        return Mage::getModel('core/design_source_design')
            ->setIsFullLabel(true)->getAllOptions(true);
    }

    /**
     * Initialize form fileds values
     *
     * @inheritDoc
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getWidgetInstance()->getData());
        return parent::_initFormValues();
    }
}
