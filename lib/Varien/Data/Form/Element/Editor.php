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
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
            function openEditorPopup(url, name, specs, parent) {
                if ((typeof popups == "undefined") || popups[name] == undefined || popups[name].closed) {
                    if (typeof popups == "undefined") {
                        popups = new Array();
                    }
                    var opener = (parent != undefined ? parent : window);
                    popups[name] = opener.open(url, name, specs);
                } else {
                    popups[name].focus();
                }
            }

            function closeEditorPopup(name) {
                if ((typeof popups != "undefined") && popups[name] != undefined && !popups[name].closed) {
                    popups[name].close();
                }
            }
    		//]]>
            </script>';

        if($this->isEnabled())
        {
            // add Firebug notice translations
            $this->getConfig()->addData(array(
                'firebug_warning_title'  => $this->translate('Warning'),
                'firebug_warning_text'   => $this->translate('Firebug is known to make the WYSIWYG editor slow unless it is turned off or configured properly.'),
                'firebug_warning_anchor' => $this->translate('Hide'),
            ));

            $jsSetupObject = 'wysiwyg' . $this->getHtmlId();

            $html = $this->_getButtonsHtml()
                .'<textarea name="'.$this->getName().'" title="'.$this->getTitle().'" id="'.$this->getHtmlId().'" class="textarea '.$this->getClass().'" '.$this->serialize($this->getHtmlAttributes()).' >'.$this->getEscapedValue().'</textarea>

                '.$js.'

                <script type="text/javascript">
                //<![CDATA[

                function imagebrowser(fieldName, url, objectType, w) {
                    varienGlobalEvents.fireEvent("open_browser_callback", {win:w, type:objectType, field:fieldName});
                }

                '.$jsSetupObject.' = new tinyMceWysiwygSetup("'.$this->getHtmlId().'", '.Zend_Json::encode($this->getConfig()).');

                '.($this->isHidden() ? '' : 'Event.observe(window, "load", '.$jsSetupObject.'.setup.bind('.$jsSetupObject.'));').'

				Event.observe("toggle'.$this->getHtmlId().'", "click", '.$jsSetupObject.'.toggle.bind('.$jsSetupObject.'));
                varienGlobalEvents.attachEventHandler("formSubmit", '.$jsSetupObject.'.onFormValidation.bind('.$jsSetupObject.'));
                varienGlobalEvents.attachEventHandler("tinymceBeforeSetContent", '.$jsSetupObject.'.beforeSetContent.bind('.$jsSetupObject.'));
                varienGlobalEvents.attachEventHandler("tinymceSaveContent", '.$jsSetupObject.'.saveContent.bind('.$jsSetupObject.'));
                varienGlobalEvents.attachEventHandler("open_browser_callback", '.$jsSetupObject.'.openImagesBrowser.bind('.$jsSetupObject.'));

				//]]>
                </script>';

            $html = $this->_wrapIntoContainer($html);
            $html.= $this->getAfterElementHtml();
            return $html;
        }
        else
        {
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
            $buttonsHtml .= $this->_getPluginButtonsHtml(false) . $this->_getToggleButtonHtml();
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
        $winUrl = $this->getConfig('widget_window_no_wysiwyg_url');
        $buttonsHtml .= $this->_getButtonHtml(array(
            'title'     => $this->translate('Insert Widget...'),
            'onclick'   => "openEditorPopup('" . $winUrl . "', 'widget_window" . $this->getHtmlId() . "', 'width=1024,height=800,scrollbars=yes')",
            'class'     => 'add-widget plugin',
            'style'     => $visible ? '' : 'display:none',
        ));

        // Button to media images insertion window
        $winUrl = $this->getConfig('files_browser_window_url');
        $buttonsHtml .= $this->_getButtonHtml(array(
            'title'     => $this->translate('Insert Image...'),
            'onclick'   => "openEditorPopup('" . $winUrl . "', 'browser_window" . $this->getHtmlId() . "', 'width=1024,height=800')",
            'class'     => 'add-image plugin',
            'style'     => $visible ? '' : 'display:none',
        ));

        return $buttonsHtml;
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
        $html.= isset($data['title']) ? '<span>'.$data['title'].'</span>' : '';
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
        $html = '<div id="editor'.$this->getHtmlId().'"'.($this->getConfig('no_display') ? ' style="display:none;"' : '').'>'
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
        if ($this->getConfig('translator') instanceof Varien_Object) {
            return $this->getConfig('translator')->__($string);
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
