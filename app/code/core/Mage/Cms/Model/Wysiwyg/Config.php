<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

use Composer\InstalledVersions;

/**
 * Wysiwyg Config for Editor HTML Element
 *
 * @package    Mage_Cms
 *
 * @method string getStoreId()
 * @method $this setStoreId(string $value)
 */
class Mage_Cms_Model_Wysiwyg_Config extends Varien_Object
{
    /**
     * Wysiwyg store config path
     */
    public const WYSIWYG_CONFIG_ENABLED = 'cms/wysiwyg/enabled';

    /**
     * Wysiwyg behaviour: enabled
     */
    public const WYSIWYG_ENABLED = 'enabled';

    /**
     * Wysiwyg behaviour: hidden
     */
    public const WYSIWYG_HIDDEN = 'hidden';

    /**
     * Wysiwyg behaviour: disabled
     */
    public const WYSIWYG_DISABLED = 'disabled';

    /**
     * constant for image directory
     */
    public const IMAGE_DIRECTORY = 'wysiwyg';

    /**
     * Path to skin image placeholder file
     */
    public const WYSIWYG_SKIN_IMAGE_PLACEHOLDER_FILE = 'images/wysiwyg/skin_image.png';

    public const WYSIWYG_LOCALES = ['ar','az','be','bg_BG','bn_BD','ca','cs','cy','da','de','dv','el','eo','es','es_MX','et','eu','fa','fi','fr_FR','ga','gl','he_IL','hi','hr','hu_HU','hy','id','is_IS','it','ja','ka_GE','kab','kk','ko_KR','ku','lt','lv','nb_NO','ne','nl','nl_BE','oc','pl','pt_BR','ro','ru','sk','sl_SI','sq','sr','sv_SE','ta','tg','th_TH','tr','ug','uk','uz','vi','zh-Hans','zh-Hant','zh_HK','zh_MO','zh_SG'];

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
     *
     * @param array $data constructor params to override default config values
     * @return Varien_Object
     */
    public function getConfig($data = [])
    {
        $config = new Varien_Object();

        $locale = Mage::app()->getLocale()->getLocaleCode();
        $lang = in_array($locale, self::WYSIWYG_LOCALES) ? $locale : substr($locale, 0, 2);
        $config->setData([
            'enabled'                       => $this->isEnabled(),
            'hidden'                        => $this->isHidden(),
            'skin'                          => Mage::getStoreConfig('cms/wysiwyg/skin'),
            'use_container'                 => false,
            'add_variables'                 => Mage::getSingleton('admin/session')->isAllowed('system/variable'),
            'add_widgets'                   => Mage::getSingleton('admin/session')->isAllowed('cms/widget_instance'),
            'no_display'                    => false,
            'translator'                    => Mage::helper('cms'),
            'encode_directives'             => true,
            'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
            'width'                         => '100%',
            'plugins'                       => [],
            'lang'                          => $lang,
        ]);
        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if (Mage::getSingleton('admin/session')->isAllowed('cms/media_gallery')) {
            $config->addData([
                'add_images'               => true,
                'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg_images/index'),
                'files_browser_window_width'
                    => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
                'files_browser_window_height'
                    => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height'),
            ]);
        }

        if (is_array($data)) {
            $config->addData($data);
        }

        Mage::dispatchEvent('cms_wysiwyg_config_prepare', ['config' => $config]);

        return $config;
    }

    /**
     * Return the URL for skin image placeholder
     *
     * @return string
     */
    public function getSkinImagePlaceholderUrl()
    {
        return Mage::getDesign()->getSkinUrl(self::WYSIWYG_SKIN_IMAGE_PLACEHOLDER_FILE);
    }

    /**
     * Return the path to the skin image placeholder
     *
     * @return string
     */
    public function getSkinImagePlaceholderPath()
    {
        return Mage::getDesign()->getFilename(self::WYSIWYG_SKIN_IMAGE_PLACEHOLDER_FILE, ['_type' => 'skin']);
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (!InstalledVersions::isInstalled('tinymce/tinymce')) {
            return false;
        }

        $storeId = $this->getStoreId();
        if (!is_null($storeId)) {
            $wysiwygState = Mage::getStoreConfig(self::WYSIWYG_CONFIG_ENABLED, $storeId);
        } else {
            $wysiwygState = Mage::getStoreConfig(self::WYSIWYG_CONFIG_ENABLED);
        }
        return in_array($wysiwygState, [self::WYSIWYG_ENABLED, self::WYSIWYG_HIDDEN]);
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return Mage::getStoreConfig(self::WYSIWYG_CONFIG_ENABLED) == self::WYSIWYG_HIDDEN;
    }
}
