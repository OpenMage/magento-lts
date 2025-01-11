<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WYSIWYG widget options form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method $this setConfig(Varien_Object $value)
 * @method $this setElement(Varien_Data_Form_Element_Abstract $value)
 * @method $this setFieldsetId(string $value)
 * @method string getLabel()
 * @method $this setTranslationHelper(Mage_Core_Helper_Abstract $value)
 * @method $this setSourceUrl(string $value)
 * @method $this setUniqId(string $value)
 */
class Mage_Widget_Block_Adminhtml_Widget_Chooser extends Mage_Adminhtml_Block_Template
{
    /**
     * Chooser source URL getter
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->_getData('source_url');
    }

    /**
     * Chooser form element getter
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_getData('element');
    }

    /**
     * Convert Array config to Object
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if ($this->_getData('config') instanceof Varien_Object) {
            return $this->_getData('config');
        }

        $configArray = $this->_getData('config');
        $config = new Varien_Object();
        $this->setConfig($config);
        if (!is_array($configArray)) {
            return $this->_getData('config');
        }

        // define chooser label
        if (isset($configArray['label'])) {
            $config->setData('label', $this->getTranslationHelper()->__($configArray['label']));
        }

        // chooser control buttons
        $buttons = [
            'open'  => Mage::helper('widget')->__('Choose...'),
            'close' => Mage::helper('widget')->__('Close'),
        ];
        if (isset($configArray['button']) && is_array($configArray['button'])) {
            foreach ($configArray['button'] as $id => $label) {
                $buttons[$id] = $this->getTranslationHelper()->__($label);
            }
        }
        $config->setButtons($buttons);

        return $this->_getData('config');
    }

    /**
     * Helper getter for translations
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getTranslationHelper()
    {
        if ($this->_getData('translation_helper') instanceof Mage_Core_Helper_Abstract) {
            return $this->_getData('translation_helper');
        }
        return $this->helper('widget');
    }

    /**
     * Unique identifier for block that uses Chooser
     *
     * @return string
     */
    public function getUniqId()
    {
        return $this->_getData('uniq_id');
    }

    /**
     * Form element fieldset id getter for working with form in chooser
     *
     * @return string
     */
    public function getFieldsetId()
    {
        return $this->_getData('fieldset_id');
    }

    /**
     * Flag to indicate include hidden field before chooser or not
     *
     * @return bool
     */
    public function getHiddenEnabled()
    {
        return $this->hasData('hidden_enabled') ? (bool) $this->_getData('hidden_enabled') : true;
    }

    /**
     * Return chooser HTML and init scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        $element   = $this->getElement();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset  = $element->getForm()->getElement($this->getFieldsetId());
        $chooserId = $this->getUniqId();
        $config    = $this->getConfig();

        // add chooser element to fieldset
        $chooser = $fieldset->addField('chooser' . $element->getId(), 'note', [
            'label'       => $config->getLabel() ? $config->getLabel() : '',
            'value_class' => 'value2',
        ]);
        $hiddenHtml = '';
        if ($this->getHiddenEnabled()) {
            $hidden = new Varien_Data_Form_Element_Hidden($element->getData());
            $hidden->setId("{$chooserId}value")->setForm($element->getForm());
            if ($element->getRequired()) {
                $hidden->addClass('required-entry');
            }
            $hiddenHtml = $hidden->getElementHtml();
            $element->setValue('');
        }

        $buttons = $config->getButtons();
        $chooseButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setId($chooserId . 'control')
            ->setClass('btn-chooser')
            ->setLabel($buttons['open'])
            ->setOnclick($chooserId . '.choose()')
            ->setDisabled($element->getReadonly());
        $chooser->setData('after_element_html', $hiddenHtml . $chooseButton->toHtml());

        // render label and chooser scripts
        $configJson = Mage::helper('core')->jsonEncode($config->getData());
        return '
            <label class="widget-option-label" id="' . $chooserId . 'label">'
            . $this->quoteEscape($this->getLabel() ? $this->getLabel() : Mage::helper('widget')->__('Not Selected'))
            . '</label>
            <div id="' . $chooserId . 'advice-container" class="hidden"></div>
            <script type="text/javascript">//<![CDATA[
                (function() {
                    var instantiateChooser = function() {
                        window.' . $chooserId . ' = new WysiwygWidget.chooser(
                            "' . $chooserId . '",
                            "' . $this->getSourceUrl() . '",
                            ' . $configJson . '
                        );
                        if ($("' . $chooserId . 'value")) {
                            $("' . $chooserId . 'value").advaiceContainer = "' . $chooserId . 'advice-container";
                        }
                    }

                    if (document.loaded) { //allow load over ajax
                        instantiateChooser();
                    } else {
                        document.observe("dom:loaded", instantiateChooser);
                    }
                })();
            //]]></script>
        ';
    }
}
