<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widgets Insertion Plugin Config for Editor HTML Element
 *
 * @package    Mage_Widget
 */
class Mage_Widget_Model_Widget_Config extends Varien_Object
{
    /**
     * Return config settings for widgets insertion plugin based on editor element config
     *
     * @param Varien_Object $config
     * @return array
     */
    public function getPluginSettings($config)
    {
        return [
            'widget_plugin_src'   => Mage::getBaseUrl('js') . 'mage/adminhtml/wysiwyg/tinymce/plugins/openmagewidget.js',
            'widget_images_url'   => $this->getPlaceholderImagesBaseUrl(),
            'widget_placeholders' => $this->getAvailablePlaceholderFilenames(),
            'widget_window_url'   => $this->getWidgetWindowUrl($config),
        ];
    }

    /**
     * Return Widget placeholders images URL
     *
     * @return string
     */
    public function getPlaceholderImagesBaseUrl()
    {
        return Mage::getDesign()->getSkinUrl('images/widget/');
    }

    /**
     * Return Widget placeholders images dir
     *
     * @return string
     */
    public function getPlaceholderImagesBaseDir()
    {
        return Mage::getDesign()->getSkinBaseDir() . DS . 'images' . DS . 'widget';
    }

    /**
     * Return list of existing widget image placeholders
     *
     * @return array
     */
    public function getAvailablePlaceholderFilenames()
    {
        $result = [];
        $targetDir = $this->getPlaceholderImagesBaseDir();
        if (is_dir($targetDir) && is_readable($targetDir)) {
            $collection = new Varien_Data_Collection_Filesystem();
            $collection->addTargetDir($targetDir)
                ->setCollectDirs(false)
                ->setCollectFiles(true)
                ->setCollectRecursively(false);
            foreach ($collection as $file) {
                $result[] = $file->getBasename();
            }
        }

        return $result;
    }

    /**
     * Return Widgets Insertion Plugin Window URL
     *
     * @param Varien_Object $config Editor element config
     * @return string
     */
    public function getWidgetWindowUrl($config)
    {
        $params = [];

        $skipped = is_array($config->getData('skip_widgets')) ? $config->getData('skip_widgets') : [];
        if ($config->hasData('widget_filters')) {
            $all = Mage::getModel('widget/widget')->getWidgetsXml();
            $filtered = Mage::getModel('widget/widget')->getWidgetsXml($config->getData('widget_filters'));
            $reflection = new ReflectionObject($filtered);
            foreach ($all as $code => $widget) {
                if (!$reflection->hasProperty($code)) {
                    $skipped[] = $widget->getAttribute('type');
                }
            }
        }

        if (count($skipped) > 0) {
            $params['skip_widgets'] = $this->encodeWidgetsToQuery($skipped);
        }
        return Mage::getSingleton('adminhtml/url')->getUrl('*/widget/index', $params);
    }

    /**
     * Encode list of widget types into query param
     *
     * @param array $widgets List of widgets
     * @return string Query param value
     */
    public function encodeWidgetsToQuery($widgets)
    {
        $widgets = is_array($widgets) ? $widgets : [$widgets];
        $param = implode(',', $widgets);
        return Mage::helper('core')->urlEncode($param);
    }

    /**
     * Decode URL query param and return list of widgets
     *
     * @param string $queryParam Query param value to decode
     * @return array Array of widget types
     */
    public function decodeWidgetsFromQuery($queryParam)
    {
        $param = Mage::helper('core')->urlDecode($queryParam);
        return preg_split('/\s*\,\s*/', $param, 0, PREG_SPLIT_NO_EMPTY);
    }
}
