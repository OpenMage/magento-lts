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
 * @category    Varien
 * @package     Varien_Data
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form editor element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Editor extends Varien_Data_Form_Element_Textarea
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);

        if($this->isEnabled()) {
            $this->setType('wysiwyg');
            $this->setExtType('wysiwyg');
        } else {
            $this->setType('textarea');
            $this->setExtType('textarea');
        }
    }

    public function getElementHtml()
    {
        $js = '
            <script type="text/javascript">
            //<![CDATA[
                openEditorPopup = function(url, name, specs, parent) {
                    if ((typeof popups == "undefined") || popups[name] == undefined || popups[name].closed) {
                        if (typeof popups == "undefined") {
                            popups = new Array();
                        }
                        var opener = (parent != undefined ? parent : window);
                        popups[name] = opener.open(url, name, specs);
                    } else {
                        popups[name].focus();
                    }
                    return popups[name];
                }

                closeEditorPopup = function(name) {
                    if ((typeof popups != "undefined") && popups[name] != undefined && !popups[name].closed) {
                        popups[name].close();
                    }
                }
            //]]>
            </script>';

        if($this->isEnabled())
        {
            // add Firebug notice translations
            $warn = 'Firebug is known to make the WYSIWYG editor slow unless it is turned off or configured properly.';
            $this->getConfig()->addData(array(
                'firebug_warning_title'  => $this->translate('Warning'),
                'firebug_warning_text'   => $this->translate($warn),
                'firebug_warning_anchor' => $this->translate('Hide'),
            ));

            $translatedString = array(
                'Insert Image...' => $this->translate('Insert Image...'),
                'Insert Media...' => $this->translate('Insert Media...'),
                'Insert File...'  => $this->translate('Insert File...')
            );

            $jsSetupObject = 'wysiwyg' . $this->getHtmlId();

            $forceLoad = '';
            if (!$this->isHidden()) {
                if ($this->getForceLoad()) {
                    $forceLoad = $jsSetupObject . '.setup("exact");';
                } else {
                    $forceLoad = 'Event.observe(window, "load", '
                                . $jsSetupObject . '.setup.bind(' . $jsSetupObject . ', "exact"));';
                }
            }

            $html = $this->_getButtonsHtml()
                . '<textarea name="' . $this->getName() . '" title="' . $this->getTitle()
                . '" id="' . $this->getHtmlId() . '"'
                . ' class="textarea ' . $this->getClass() . '" '
                . $this->serialize($this->getHtmlAttributes()) . ' >' . $this->getEscapedValue() . '</textarea>'
                . $js . '
                <script type="text/javascript">
                //<![CDATA[
                    if ("undefined" != typeof(Translator)) {
                        Translator.add(' . Zend_Json::encode($translatedString) . ');
                    }'
                    . $jsSetupObject . ' = new tinyMceWysiwygSetup("' . $this->getHtmlId() . '", '
                    . Zend_Json::encode($this->getConfig()).');'
                    . $forceLoad.'
                    editorFormValidationHandler = ' . $jsSetupObject . '.onFormValidation.bind(' . $jsSetupObject . ');
                    Event.observe("toggle' . $this->getHtmlId() . '", "click", '
                        . $jsSetupObject . '.toggle.bind('.$jsSetupObject.'));
                    varienGlobalEvents.attachEventHandler("formSubmit", editorFormValidationHandler);
                    varienGlobalEvents.attachEventHandler("tinymceBeforeSetContent", '
                        . $jsSetupObject . '.beforeSetContent.bind(' . $jsSetupObject . '));
                    varienGlobalEvents.attachEventHandler("tinymceSaveContent", '
                        . $jsSetupObject . '.saveContent.bind(' . $jsSetupObject . '));
                    varienGlobalEvents.clearEventHandlers("open_browser_callback");
                    varienGlobalEvents.attachEventHandler("open_browser_callback", '
                        . $jsSetupObject . '.openFileBrowser.bind(' . $jsSetupObject . '));
                //]]>
                </script>';

            $html = $this->_wrapIntoContainer($html);
            $html .= $this->getAfterElementHtml();
            return $html;
        } else {
            // Display only buttons to additional features
            if ($this->getConfig('widget_window_url')) {
                $html = $this->_getButtonsHtml() . $js . parent::getElementHtml();
                $html = $this->_wrapIntoContainer($html);
                return $html;
            }
            return parent::getElementHtml();
        }
    }

    public function getTheme()
    {
        if(!$this->hasData('theme')) {
            return 'simple';
        }

        return $this->_getData('theme');
    }

    /**
     * Return Editor top Buttons HTML
     *
     * @return string
     */
    protected function _getButtonsHtml()
    {
        $buttonsHtml = '<div id="buttons'.$this->getHtmlId().'" class="buttons-set">';
        if ($this->isEnabled()) {
            $buttonsHtml .= $this->_getToggleButtonHtml() . $this->_getPluginButtonsHtml($this->isHidden());
        } else {
            $buttonsHtml .= $this->_getPluginButtonsHtml(true);
        }
        $buttonsHtml .= '</div>';

        return $buttonsHtml;
    }

    /**
     * Return HTML button to toggling WYSIWYG
     *
     * @return string
     */
    protected function _getToggleButtonHtml($visible = true)
    {
        $html = $this->_getButtonHtml(array(
            'title'     => $this->translate('Show / Hide Editor'),
            'class'     => 'show-hide',
            'style'     => $visible ? '' : 'display:none',
            'id'        => 'toggle'.$this->getHtmlId(),
        ));
        return $html;
    }

    /**
     * Prepare Html buttons for additional WYSIWYG features
     *
     * @param bool $visible Display button or not
     * @return void
     */
    protected function _getPluginButtonsHtml($visible = true)
    {
        $buttonsHtml = '';

        // Button to widget insertion window
        if ($this->getConfig('add_widgets')) {
            $buttonsHtml .= $this->_getButtonHtml(array(
                'title'     => $this->translate('Insert Widget...'),
                'onclick'   => "widgetTools.openDialog('" . $this->getConfig('widget_window_url') . "widget_target_id/"
                               . $this->getHtmlId() . "')",
                'class'     => 'add-widget plugin',
                'style'     => $visible ? '' : 'display:none',
            ));
        }

        // Button to media images insertion window
        if ($this->getConfig('add_images')) {
            $buttonsHtml .= $this->_getButtonHtml(array(
                'title'     => $this->translate('Insert Image...'),
                'onclick'   => "MediabrowserUtility.openDialog('" .
                               $this->getConfig('files_browser_window_url') .
                               "target_element_id/" . $this->getHtmlId() . "/" .
                                ((null !== $this->getConfig('store_id'))
                                    ? ('store/' . $this->getConfig('store_id') . '/')
                                    : '')
                               . "')",
            'class'     => 'add-image plugin',
            'style'     => $visible ? '' : 'display:none',
        ));
        }

        foreach ($this->getConfig('plugins') as $plugin) {
            if (isset($plugin['options']) && $this->_checkPluginButtonOptions($plugin['options'])) {
                $buttonOptions = $this->_prepareButtonOptions($plugin['options']);
                if (!$visible) {
                    $configStyle = '';
                    if (isset($buttonOptions['style'])) {
                        $configStyle = $buttonOptions['style'];
                    }
                    $buttonOptions = array_merge($buttonOptions, array('style' => 'display:none;' . $configStyle));
                }
                $buttonsHtml .= $this->_getButtonHtml($buttonOptions);
            }
        }

        return $buttonsHtml;
    }

    /**
     * Prepare button options array to create button html
     *
     * @param array $options
     * @return array
     */
    protected function _prepareButtonOptions($options)
    {
        $buttonOptions = array();
        $buttonOptions['class'] = 'plugin';
        foreach ($options as $name => $value) {
            $buttonOptions[$name] = $value;
        }
        $buttonOptions = $this->_prepareOptions($buttonOptions);
        return $buttonOptions;
    }

    /**
     * Check if plugin button options have required values
     *
     * @param array $pluginOptions
     * @return boolean
     */
    protected function _checkPluginButtonOptions($pluginOptions)
    {
        if (!isset($pluginOptions['title'])) {
            return false;
        }
        return true;
    }

    /**
     * Convert options by replacing template constructions ( like {{var_name}} )
     * with data from this element object
     *
     * @param array $options
     * @return array
     */
    protected function _prepareOptions($options)
    {
        $preparedOptions = array();
        foreach ($options as $name => $value) {
            if (is_array($value) && isset($value['search']) && isset($value['subject'])) {
                $subject = $value['subject'];
                foreach ($value['search'] as $part) {
                    $subject = str_replace('{{'.$part.'}}', $this->getDataUsingMethod($part), $subject);
                }
                $preparedOptions[$name] = $subject;
            } else {
                $preparedOptions[$name] = $value;
            }
        }
        return $preparedOptions;
    }

    /**
     * Return custom button HTML
     *
     * @param array $data Button params
     * @return string
     */
    protected function _getButtonHtml($data)
    {
        $html = '<button type="button"';
        $html.= ' class="scalable '.(isset($data['class']) ? $data['class'] : '').'"';
        $html.= isset($data['onclick']) ? ' onclick="'.$data['onclick'].'"' : '';
        $html.= isset($data['style']) ? ' style="'.$data['style'].'"' : '';
        $html.= isset($data['id']) ? ' id="'.$data['id'].'"' : '';
        $html.= '>';
        $html.= isset($data['title']) ? '<span><span><span>'.$data['title'].'</span></span></span>' : '';
        $html.= '</button>';

        return $html;
    }

    /**
     * Wraps Editor HTML into div if 'use_container' config option is set to true
     * If 'no_display' config option is set to true, the div will be invisible
     *
     * @param string $html HTML code to wrap
     * @return string
     */
    protected function _wrapIntoContainer($html)
    {
        if (!$this->getConfig('use_container')) {
            return $html;
        }

        $html = '<div id="editor'.$this->getHtmlId().'"'
              . ($this->getConfig('no_display') ? ' style="display:none;"' : '')
              . ($this->getConfig('container_class') ? ' class="' . $this->getConfig('container_class') . '"' : '')
              . '>'
              . $html
              . '</div>';

        return $html;
    }

    /**
     * Editor config retriever
     *
     * @param string $key Config var key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if ( !($this->_getData('config') instanceof Varien_Object) ) {
            $config = new Varien_Object();
            $this->setConfig($config);
        }
        if ($key !== null) {
            return $this->_getData('config')->getData($key);
        }
        return $this->_getData('config');
    }

    /**
     * Translate string using defined helper
     *
     * @param string $string String to be translated
     * @return string
     */
    public function translate($string)
    {
        $translator = $this->getConfig('translator');
        if (method_exists($translator, '__')) {
            $result = $translator->__($string);
            if (is_string($result)) {
                return $result;
            }
        }

        return $string;
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->hasData('wysiwyg')) {
            return $this->getWysiwyg();
        }
        return $this->getConfig('enabled');
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->getConfig('hidden');
    }
}
