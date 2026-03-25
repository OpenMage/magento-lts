<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Variable Wysiwyg Plugin Config
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Variable_Config
{
    /**
     * Prepare variable wysiwyg config
     *
     * @param  Varien_Object $config
     * @return array
     */
    public function getWysiwygPluginSettings($config)
    {
        $variableConfig = [];
        $onclickParts = [
            'search' => ['html_id'],
            'subject' => "OpenmagevariablePlugin.loadChooser('" . $this->getVariablesWysiwygActionUrl() . "', '{{html_id}}');",
        ];
        $variableWysiwygPlugin = [['name' => 'openmagevariable',
            'src' => $this->getWysiwygJsPluginSrc(),
            'options' => [
                'title' => Mage::helper('adminhtml')->__('Insert Variable...'),
                'url' => $this->getVariablesWysiwygActionUrl(),
                'onclick' => $onclickParts,
                'class'   => 'add-variable plugin',
            ]]];
        $configPlugins = $config->getData('plugins');
        $variableConfig['plugins'] = array_merge($configPlugins, $variableWysiwygPlugin);
        return $variableConfig;
    }

    /**
     * Return url to wysiwyg plugin
     *
     * @return string
     */
    public function getWysiwygJsPluginSrc()
    {
        return Mage::getBaseUrl('js') . 'mage/adminhtml/wysiwyg/tinymce/plugins/openmagevariable.js';
    }

    /**
     * Return url of action to get variables
     *
     * @return string
     */
    public function getVariablesWysiwygActionUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/system_variable/wysiwygPlugin');
    }
}
