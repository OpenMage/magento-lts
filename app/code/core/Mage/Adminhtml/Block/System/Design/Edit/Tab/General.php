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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_Block_System_Design_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('core')->__('General Settings')));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => Mage::helper('core')->__('Store'),
                'title'    => Mage::helper('core')->__('Store'),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                'name'     => 'store_id',
                'required' => true,
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId(),
            ));
        }

        $fieldset->addField('design', 'select', array(
            'label'    => Mage::helper('core')->__('Custom Design'),
            'title'    => Mage::helper('core')->__('Custom Design'),
            'values'   => Mage::getSingleton('core/design_source_design')->getAllOptions(),
            'name'     => 'design',
            'required' => true,
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('date_from', 'date', array(
            'label'    => Mage::helper('core')->__('Date From'),
            'title'    => Mage::helper('core')->__('Date From'),
            'name'     => 'date_from',
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format'   => $dateFormatIso,
            //'required' => true,
        ));
        $fieldset->addField('date_to', 'date', array(
            'label'    => Mage::helper('core')->__('Date To'),
            'title'    => Mage::helper('core')->__('Date To'),
            'name'     => 'date_to',
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format'   => $dateFormatIso,
            //'required' => true,
        ));

        $formData = Mage::getSingleton('adminhtml/session')->getDesignData(true);
        if (!$formData){
            $formData = Mage::registry('design')->getData();
        } else {
            $formData = $formData['design'];
        }

        $form->addValues($formData);
        $form->setFieldNameSuffix('design');
        $this->setForm($form);
    }

}
