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
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wysiwyg Config for Editor HTML Element
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Wysiwyg_Config extends Varien_Object
{
    /**
     * Wysiwyg behaviour
     */
    const WYSIWYG_ENABLED = 'enabled';
    const WYSIWYG_HIDDEN = 'hidden';
    const WYSIWYG_DISABLED = 'disabled';

    /**
     * Return Wysiwyg config as Varien_Object
     *
     * Config options description:
     *
     * enabled:                 Enabled Visual Editor or not
     * hidden:                  Show Visual Editor on page load or not
     * use_container:           Wrap Editor contents into div or not
     * no_display:              Hide Editor container or not (related to use_container)
     * translator:              Helper to translate phrases in lib
     * files_browser_*:         Files Browser (media, images) settings
     * encode_directives:       Encode template directives with JS or not
     * widget_window_*:         Widget plugin insertion settings
     * widget_image_url:        Default image placeholder fot widget insertion
     *
     * @param $data Varien_Object constructor params to override default config values
     * @return Varien_Object
     */
    public function getConfig($data = array())
    {
        $config = new Varien_Object();
        $config->setData(array(
            'enabled'                       => $this->isEnabled(),
            'hidden'                        => $this->isHidden(),
            'use_container'                 => false,
            'no_display'                    => false,
            'translator'                    => Mage::helper('cms'),
            'files_browser_window_url'      => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg_images/index'),
            'files_browser_window_width'    => Mage::getStoreConfig('cms/wysiwyg/browser_window_width'),
            'files_browser_window_height'   => Mage::getStoreConfig('cms/wysiwyg/browser_window_height'),
            'encode_directives'             => true,
            'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
            'widget_plugin_src'             => Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/plugins/magentowidget/editor_plugin.js',
            'widget_images_url'             => Mage::getSingleton('cms/widget')->getPlaceholderImagesBaseUrl(),
            'widget_placeholders'           => $this->getAvailablePlaceholderFilenames(),
            'popup_css'                     => Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css',
            'content_css'                   => Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css',
        ));

        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if (is_array($data)) {
            $config->addData($data);
        }

        if (!$config->hasData('widget_window_url')) {
            $config->setData('widget_window_url', $this->getWidgetWindowUrl($config));
        }
        if (!$config->hasData('widget_window_no_wysiwyg_url')) {
            $config->setData('widget_window_no_wysiwyg_url', $this->getWidgetWindowUrl($config, false));
        }

        return $config;
    }

    /**
     * Return Widgets Insertion Plugin Window URL
     *
     * @param array $params URL params
     * @return string
     */
    public function getWidgetWindowUrl($config, $wysiwygMode = true)
    {
        $params = $wysiwygMode ? array() : array('no_wysiwyg' => true);

        if ($config->getData('skip_context_widgets')) {
            $params['skip_context_widgets'] = 1;
        }

        if ($config->hasData('skip_widgets')) {
            $skipped = $config->getData('skip_widgets');
            $skipped = is_array($skipped) ? $skipped : array($skipped);
            $skipped = implode(',', $skipped);
            $params['skip_widgets'] = Mage::helper('core')->urlEncode($skipped);
        }

        return Mage::getSingleton('adminhtml/url')->getUrl('*/cms_widget/index', $params);
    }

    /**
     * Return list of existing widget image placeholders
     *
     * @return array
     */
    public function getAvailablePlaceholderFilenames()
    {
        $collection = new Varien_Data_Collection_Filesystem();
        $collection->addTargetDir(Mage::getSingleton('cms/widget')->getPlaceholderImagesBaseDir())
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false);
        $result = array();
        foreach ($collection as $file) {
            $result[] = $file->getBasename();
        }
        return $result;
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return in_array(Mage::getStoreConfig('cms/wysiwyg/enabled'), array(self::WYSIWYG_ENABLED, self::WYSIWYG_HIDDEN));
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return Mage::getStoreConfig('cms/wysiwyg/enabled') == self::WYSIWYG_HIDDEN;
    }
}
