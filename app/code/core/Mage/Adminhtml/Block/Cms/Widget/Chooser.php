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
 * WYSIWYG widget options form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Cms_Widget_Chooser extends Mage_Adminhtml_Block_Template
{
    /**
     * Chooser source URL getter
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->getData('source_url');
    }

    /**
     * Chooser form element getter
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->getData('element');
    }

    /**
     * Convert XML config to Object
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if ($this->getData('config') instanceof Varien_Object) {
            return $this->getData('config');
        }

        $configXml = $this->getData('config');
        $config = new Varien_Object();
        $this->setConfig($config);
        if (!($configXml instanceof Varien_Simplexml_Element)) {
            return $this->getData('config');
        }

        // define chooser label
        if ($configXml->label) {
            $config->setData('label', $this->getTranslationHelper()->__((string)$configXml->label));
        }

        // chooser control buttons
        $buttons = array(
            'open' => Mage::helper('cms')->__('Choose'),
            'close' => Mage::helper('cms')->__('Close')
        );
        if ($configXml->button && $configXml->button->hasChildren()) {
            foreach ($configXml->button->children() as $button) {
                $buttons[(string)$button->getName()] = $this->getTranslationHelper()->__((string)$button);
            }
        }
        $config->setButtons($buttons);

        return $this->getData('config');
    }

    /**
     * Helper getter for translations
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getTranslationHelper()
    {
        if ($this->getData('translation_helper') instanceof Mage_Core_Helper_Abstract) {
            return $this->getData('translation_helper');
        }
        return $this->helper('cms');
    }

    /**
     * Unique identifier for block that uses Chooser
     *
     * @return string
     */
    public function getUniqId()
    {
        return $this->getData('uniq_id');
    }

    /**
     * Form element fieldset id getter for working with form in chooser
     *
     * @return string
     */
    public function getFieldsetId()
    {
        return $this->getData('fieldset_id');
    }

    /**
     * Flag to indicate include hidden field before chooser or not
     *
     * @return bool
     */
    public function getHiddenEnabled()
    {
        return $this->hasData('hidden_enabled') ? (bool)$this->getData('hidden_enabled') : true;
    }

    /**
     * Return chooser HTML and init scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        $element = $this->getElement();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $element->getForm()->getElement($this->getFieldsetId());

        $chooserId = $this->getUniqId();

        $hiddenHtml = '';
        if ($this->getHiddenEnabled()) {
            $hidden = new Varien_Data_Form_Element_Hidden(array(
                'name'      => $element->getName(),
                'required'  => (bool)$element->required,
                'value'     => $element->getValue(),
                'class'     => $element->getClass(),
            ));
            $hidden->setId($chooserId . 'value');
            $hidden->setForm($element->getForm());
            $hiddenHtml = $hidden->getElementHtml();

            // Unset element value in favour of hidden field
            $element->setValue("");
        }

        $config = $this->getConfig();

        $chooser = $fieldset->addField('chooser' . $element->getId(), 'label', array(
            'label' => $config->getLabel() ? $config->getLabel() : ''
        ));

        $buttons = $config->getButtons();
        $chooseButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setId($chooserId . 'control')
            ->setClass('widget-option-chooser')
            ->setLabel($buttons['open'])
            ->setOnclick($chooserId.'.choose()');

        $configJson = Mage::helper('core')->jsonEncode($config->getData());
        $html = '
            <script type="text/javascript">
                '.$chooserId.' = new WysiwygWidget.chooser("'.$chooserId.'", "'.$this->getSourceUrl().'", '.$configJson.');
            </script>
            <label class="widget-option-label" id="'.$chooserId . 'label">'.($this->getLabel() ? $this->getLabel() : Mage::helper('cms')->__('Not Selected')).'</label>
        ';

        $chooser->setData('after_element_html', $hiddenHtml . $chooseButton->toHtml());
        return $html;
    }


}
