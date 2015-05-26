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
 * @package     Mage_Widget
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget Instance Main tab block
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Internal constructor
     *
     */
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
     * @return true
     */
    public function canShowTab()
    {
        return $this->getWidgetInstance()->isCompleteToCreate();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Getter
     *
     * @return Widget_Model_Widget_Instance
     */
    public function getWidgetInstance()
    {
        return Mage::registry('current_widget_instance');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        $widgetInstance = $this->getWidgetInstance();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('widget')->__('Frontend Properties'))
        );

        if ($widgetInstance->getId()) {
            $fieldset->addField('instance_id', 'hidden', array(
                'name' => 'isntance_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $fieldset->addField('type', 'select', array(
            'name'  => 'type',
            'label' => Mage::helper('widget')->__('Type'),
            'title' => Mage::helper('widget')->__('Type'),
            'class' => '',
            'values' => $this->getTypesOptionsArray(),
            'disabled' => true
        ));

        $fieldset->addField('package_theme', 'select', array(
            'name'  => 'package_theme',
            'label' => Mage::helper('widget')->__('Design Package/Theme'),
            'title' => Mage::helper('widget')->__('Design Package/Theme'),
            'required' => false,
            'values'   => $this->getPackegeThemeOptionsArray(),
            'disabled' => true
        ));

        $fieldset->addField('title', 'text', array(
            'name'  => 'title',
            'label' => Mage::helper('widget')->__('Widget Instance Title'),
            'title' => Mage::helper('widget')->__('Widget Instance Title'),
            'class' => '',
            'required' => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_ids', 'multiselect', array(
                'name'      => 'store_ids[]',
                'label'     => Mage::helper('widget')->__('Assign to Store Views'),
                'title'     => Mage::helper('widget')->__('Assign to Store Views'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('sort_order', 'text', array(
            'name'  => 'sort_order',
            'label' => Mage::helper('widget')->__('Sort Order'),
            'title' => Mage::helper('widget')->__('Sort Order'),
            'class' => '',
            'required' => false,
            'note' => Mage::helper('widget')->__('Sort Order of widget instances in the same block reference')
        ));

        /* @var $layoutBlock Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout */
        $layoutBlock = $this->getLayout()
            ->createBlock('widget/adminhtml_widget_instance_edit_tab_main_layout')
            ->setWidgetInstance($widgetInstance);
        $fieldset = $form->addFieldset('layout_updates_fieldset',
            array('legend' => Mage::helper('widget')->__('Layout Updates'))
        );
        $fieldset->addField('layout_updates', 'note', array(
        ));
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
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getWidgetInstance()->getData());
        return parent::_initFormValues();
    }
}
