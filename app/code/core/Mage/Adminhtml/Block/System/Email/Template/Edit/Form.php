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

/**
 * Adminhtml system template edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Email_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare layout.
     * Add files to use dialog windows
     *
     * @return Mage_Adminhtml_Block_System_Email_Template_Edit_Form
     */
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addItem('js', 'prototype/window.js')
                ->addItem('js_css', 'prototype/windows/themes/default.css')
                ->addCss('lib/prototype/windows/themes/magento.css')
                ->addItem('js', 'mage/adminhtml/variables.js');
        }
        return parent::_prepareLayout();
    }

    /**
     * Add fields to form and create template info form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('adminhtml')->__('Template Information'),
            'class' => 'fieldset-wide'
        ));

        $templateId = $this->getEmailTemplate()->getId();
        if ($templateId) {
            $fieldset->addField('used_currently_for', 'label', array(
                'label' => Mage::helper('adminhtml')->__('Used Currently For'),
                'container_id' => 'used_currently_for',
                'after_element_html' =>
                    '<script type="text/javascript">' .
                    (!$this->getEmailTemplate()->getSystemConfigPathsWhereUsedCurrently()
                        ? '$(\'' . 'used_currently_for' . '\').hide(); ' : '') .
                    '</script>',
            ));
        }

        if (!$templateId) {
            $fieldset->addField('used_default_for', 'label', array(
                'label' => Mage::helper('adminhtml')->__('Used as Default For'),
                'container_id' => 'used_default_for',
                'after_element_html' =>
                    '<script type="text/javascript">' .
                    (!(bool)$this->getEmailTemplate()->getOrigTemplateCode()
                        ? '$(\'' . 'used_default_for' . '\').hide(); ' : '') .
                    '</script>',
            ));
        }

        $fieldset->addField('template_code', 'text', array(
            'name'=>'template_code',
            'label' => Mage::helper('adminhtml')->__('Template Name'),
            'required' => true

        ));

        $fieldset->addField('template_subject', 'text', array(
            'name'=>'template_subject',
            'label' => Mage::helper('adminhtml')->__('Template Subject'),
            'required' => true
        ));

        $fieldset->addField('orig_template_variables', 'hidden', array(
            'name' => 'orig_template_variables',
        ));

        $fieldset->addField('variables', 'hidden', array(
            'name' => 'variables',
            'value' => Zend_Json::encode($this->getVariables())
        ));

        $fieldset->addField('template_variables', 'hidden', array(
            'name' => 'template_variables',
        ));

        $insertVariableButton = $this->getLayout()
            ->createBlock('adminhtml/widget_button', '', array(
                'type' => 'button',
                'label' => Mage::helper('adminhtml')->__('Insert Variable...'),
                'onclick' => 'templateControl.openVariableChooser();return false;'
            ));

        $fieldset->addField('insert_variable', 'note', array(
            'text' => $insertVariableButton->toHtml()
        ));

        $fieldset->addField('template_text', 'textarea', array(
            'name'=>'template_text',
            'label' => Mage::helper('adminhtml')->__('Template Content'),
            'title' => Mage::helper('adminhtml')->__('Template Content'),
            'required' => true,
            'style' => 'height:24em;',
        ));

        if (!$this->getEmailTemplate()->isPlain()) {
            $fieldset->addField('template_styles', 'textarea', array(
                'name'=>'template_styles',
                'label' => Mage::helper('adminhtml')->__('Template Styles'),
                'container_id' => 'field_template_styles'
            ));
        }

        if ($templateId) {
            $form->addValues($this->getEmailTemplate()->getData());
        }

        if ($values = Mage::getSingleton('adminhtml/session')->getData('email_template_form_data', true)) {
            $form->setValues($values);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return current email template model
     *
     * @return Mage_Core_Model_Email_Template
     */
    public function getEmailTemplate()
    {
        return Mage::registry('current_email_template');
    }

    /**
     * Retrieve variables to insert into email
     *
     * @return array
     */
    public function getVariables()
    {
        $variables = array();
        $variables[] = Mage::getModel('core/source_email_variables')
            ->toOptionArray(true);
        $customVariables = Mage::getModel('core/variable')
            ->getVariablesOptionArray(true);
        if ($customVariables) {
            $variables[] = $customVariables;
        }
        /* @var $template Mage_Core_Model_Email_Template */
        $template = Mage::registry('current_email_template');
        if ($template->getId() && $templateVariables = $template->getVariablesOptionArray(true)) {
            $variables[] = $templateVariables;
        }
        return $variables;
    }
}
